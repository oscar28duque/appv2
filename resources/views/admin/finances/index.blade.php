@extends('layouts.admin')

@section('title', 'Cuentas & Finanzas')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h2 class="fw-bold text-dark mb-1">
            <i class="bi bi-cash-coin text-primary me-2"></i>Gestión Financiera & Cuentas por Cobrar
        </h2>
        <p class="text-muted mb-0">Control de cobros pendientes de clientes, registro de abonos y egresos operativos.</p>
    </div>
    <button type="button" class="btn btn-primary d-flex align-items-center gap-2 shadow-sm" data-bs-toggle="modal" data-bs-target="#newExpenseModal">
        <i class="bi bi-receipt"></i> Registrar Gasto Operativo
    </button>
</div>

<!-- Métricas Financieras -->
<div class="row g-3 mb-4">
    <!-- Cuentas por Cobrar -->
    <div class="col-sm-6 col-xl-3">
        <div class="card stat-card bg-white p-3 h-100">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-muted fw-bold small">TOTAL POR COBRAR</span>
                <span class="p-2 rounded-3 bg-warning bg-opacity-10 text-warning">
                    <i class="bi bi-hourglass-split fs-5"></i>
                </span>
            </div>
            <h3 class="fw-bold text-danger mb-0">${{ number_format($totalReceivable, 2) }}</h3>
            <small class="text-muted mt-1">{{ $pendingReservations->count() }} contratos con saldo</small>
        </div>
    </div>

    <!-- Ingresos del Mes -->
    <div class="col-sm-6 col-xl-3">
        <div class="card stat-card bg-white p-3 h-100">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-muted fw-bold small">RECAUDO ESTE MES</span>
                <span class="p-2 rounded-3 bg-success bg-opacity-10 text-success">
                    <i class="bi bi-cash-stack fs-5"></i>
                </span>
            </div>
            <h3 class="fw-bold text-success mb-0">${{ number_format($totalIncomeMonth, 2) }}</h3>
            <small class="text-muted mt-1">Abonos y pagos de alquileres</small>
        </div>
    </div>

    <!-- Gastos del Mes -->
    <div class="col-sm-6 col-xl-3">
        <div class="card stat-card bg-white p-3 h-100">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-muted fw-bold small">GASTOS ESTE MES</span>
                <span class="p-2 rounded-3 bg-danger bg-opacity-10 text-danger">
                    <i class="bi bi-cart-x fs-5"></i>
                </span>
            </div>
            <h3 class="fw-bold text-danger mb-0">${{ number_format($totalExpensesMonth, 2) }}</h3>
            <small class="text-muted mt-1">Fletes, combustible, insumos</small>
        </div>
    </div>

    <!-- Balance Neto -->
    <div class="col-sm-6 col-xl-3">
        <div class="card stat-card bg-white p-3 h-100">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-muted fw-bold small">BALANCE NETO DEL MES</span>
                <span class="p-2 rounded-3 bg-info bg-opacity-10 text-info">
                    <i class="bi bi-graph-up fs-5"></i>
                </span>
            </div>
            @php $netBalance = $totalIncomeMonth - $totalExpensesMonth; @endphp
            <h3 class="fw-bold {{ $netBalance >= 0 ? 'text-primary' : 'text-danger' }} mb-0">
                ${{ number_format($netBalance, 2) }}
            </h3>
            <small class="text-muted mt-1">Recaudo menos egresos</small>
        </div>
    </div>
</div>

<!-- Pestañas Cuentas por Cobrar vs Gastos -->
<ul class="nav nav-tabs mb-4" id="financeTabs" role="tablist">
    <li class="nav-item">
        <button class="nav-link active fw-bold" id="receivable-tab" data-bs-toggle="tab" data-bs-target="#receivable" type="button">
            <i class="bi bi-wallet2 me-1"></i> Cuentas por Cobrar Pendientes ({{ $pendingReservations->count() }})
        </button>
    </li>
    <li class="nav-item">
        <button class="nav-link fw-bold" id="expenses-tab" data-bs-toggle="tab" data-bs-target="#expenses" type="button">
            <i class="bi bi-receipt me-1"></i> Egresos & Gastos Operativos
        </button>
    </li>
</ul>

