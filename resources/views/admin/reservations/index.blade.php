@extends('layouts.admin')

@section('title', 'Gestión de Reservas')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h2 class="fw-bold text-dark mb-1">
            <i class="bi bi-calendar-check text-primary me-2"></i>Gestión de Reservas & Alquileres
        </h2>
        <p class="text-muted mb-0">Control de disponibilidad, cálculo de tarifas (día vs fin de semana) y seguimiento de contratos.</p>
    </div>
    <a href="{{ route('admin.reservations.create') }}" class="btn btn-primary d-flex align-items-center gap-2 shadow-sm">
        <i class="bi bi-plus-circle-fill"></i> Crear Nueva Reserva
    </a>
</div>

<!-- Filtros -->
<div class="card border-0 shadow-sm rounded-3 bg-white p-3 mb-4">
    <form method="GET" action="{{ route('admin.reservations.index') }}" class="row g-2 align-items-center">
        <div class="col-md-4">
            <div class="input-group input-group-sm">
                <span class="input-group-text bg-light"><i class="bi bi-search"></i></span>
                <input type="text" name="search" class="form-control" placeholder="Buscar por código, evento o cliente..." value="{{ request('search') }}">
            </div>
        </div>
        <div class="col-md-3">
            <select name="status" class="form-select form-select-sm">
                <option value="">-- Todos los Estados --</option>
                @foreach($statuses as $val => $label)
                    <option value="{{ $val }}" {{ request('status') === $val ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <input type="date" name="start_date" class="form-control form-control-sm" title="Desde fecha" value="{{ request('start_date') }}">
        </div>
        <div class="col-md-2 d-flex gap-2">
            <button type="submit" class="btn btn-sm btn-primary w-100">Filtrar</button>
            <a href="{{ route('admin.reservations.index') }}" class="btn btn-sm btn-outline-secondary">Limpiar</a>
        </div>
    </form>
</div>

@if($reservations->isEmpty())
    <div class="card border-0 shadow-sm p-5 text-center bg-white rounded-3">
        <div class="text-muted mb-3"><i class="bi bi-calendar-x fs-1"></i></div>
        <h5 class="fw-bold text-secondary">No se encontraron reservas</h5>
        <p class="text-muted">No hay registros que coincidan con los criterios de búsqueda.</p>
        <div>
            <a href="{{ route('admin.reservations.create') }}" class="btn btn-primary px-4 py-2">
                <i class="bi bi-plus-circle me-1"></i> Crear Nueva Reserva
            </a>
        </div>
    </div>
@else
    <div class="card border-0 shadow-sm rounded-3 overflow-hidden bg-white">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light small">
                    <tr>
                        <th>Código</th>
                        <th>Cliente & Evento</th>
                        <th>Periodo & Duración</th>
                        <th>Modalidad</th>
                        <th class="text-end">Total</th>
                        <th class="text-end">Saldo Pendiente</th>
                        <th class="text-center">Estado</th>
                        <th class="text-end" style="width: 140px;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($reservations as $rsv)
                        <tr class="{{ $rsv->is_overdue ? 'table-danger' : '' }}">
                            <td>
                                <a href="{{ route('admin.reservations.show', $rsv) }}" class="fw-bold text-primary text-decoration-none">
                                    {{ $rsv->code }}
                                </a>
                                @if($rsv->is_overdue)
                                    <span class="badge bg-danger d-block mt-1" style="font-size: 10px;">Devolución Vencida</span>
                                @endif
                            </td>
                            <td>
                                <div class="fw-bold text-dark">{{ $rsv->client->name }}</div>
                                <small class="text-muted">{{ $rsv->event_name }} &bull; {{ $rsv->event_location ?? 'Sin locación' }}</small>
                            </td>
                            <td class="small">
                                <div><i class="bi bi-calendar-event me-1"></i>{{ $rsv->start_date->format('d/m/Y') }} al {{ $rsv->end_date->format('d/m/Y') }}</div>
                                <small class="text-muted">{{ $rsv->days_count }} día(s)</small>
                            </td>
                            <td>
                                <span class="badge {{ $rsv->pricing_type === 'weekend' ? 'bg-info-subtle text-info border border-info-subtle' : 'bg-light text-dark border' }} small">
                                    {{ $rsv->pricing_type === 'weekend' ? 'Fin de Semana' : 'Tarifa Diaria' }}
                                </span>
                            </td>
                            <td class="text-end fw-semibold text-dark">${{ number_format($rsv->total_amount, 2) }}</td>
                            <td class="text-end">
                                @if($rsv->pending_balance > 0)
                                    <span class="text-danger fw-bold">${{ number_format($rsv->pending_balance, 2) }}</span>
                                @else
                                    <span class="text-success small fw-semibold"><i class="bi bi-check-circle-fill"></i> Al día</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($rsv->status === 'confirmada')
                                    <span class="badge bg-primary">Confirmada</span>
                                @elseif($rsv->status === 'en_curso')
                                    <span class="badge bg-warning text-dark">En Curso</span>
                                @elseif($rsv->status === 'devuelta')
                                    <span class="badge bg-success">Devuelta</span>
                                @elseif($rsv->status === 'cancelada')
                                    <span class="badge bg-danger">Cancelada</span>
                                @else
                                    <span class="badge bg-secondary">Pendiente</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <a href="{{ route('admin.reservations.show', $rsv) }}" class="btn btn-sm btn-outline-primary" title="Ver detalles y gestionar">
                                    <i class="bi bi-eye"></i> Detalle
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="d-flex justify-content-center mt-4">
        {{ $reservations->links() }}
    </div>
@endif
@endsection
