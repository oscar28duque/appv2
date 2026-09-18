<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\Media;
use App\Models\Payment;
use App\Models\Reservation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class FinanceController extends Controller
{
    /**
     * Panel de Cuentas por Cobrar y Gastos Operativos.
     */
    public function index(Request $request): View
    {
        // 1. Cuentas por cobrar pendientes
        $pendingReservations = Reservation::with(['client', 'payments'])
            ->whereNotIn('status', ['cancelada'])
            ->whereRaw('total_amount > paid_amount')
            ->orderBy('end_date')
            ->get();

        $totalReceivable = $pendingReservations->sum(fn($r) => $r->pending_balance);

        // 2. Gastos operativos
        $expenses = Expense::with('receiptMedia')->latest('expense_date')->paginate(15);
        $totalExpensesMonth = Expense::whereMonth('expense_date', now()->month)
            ->whereYear('expense_date', now()->year)
            ->sum('amount');

        // 3. Ingresos recaudados en el mes
        $totalIncomeMonth = Payment::whereMonth('payment_date', now()->month)
            ->whereYear('payment_date', now()->year)
            ->sum('amount');

        $categories = Expense::CATEGORIES;

        return view('admin.finances.index', compact(
            'pendingReservations',
            'totalReceivable',
            'expenses',
            'totalExpensesMonth',
            'totalIncomeMonth',
            'categories'
        ));
    }

    /**
     * Registra un nuevo gasto operativo con soporte para comprobante en Storage.
     */
    public function storeExpense(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'category' => ['required', 'string', Rule::in(array_keys(Expense::CATEGORIES))],
            'description' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'expense_date' => ['required', 'date'],
            'receipt' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,pdf', 'max:5120'],
        ]);

        $receiptMediaId = null;

        if ($request->hasFile('receipt')) {
            $file = $request->file('receipt');
            $path = $file->store('media/comprobantes', 'public');

            $media = Media::create([
                'name' => 'Comprobante ' . $validated['description'],
                'path' => $path,
                'mime_type' => $file->getMimeType(),
                'size' => $file->getSize(),
                'active' => true,
            ]);

            $receiptMediaId = $media->id;
        }

        Expense::create([
            'category' => $validated['category'],
            'description' => $validated['description'],
            'amount' => $validated['amount'],
            'expense_date' => $validated['expense_date'],
            'receipt_media_id' => $receiptMediaId,
        ]);

        return back()->with('success', 'Gasto operativo registrado exitosamente.');
    }

    /**
     * Elimina un registro de gasto.
     */
    public function destroyExpense(Expense $expense): RedirectResponse
    {
        $expense->delete();

        return back()->with('success', 'Gasto eliminado.');
    }
}