<div class="tab-content" id="financeTabContent">
    <!-- Pestaña 1: Cuentas por Cobrar -->
    <div class="tab-pane fade show active" id="receivable" role="tabpanel">
        @if($pendingReservations->isEmpty())
            <div class="card border-0 shadow-sm p-5 text-center bg-white rounded-3">
                <div class="text-success mb-3"><i class="bi bi-check-circle-fill fs-1"></i></div>
                <h5 class="fw-bold text-success">¡Excelente! No hay cuentas por cobrar pendientes</h5>
                <p class="text-muted mb-0">Todas las reservas confirmadas y en curso se encuentran con pago al día.</p>
            </div>
        @else
            <div class="card border-0 shadow-sm rounded-3 overflow-hidden bg-white">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light small">
                            <tr>
                                <th>Reserva</th>
                                <th>Cliente</th>
                                <th>Fecha Fin Servicio</th>
                                <th class="text-end">Total Factura</th>
                                <th class="text-end">Abonado</th>
                                <th class="text-end">Saldo Pendiente</th>
                                <th class="text-end" style="width: 140px;">Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pendingReservations as $rsv)
                                <tr>
                                    <td>
                                        <a href="{{ route('admin.reservations.show', $rsv) }}" class="fw-bold text-decoration-none">
                                            {{ $rsv->code }}
                                        </a>
                                        @if($rsv->is_overdue)
                                            <span class="badge bg-danger ms-1" style="font-size: 10px;">Vencida</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark">{{ $rsv->client->name }}</div>
                                        <small class="text-muted"><i class="bi bi-telephone me-1"></i>{{ $rsv->client->phone ?? 'Sin teléfono' }}</small>
                                    </td>
                                    <td class="small">
                                        {{ $rsv->end_date->format('d/m/Y') }}
                                        @if($rsv->end_date->isPast())
                                            <div class="text-danger small">Hace {{ $rsv->end_date->diffInDays(now()) }} días</div>
                                        @endif
                                    </td>
                                    <td class="text-end">${{ number_format($rsv->total_amount, 2) }}</td>
                                    <td class="text-end text-primary">${{ number_format($rsv->paid_amount, 2) }}</td>
                                    <td class="text-end text-danger fw-bold fs-6">${{ number_format($rsv->pending_balance, 2) }}</td>
                                    <td class="text-end">
                                        <a href="{{ route('admin.reservations.show', $rsv) }}" class="btn btn-sm btn-outline-success">
                                            <i class="bi bi-cash"></i> Cobrar
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>

    <!-- Pestaña 2: Gastos Operativos -->
    <div class="tab-pane fade" id="expenses" role="tabpanel">
        <div class="card border-0 shadow-sm rounded-3 overflow-hidden bg-white">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light small">
                        <tr>
                            <th>Fecha</th>
                            <th>Categoría</th>
                            <th>Descripción</th>
                            <th class="text-end">Monto</th>
                            <th class="text-center">Comprobante</th>
                            <th class="text-end">Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($expenses as $exp)
                            <tr>
                                <td class="small">{{ $exp->expense_date->format('d/m/Y') }}</td>
                                <td>
                                    <span class="badge bg-secondary-subtle text-secondary border">
                                        {{ \App\Models\Expense::CATEGORIES[$exp->category] ?? $exp->category }}
                                    </span>
                                </td>
                                <td>{{ $exp->description }}</td>
                                <td class="text-end fw-bold text-danger">-${{ number_format($exp->amount, 2) }}</td>
                                <td class="text-center">
                                    @if($exp->receiptMedia)
                                        <a href="{{ Storage::url($exp->receiptMedia->path) }}" target="_blank" class="btn btn-xs btn-outline-primary py-0">
                                            <i class="bi bi-file-earmark-image"></i> Ver Recibo
                                        </a>
                                    @else
                                        <span class="text-muted small">Sin adjunto</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <form action="{{ route('admin.finances.expense.destroy', $exp) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar este gasto?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger py-0">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">No hay gastos operativos registrados en el sistema.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="d-flex justify-content-center mt-4">
            {{ $expenses->links() }}
        </div>
    </div>
</div>

<!-- Modal Registrar Gasto Operativo -->
<div class="modal fade" id="newExpenseModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="bi bi-receipt me-2"></i>Registrar Gasto Operativo</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.finances.expense.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Categoría del Gasto</label>
                        <select name="category" class="form-select" required>
                            @foreach($categories as $key => $lbl)
                                <option value="{{ $key }}">{{ $lbl }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Descripción</label>
                        <input type="text" name="description" class="form-control" placeholder="Ej: Flete ida y regreso camión evento Tequendama" required>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold">Monto ($)</label>
                            <input type="number" step="0.01" min="0.01" name="amount" class="form-control" placeholder="0.00" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold">Fecha del Gasto</label>
                            <input type="date" name="expense_date" class="form-control" value="{{ now()->toDateString() }}" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Comprobante / Recibo (opcional)</label>
                        <input type="file" name="receipt" class="form-control" accept="image/*,.pdf">
                        <div class="form-text small">Guardado en Storage para auditoría y deducibilidad.</div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Registrar Gasto</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
