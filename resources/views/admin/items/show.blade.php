@extends('layouts.admin')

@section('title', $item->name)

@section('content')
<div class="mb-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
    <div>
        <a href="{{ route('admin.items.index') }}" class="btn btn-outline-secondary btn-sm mb-2">
            <i class="bi bi-arrow-left me-1"></i> Volver al inventario
        </a>
        <h2 class="fw-bold text-dark mb-1">
            <span class="badge bg-secondary font-monospace me-2">{{ $item->code }}</span>{{ $item->name }}
        </h2>
    </div>
    <div class="d-flex gap-2">
        <button type="button" class="btn btn-outline-warning" data-bs-toggle="modal" data-bs-target="#maintenanceModal">
            <i class="bi bi-tools me-1"></i> Registrar Mantenimiento / Baja
        </button>
        <a href="{{ route('admin.items.edit', $item) }}" class="btn btn-primary">
            <i class="bi bi-pencil me-1"></i> Editar Equipo
        </a>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm rounded-3 bg-white p-3 text-center">
            @if($item->media)
                <img src="{{ Storage::url($item->media->path) }}" alt="{{ $item->name }}" class="img-fluid rounded shadow-sm mb-2" style="max-height: 280px; object-fit: cover;">
                <small class="text-muted d-block">Archivo: <code>{{ $item->media->name }}</code></small>
            @else
                <div class="bg-light rounded p-5 text-muted border">
                    <i class="bi bi-image fs-1 d-block mb-2"></i>
                    Sin imagen registrada
                </div>
            @endif
        </div>
    </div>

    <div class="col-lg-7">
        <div class="card border-0 shadow-sm rounded-3 bg-white p-4 h-100">
            <h5 class="fw-bold text-dark mb-3">Ficha Técnica & Disponibilidad</h5>

            <div class="row g-3 mb-4">
                <div class="col-sm-6">
                    <div class="p-3 bg-light rounded-3">
                        <small class="text-muted d-block">Categoría:</small>
                        <span class="fw-bold fs-6 text-dark">{{ $item->category }}</span>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="p-3 bg-light rounded-3">
                        <small class="text-muted d-block">Estado Operativo:</small>
                        <span class="badge {{ $item->status === 'disponible' ? 'bg-success' : ($item->status === 'en_mantenimiento' ? 'bg-warning text-dark' : 'bg-danger') }}">
                            {{ ucfirst(str_replace('_', ' ', $item->status)) }}
                        </span>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="p-3 bg-light rounded-3">
                        <small class="text-muted d-block">Stock Total:</small>
                        <span class="fw-bold fs-5 text-dark">{{ $item->total_quantity }} unidades</span>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="p-3 bg-light rounded-3">
                        <small class="text-muted d-block">Disponibilidad Hoy:</small>
                        <span class="fw-bold fs-5 {{ $item->available_today > 0 ? 'text-success' : 'text-danger' }}">
                            {{ $item->available_today }} unidad(es)
                        </span>
                    </div>
                </div>
            </div>

            <div class="row g-3 p-3 bg-primary bg-opacity-10 rounded-3 mb-3 border border-primary border-opacity-25">
                <div class="col-sm-6">
                    <small class="text-muted d-block fw-semibold">TARIFA DÍA HÁBIL</small>
                    <span class="fs-4 fw-bold text-primary">${{ number_format($item->daily_rate, 2) }}</span>
                </div>
                <div class="col-sm-6">
                    <small class="text-muted d-block fw-semibold">TARIFA FIN DE SEMANA</small>
                    <span class="fs-4 fw-bold text-success">${{ number_format($item->weekend_rate, 2) }}</span>
                </div>
            </div>

            <p class="text-secondary mb-0">
                <strong>Descripción:</strong> {{ $item->description ?? 'Sin especificaciones detalladas registradas.' }}
            </p>
        </div>
    </div>
</div>

<!-- Historial de Mantenimientos y Bajas (Requerimiento 3.10) -->
<div class="card border-0 shadow-sm rounded-3 bg-white p-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold text-dark mb-0">
            <i class="bi bi-wrench me-2 text-warning"></i>Historial de Mantenimiento y Bajas
        </h5>
        <button type="button" class="btn btn-sm btn-outline-warning" data-bs-toggle="modal" data-bs-target="#maintenanceModal">
            <i class="bi bi-plus-circle me-1"></i> Añadir Registro
        </button>
    </div>

    @if($item->maintenanceRecords->isEmpty())
        <div class="text-center py-4 text-muted">
            <i class="bi bi-shield-check fs-2 text-success d-block mb-2"></i>
            Este equipo no registra novedades técnicas ni mantenimientos en su historial.
        </div>
    @else
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light small">
                    <tr>
                        <th>Fecha</th>
                        <th>Tipo</th>
                        <th>Descripción</th>
                        <th>Costo ($)</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($item->maintenanceRecords as $record)
                        <tr>
                            <td class="small">{{ $record->maintenance_date->format('d/m/Y') }}</td>
                            <td>
                                <span class="badge {{ $record->type === 'baja_inventario' ? 'bg-danger' : 'bg-warning text-dark' }}">
                                    {{ \App\Models\MaintenanceRecord::TYPES[$record->type] ?? $record->type }}
                                </span>
                            </td>
                            <td>{{ $record->description }}</td>
                            <td class="fw-semibold">${{ number_format($record->cost, 2) }}</td>
                            <td>
                                <span class="badge {{ $record->status === 'completado' ? 'bg-success' : 'bg-secondary' }}">
                                    {{ ucfirst($record->status) }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

<!-- Modal para Registrar Mantenimiento / Baja -->
<div class="modal fade" id="maintenanceModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-tools me-2"></i>Mantenimiento / Baja de Equipo
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.items.maintenance', $item) }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Tipo de Intervención</label>
                        <select name="type" class="form-select" required>
                            @foreach($maintenanceTypes as $key => $name)
                                <option value="{{ $key }}">{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold">Fecha</label>
                            <input type="date" name="maintenance_date" class="form-control" value="{{ now()->toDateString() }}" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold">Costo ($)</label>
                            <input type="number" step="0.01" name="cost" class="form-control" value="0.00" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Estado de la Intervención</label>
                        <select name="status" class="form-select" required>
                            <option value="programado">Programado</option>
                            <option value="en_proceso">En Proceso</option>
                            <option value="completado">Completado</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Descripción del Daño / Reparación</label>
                        <textarea name="description" class="form-control" rows="3" placeholder="Detalle técnico de la reparación o motivo de la baja..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-warning fw-bold">Registrar Novedad</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
