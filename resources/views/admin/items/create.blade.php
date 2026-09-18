@extends('layouts.admin')

@section('title', 'Registrar Equipo')

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.items.index') }}" class="btn btn-outline-secondary btn-sm mb-2">
        <i class="bi bi-arrow-left me-1"></i> Volver al inventario
    </a>
    <h2 class="fw-bold text-dark mb-1">
        <i class="bi bi-plus-circle text-primary me-2"></i>Registrar Nuevo Equipo en Inventario
    </h2>
    <p class="text-muted">Añade equipos con sus tarifas diferenciadas y foto técnica.</p>
</div>

<form action="{{ route('admin.items.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="row g-4">
        <!-- Datos Principales -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm p-4 bg-white rounded-3 mb-4">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="code" class="form-label fw-semibold">Código Único <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="code" name="code" placeholder="Ej: AUD-001, ILU-005" value="{{ old('code') }}" required style="text-transform: uppercase;">
                    </div>

                    <div class="col-md-8">
                        <label for="name" class="form-label fw-semibold">Nombre del Equipo <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" placeholder="Ej: Parlante Activo 15' JBL EON715" value="{{ old('name') }}" required>
                    </div>

                    <div class="col-md-6">
                        <label for="category" class="form-label fw-semibold">Categoría <span class="text-danger">*</span></label>
                        <select class="form-select" id="category" name="category" required>
                            <option value="">-- Seleccionar Categoría --</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat }}" {{ old('category') === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label for="total_quantity" class="form-label fw-semibold">Cantidad Total en Stock <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="total_quantity" name="total_quantity" min="1" value="{{ old('total_quantity', 1) }}" required>
                    </div>

                    <div class="col-12">
                        <label for="description" class="form-label fw-semibold">Descripción / Especificaciones Técnicas</label>
                        <textarea class="form-control" id="description" name="description" rows="4" placeholder="Potencia, conectividad, accesorios incluidos, recomendaciones de uso...">{{ old('description') }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Tarifas Diferenciadas (Requerimiento 3.2 y 3.3) -->
            <div class="card border-0 shadow-sm p-4 bg-white rounded-3">
                <h5 class="fw-bold text-dark mb-3">
                    <i class="bi bi-tag me-1"></i> Tarifas de Alquiler del Negocio
                </h5>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="daily_rate" class="form-label fw-semibold">Tarifa por Día Hábil ($) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" step="0.01" class="form-control" id="daily_rate" name="daily_rate" placeholder="0.00" value="{{ old('daily_rate') }}" required>
                        </div>
                        <div class="form-text small">Cobro por cada jornada de 24 horas.</div>
                    </div>

                    <div class="col-md-6">
                        <label for="weekend_rate" class="form-label fw-semibold">Tarifa Plana Fin de Semana ($) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" step="0.01" class="form-control" id="weekend_rate" name="weekend_rate" placeholder="0.00" value="{{ old('weekend_rate') }}" required>
                        </div>
                        <div class="form-text small">Tarifa plana especial para eventos de sábado y domingo.</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Columna Lateral: Imagen y Estado -->
        <div class="col-lg-4">
            <!-- Estado Inicial -->
            <div class="card border-0 shadow-sm p-4 bg-white rounded-3 mb-4">
                <h5 class="fw-bold text-dark mb-3">
                    <i class="bi bi-sliders me-1"></i> Estado Operativo
                </h5>
                <div class="mb-3">
                    <label for="status" class="form-label fw-semibold">Estado del Artículo</label>
                    <select class="form-select" id="status" name="status" required>
                        <option value="disponible" {{ old('status') === 'disponible' ? 'selected' : '' }}>Disponible para Alquiler</option>
                        <option value="en_mantenimiento" {{ old('status') === 'en_mantenimiento' ? 'selected' : '' }}>En Mantenimiento</option>
                        <option value="dado_de_baja" {{ old('status') === 'dado_de_baja' ? 'selected' : '' }}>Dado de Baja</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold">
                    <i class="bi bi-check-lg me-1"></i> Guardar Equipo
                </button>
            </div>

            <!-- Imagen del Equipo (Storage Seguro) -->
            <div class="card border-0 shadow-sm p-4 bg-white rounded-3">
                <h5 class="fw-bold text-dark mb-3">
                    <i class="bi bi-camera me-1"></i> Fotografía del Equipo
                </h5>

                <ul class="nav nav-pills nav-fill mb-3" id="itemImgTabs" role="tablist">
                    <li class="nav-item">
                        <button class="nav-link active btn-sm" data-bs-toggle="tab" data-bs-target="#tab-item-lib" type="button">
                            Biblioteca
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link btn-sm" data-bs-toggle="tab" data-bs-target="#tab-item-up" type="button">
                            Subir Nueva
                        </button>
                    </li>
                </ul>

                <div class="tab-content">
                    <div class="tab-pane fade show active" id="tab-item-lib">
                        <select class="form-select mb-2" name="media_id" id="media_id">
                            <option value="">-- Sin imagen asociada --</option>
                            @foreach($mediaItems as $media)
                                <option value="{{ $media->id }}" {{ old('media_id') == $media->id ? 'selected' : '' }}>
                                    #{{ $media->id }} - {{ $media->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="tab-pane fade" id="tab-item-up">
                        <input type="file" class="form-control mb-2" name="file" accept="image/*">
                        <div class="form-text small">JPG, PNG, WEBP o GIF (máx 5MB). Almacenado de forma segura en Storage.</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection
