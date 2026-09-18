<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Expense;
use App\Models\Item;
use App\Models\Media;
use App\Models\Payment;
use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Dashboard principal del CMS con indicadores en tiempo real (Requerimiento 3.1).
     */
    public function index(): View
    {
        $today = now()->toDateString();

        // 1. KPI Disponibilidad de artículos hoy (fijo independiente de fecha navegada)
        $totalItemsCount = Item::where('status', 'disponible')->sum('total_quantity');
        $bookedTodayCount = DB::table('reservation_items')
            ->join('reservations', 'reservation_items.reservation_id', '=', 'reservations.id')
            ->whereIn('reservations.status', ['confirmada', 'en_curso'])
            ->where('reservations.start_date', '<=', $today)
            ->where('reservations.end_date', '>=', $today)
            ->sum('reservation_items.quantity');

        $availableTodayCount = max(0, $totalItemsCount - $bookedTodayCount);
        $availabilityRate = $totalItemsCount > 0 ? round(($availableTodayCount / $totalItemsCount) * 100) : 100;

        // 2. KPI Reservas activas y próximas a iniciar/vencer en las próximas 72 horas
        $activeReservationsCount = Reservation::whereIn('status', ['confirmada', 'en_curso'])->count();
        $upcomingReservations = Reservation::with('client')
            ->whereIn('status', ['confirmada'])
            ->whereBetween('start_date', [$today, Carbon::now()->addDays(3)->toDateString()])
            ->orderBy('start_date')
            ->get();

        // 3. KPI Ingresos del mes y comparativo
        $currentMonthIncome = Payment::whereMonth('payment_date', now()->month)
            ->whereYear('payment_date', now()->year)
            ->sum('amount');

        $lastMonthIncome = Payment::whereMonth('payment_date', now()->subMonth()->month)
            ->whereYear('payment_date', now()->subMonth()->year)
            ->sum('amount');

        $incomeDifference = $currentMonthIncome - $lastMonthIncome;

        // 4. KPI Cuentas por cobrar pendientes
        $pendingReceivableReservations = Reservation::whereNotIn('status', ['cancelada'])
            ->whereRaw('total_amount > paid_amount')
            ->get();

        $totalReceivable = $pendingReceivableReservations->sum(fn($r) => $r->pending_balance);

        // 5. Alertas críticas
        // Devoluciones atrasadas (fecha de fin vencida sin registrar devolución)
        $overdueReturns = Reservation::with('client')
            ->whereIn('status', ['confirmada', 'en_curso'])
            ->whereDate('end_date', '<', $today)
            ->orderBy('end_date')
            ->get();

        // Equipos en mantenimiento
        $itemsInMaintenance = Item::where('status', 'en_mantenimiento')->count();

        // 6. Gráficas de ocupación / distribución por categoría de artículo
        $categoriesDistribution = Item::select('category', DB::raw('SUM(total_quantity) as total'))
            ->groupBy('category')
            ->pluck('total', 'category')
            ->toArray();

        // 7. Multimedia y métricas de soporte
        $mediaCount = Media::count();
        $totalBytes = (int) Media::sum('size');
        $storageUsed = $totalBytes >= 1048576
            ? number_format($totalBytes / 1048576, 2) . ' MB'
            : number_format($totalBytes / 1024, 1) . ' KB';

        // 8. Reservas recientes
        $recentReservations = Reservation::with(['client', 'items.item'])->latest()->take(6)->get();

        return view('dashboard.dashboard', compact(
            'totalItemsCount',
            'availableTodayCount',
            'bookedTodayCount',
            'availabilityRate',
            'activeReservationsCount',
            'upcomingReservations',
            'currentMonthIncome',
            'lastMonthIncome',
            'incomeDifference',
            'totalReceivable',
            'pendingReceivableReservations',
            'overdueReturns',
            'itemsInMaintenance',
            'categoriesDistribution',
            'mediaCount',
            'storageUsed',
            'recentReservations'
        ));
    }
}
