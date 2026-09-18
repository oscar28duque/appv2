<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\StaffMember;
use App\Models\WorkLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StaffController extends Controller
{
    /**
     * Gestión de personal operativo y bitácora de turnos.
     */
    public function index(): View
    {
        $staffMembers = StaffMember::withCount('workLogs')->orderBy('name')->get();
        $recentWorkLogs = WorkLog::with(['staffMember', 'reservation'])->latest('work_date')->paginate(15);
        $activeReservations = Reservation::whereIn('status', ['confirmada', 'en_curso'])->get();

        $totalPayrollPending = WorkLog::where('paid', false)->sum('amount_earned');

        return view('admin.staff.index', compact('staffMembers', 'recentWorkLogs', 'activeReservations', 'totalPayrollPending'));
    }

    /**
     * Registra un nuevo miembro del equipo operativo.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'role' => ['required', 'string', 'max:100'],
            'document' => ['nullable', 'string', 'max:50'],
            'phone' => ['nullable', 'string', 'max:50'],
            'daily_rate' => ['required', 'numeric', 'min:0'],
            'payment_frequency' => ['required', 'in:diario,semanal,quincenal,mensual'],
        ]);

        StaffMember::create($validated);

        return back()->with('success', 'Personal operativo agregado al sistema.');
    }

    /**
     * Registra una jornada de trabajo en la bitácora.
     */
    public function storeWorkLog(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'staff_member_id' => ['required', 'exists:staff_members,id'],
            'reservation_id' => ['nullable', 'exists:reservations,id'],
            'work_date' => ['required', 'date'],
            'hours_or_days' => ['required', 'numeric', 'min:0.5'],
            'notes' => ['nullable', 'string'],
        ]);

        $staff = StaffMember::findOrFail($validated['staff_member_id']);
        $amountEarned = (float) $staff->daily_rate * (float) $validated['hours_or_days'];

        WorkLog::create([
            'staff_member_id' => $staff->id,
            'reservation_id' => $validated['reservation_id'] ?? null,
            'work_date' => $validated['work_date'],
            'hours_or_days' => $validated['hours_or_days'],
            'amount_earned' => $amountEarned,
            'paid' => false,
            'notes' => $validated['notes'] ?? null,
        ]);

        return back()->with('success', 'Turno registrado en la bitácora.');
    }

    /**
     * Asienta el pago de un turno.
     */
    public function togglePayWorkLog(WorkLog $workLog): RedirectResponse
    {
        $workLog->paid = !$workLog->paid;
        $workLog->save();

        $text = $workLog->paid ? 'marcado como pagado' : 'marcado como pendiente';

        return back()->with('success', "Turno {$text}.");
    }
}
