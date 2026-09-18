@extends('layouts.admin')

@section('title', 'Inventario de Equipos')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h2 class="fw-bold text-dark mb-1">
            <i class="bi bi-box-seam text-primary me-2"></i>Inventario de Equipos para Eventos
        </h2>
        <p class="text-muted mb-0">Catálogo de artículos, tarifas diferenciadas (día/fin de semana), stock y estados.</p>
    </div>
    <a href="{{ route('admin.items.create') }}" class="btn btn-primary d-flex align-items-center gap-2 shadow-sm">
        <i class="bi bi-plus-circle-fill"></i> Registrar Nuevo Equipo
    </a>
</div>

<!-- Filtros de Búsqueda -->
<div class="card border-0 shadow-sm rounded-3 bg-white p-3 mb-4">
    <form method="GET" action="{{ route('admin.items.index') }}" class="row g-2 align-items-center">
        <div class="col-md-4">
            <div class="input-group input-group-sm">
                <span class="input-group-text bg-light"><i class="bi bi-search"></i></span>
                <input type="text" name="search" class="form-control" placeholder="Buscar por código, nombre o descripción..." value="{{ request('search') }}">
            </div>
        </div>
        <div class="col-md-3">
            <select name="category" class="form-select form-select-sm">
                <option value="">-- Todas las Categorías --</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat }}" {{ request('category') === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <select name="status" class="form-select form-select-sm">
                <option value="">-- Todos los Estados --</option>
                <option value="disponible" {{ request('status') === 'disponible' ? 'selected' : '' }}>Disponible</option>
                <option value="en_mantenimiento" {{ request('status') === 'en_mantenimiento' ? 'selected' : '' }}>En Mantenimiento</option>
                <option value="dado_de_baja" {{ request('status') === 'dado_de_baja' ? 'selected' : '' }}>Dado de Baja</option>
            </select>
        </div>
        <div class="col-md-2 d-flex gap-2">
            <button type="submit" class="btn btn-sm btn-primary w-100">Filtrar</button>
            <a href="{{ route('admin.items.index') }}" class="btn btn-sm btn-outline-secondary">Limpiar</a>
        </div>
    </form>
</div>

<!-- Tabla de Artículos -->
@if($items->isEmpty())
    <div class="card border-0 shadow-sm p-5 text-center bg-white rounded-3">
        <div class="text-muted mb-3"><i class="bi bi-inbox fs-1"></i></div>
        <h5 class="fw-bold text-secondary">No se encontraron equipos</h5>
        <p class="text-muted">Ajusta los filtros o registra el primer equipo en el inventario.</p>
        <div>
            <a href="{{ route('admin.items.create') }}" class="btn btn-primary px-4 py-2">
                <i class="bi bi-plus-circle me-1"></i> Registrar Primer Equipo
            </a>
        </div>
    </div>
@else
    <div class="card border-0 shadow-sm rounded-3 overflow-hidden bg-white">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light small">
                    <tr>
                        <th style="width: 80px;">Foto</th>
                        <th>Código & Nombre</th>
                        <th>Categoría</th>
                        <th class="text-center">Stock Total</th>
                        <th class="text-center">Libre Hoy</th>
                        <th class="text-end">Tarifa Día</th>
                        <th class="text-end">Tarifa Fin de Sem.</th>
                        <th class="text-center">Estado</th>
                        <th class="text-end" style="width: 140px;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($items as $item)
                        <tr>
                            <td>
                                @if($item->media)
                                    <img src="{{ Storage::url($item->media->path) }}" alt="{{ $item->name }}" class="rounded object-fit-cover shadow-sm" style="width: 60px; height: 45px;">
                                @else
                                    <div class="bg-light rounded d-flex align-items-center justify-content-center text-muted border" style="width: 60px; height: 45px; font-size: 10px;">
                                        <i class="bi bi-image"></i>
                                    </div>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.items.show', $item) }}" class="fw-bold text-dark text-decoration-none">
                                    {{ $item->name }}
                                </a>
                                <div><code class="small text-muted">{{ $item->code }}</code></div>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border">{{ $item->category }}</span>
                            </td>
                            <td class="text-center fw-bold">{{ $item->total_quantity }}</td>
                            <td class="text-center">
                                <span class="badge {{ $item->available_today > 0 ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-danger-subtle text-danger border border-danger-subtle' }} px-2 py-1">
                                    {{ $item->available_today }} unid(s)
                                </span>
                            </td>
                            <td class="text-end text-primary fw-semibold">${{ number_format($item->daily_rate, 2) }}</td>
                            <td class="text-end text-success fw-semibold">${{ number_format($item->weekend_rate, 2) }}</td>
                            <td class="text-center">
                                @if($item->status === 'disponible')
                                    <span class="badge bg-success">Disponible</span>
                                @elseif($item->status === 'en_mantenimiento')
                                    <span class="badge bg-warning text-dark">Mantenimiento</span>
                                @else
                                    <span class="badge bg-danger">Baja</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('admin.items.show', $item) }}" class="btn btn-outline-info" title="Ver ficha técnica">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.items.edit', $item) }}" class="btn btn-outline-secondary" title="Editar">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('admin.items.destroy', $item) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar este equipo?');">
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
        {{ $items->links() }}
    </div>
@endif
@endsection
