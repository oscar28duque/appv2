@extends('layouts.admin')

@section('title', 'Directorio de Clientes')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h2 class="fw-bold text-dark mb-1">
            <i class="bi bi-people text-primary me-2"></i>Directorio de Clientes
        </h2>
        <p class="text-muted mb-0">Historial comercial, datos de contacto y saldos acumulados.</p>
    </div>
    <a href="{{ route('admin.clients.create') }}" class="btn btn-primary d-flex align-items-center gap-2 shadow-sm">
        <i class="bi bi-person-plus-fill"></i> Registrar Cliente
    </a>
</div>

<!-- Buscador -->
<div class="card border-0 shadow-sm rounded-3 bg-white p-3 mb-4">
    <form method="GET" action="{{ route('admin.clients.index') }}" class="row g-2 align-items-center">
        <div class="col-md-10">
            <div class="input-group input-group-sm">
                <span class="input-group-text bg-light"><i class="bi bi-search"></i></span>
                <input type="text" name="search" class="form-control" placeholder="Buscar por nombre, documento (CC/NIT), empresa o teléfono..." value="{{ request('search') }}">
            </div>
        </div>
        <div class="col-md-2 d-flex gap-2">
            <button type="submit" class="btn btn-sm btn-primary w-100">Buscar</button>
            <a href="{{ route('admin.clients.index') }}" class="btn btn-sm btn-outline-secondary">Limpiar</a>
        </div>
    </form>
</div>

@if($clients->isEmpty())
    <div class="card border-0 shadow-sm p-5 text-center bg-white rounded-3">
        <div class="text-muted mb-3"><i class="bi bi-person-x fs-1"></i></div>
        <h5 class="fw-bold text-secondary">No se encontraron clientes</h5>
        <div>
            <a href="{{ route('admin.clients.create') }}" class="btn btn-primary px-4 py-2 mt-2">
                <i class="bi bi-person-plus me-1"></i> Registrar Primer Cliente
            </a>
        </div>
    </div>
@else
    <div class="card border-0 shadow-sm rounded-3 overflow-hidden bg-white">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light small">
                    <tr>
                        <th>Cliente / Empresa</th>
                        <th>Documento (CC/NIT)</th>
                        <th>Contacto</th>
                        <th class="text-center">Alquileres</th>
                        <th class="text-end">Total Facturado</th>
                        <th class="text-end">Saldo Pendiente</th>
                        <th class="text-end" style="width: 140px;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($clients as $c)
                        <tr>
                            <td>
                                <a href="{{ route('admin.clients.show', $c) }}" class="fw-bold text-dark text-decoration-none">
                                    {{ $c->name }}
                                </a>
                                @if($c->company)
                                    <div class="text-muted small"><i class="bi bi-building me-1"></i>{{ $c->company }}</div>
                                @endif
                            </td>
                            <td><code>{{ $c->document ?? 'N/A' }}</code></td>
                            <td class="small">
                                <div><i class="bi bi-envelope me-1"></i>{{ $c->email ?? '-' }}</div>
                                <div><i class="bi bi-telephone me-1"></i>{{ $c->phone ?? '-' }}</div>
                            </td>
                            <td class="text-center fw-bold">{{ $c->reservations_count }}</td>
                            <td class="text-end fw-semibold">${{ number_format($c->total_billed, 2) }}</td>
                            <td class="text-end">
                                @if($c->pending_balance > 0)
                                    <span class="text-danger fw-bold">${{ number_format($c->pending_balance, 2) }}</span>
                                @else
                                    <span class="text-success small fw-semibold"><i class="bi bi-check-circle-fill"></i> Al día</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('admin.clients.show', $c) }}" class="btn btn-outline-info" title="Ver historial">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.clients.edit', $c) }}" class="btn btn-outline-secondary" title="Editar">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('admin.clients.destroy', $c) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar este cliente?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger" title="Eliminar">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="d-flex justify-content-center mt-4">
        {{ $clients->links() }}
    </div>
@endif
@endsection
