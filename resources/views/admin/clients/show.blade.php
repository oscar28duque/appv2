@extends('layouts.admin')

@section('title', 'Cliente: ' . $client->name)

@section('content')
<div class="mb-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
    <div>
        <a href="{{ route('admin.clients.index') }}" class="btn btn-outline-secondary btn-sm mb-2">
            <i class="bi bi-arrow-left me-1"></i> Volver al directorio
        </a>
        <h2 class="fw-bold text-dark mb-0">
            <i class="bi bi-person-circle text-primary me-2"></i>{{ $client->name }}
        </h2>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.reservations.create') }}?client_id={{ $client->id }}" class="btn btn-primary">
            <i class="bi bi-calendar-plus me-1"></i> Nueva Reserva para Cliente
        </a>
        <a href="{{ route('admin.clients.edit', $client) }}" class="btn btn-outline-secondary">
            <i class="bi bi-pencil me-1"></i> Editar Datos
        </a>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm p-4 bg-white rounded-3 h-100">
            <h5 class="fw-bold text-dark mb-3">Información de Contacto</h5>
            <ul class="list-unstyled mb-4">
                <li class="mb-2"><strong>Empresa:</strong> {{ $client->company ?? 'Particular' }}</li>
                <li class="mb-2"><strong>Documento (CC/NIT):</strong> <code>{{ $client->document ?? 'No registrado' }}</code></li>
                <li class="mb-2"><strong>Correo:</strong> {{ $client->email ?? 'No registrado' }}</li>
                <li class="mb-2"><strong>Teléfono:</strong> {{ $client->phone ?? 'No registrado' }}</li>
                <li class="mb-2"><strong>Dirección:</strong> {{ $client->address ?? 'No registrada' }}</li>
            </ul>

            <div class="p-3 bg-light rounded-3 border">
                <div class="d-flex justify-content-between mb-1">
                    <span class="text-muted small">Total Histórico:</span>
                    <span class="fw-bold text-dark">${{ number_format($client->total_billed, 2) }}</span>
                </div>
                <div class="d-flex justify-content-between">
                    <span class="text-muted small">Saldo Pendiente:</span>
                    <span class="fw-bold {{ $client->pending_balance > 0 ? 'text-danger' : 'text-success' }}">
                        ${{ number_format($client->pending_balance, 2) }}
                    </span>
                </div>
            </div>

            @if($client->notes)
                <div class="mt-3">
                    <small class="text-muted fw-bold d-block">Notas:</small>
                    <p class="small text-secondary mb-0">{{ $client->notes }}</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Historial de Reservas -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm p-4 bg-white rounded-3 h-100">
            <h5 class="fw-bold text-dark mb-3"><i class="bi bi-clock-history me-1"></i> Historial de Reservas & Alquileres</h5>

            @if($client->reservations->isEmpty())
                <div class="text-center py-4 text-muted">
                    <i class="bi bi-calendar-x fs-2 d-block mb-2"></i>
                    Este cliente aún no registra alquileres de equipos.
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light small">
                            <tr>
                                <th>Código</th>
                                <th>Evento</th>
                                <th>Fechas</th>
                                <th>Estado</th>
                                <th class="text-end">Total</th>
                                <th class="text-end">Saldo</th>
                                <th class="text-end">Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($client->reservations as $rsv)
                                <tr>
                                    <td><strong>{{ $rsv->code }}</strong></td>
                                    <td>{{ $rsv->event_name }}</td>
                                    <td class="small">{{ $rsv->start_date->format('d/m') }} - {{ $rsv->end_date->format('d/m/Y') }}</td>
                                    <td>
                                        <span class="badge {{ $rsv->status === 'confirmada' ? 'bg-primary' : ($rsv->status === 'en_curso' ? 'bg-warning text-dark' : 'bg-success') }}">
                                            {{ ucfirst($rsv->status) }}
                                        </span>
                                    </td>
                                    <td class="text-end fw-semibold">${{ number_format($rsv->total_amount, 2) }}</td>
                                    <td class="text-end">
                                        @if($rsv->pending_balance > 0)
                                            <span class="text-danger fw-bold">${{ number_format($rsv->pending_balance, 2) }}</span>
                                        @else
                                            <span class="text-success small"><i class="bi bi-check-circle"></i> Al día</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <a href="{{ route('admin.reservations.show', $rsv) }}" class="btn btn-sm btn-outline-primary py-0">Ver</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
