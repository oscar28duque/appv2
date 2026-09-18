@extends('layouts.admin')

@section('title', 'Editar Equipo')

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.items.index') }}" class="btn btn-outline-secondary btn-sm mb-2">
        <i class="bi bi-arrow-left me-1"></i> Volver al inventario
    </a>
    <h2 class="fw-bold text-dark mb-1">
        <i class="bi bi-pencil-square text-primary me-2"></i>Editar Equipo: {{ $item->name }} ({{ $item->code }})
    </h2>
</div>

<form action="{{ route('admin.items.update', $item) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm p-4 bg-white rounded-3 mb-4">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="code" class="form-label fw-semibold">Código Único <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="code" name="code" value="{{ old('code', $item->code) }}" required style="text-transform: uppercase;">
                    </div>

                    <div class="col-md-8">
                        <label for="name" class="form-label fw-semibold">Nombre del Equipo <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $item->name) }}" required>
                    </div>

                    <div class="col-md-6">
                        <label for="category" class="form-label fw-semibold">Categoría <span class="text-danger">*</span></label>
                        <select class="form-select" id="category" name="category" required>
                            @foreach($categories as $cat)
                                <option value="{{ $cat }}" {{ old('category', $item->category) === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label for="total_quantity" class="form-label fw-semibold">Cantidad Total en Stock <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="total_quantity" name="total_quantity" min="1" value="{{ old('total_quantity', $item->total_quantity) }}" required>
                    </div>

                    <div class="col-12">
                        <label for="description" class="form-label fw-semibold">Descripción / Especificaciones</label>
                        <textarea class="form-control" id="description" name="description" rows="4">{{ old('description', $item->description) }}</textarea>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm p-4 bg-white rounded-3">
                <h5 class="fw-bold text-dark mb-3">
                    <i class="bi bi-tag me-1"></i> Tarifas de Alquiler
                </h5>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="daily_rate" class="form-label fw-semibold">Tarifa por Día Hábil ($) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" step="0.01" class="form-control" id="daily_rate" name="daily_rate" value="{{ old('daily_rate', $item->daily_rate) }}" required>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label for="weekend_rate" class="form-label fw-semibold">Tarifa Plana Fin de Semana ($) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" step="0.01" class="form-control" id="weekend_rate" name="weekend_rate" value="{{ old('weekend_rate', $item->weekend_rate) }}" required>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm p-4 bg-white rounded-3 mb-4">
                <h5 class="fw-bold text-dark mb-3">
                    <i class="bi bi-sliders me-1"></i> Estado Operativo
                </h5>
                <div class="mb-3">
                    <label for="status" class="form-label fw-semibold">Estado Actual</label>
                    <select class="form-select" id="status" name="status" required>
                        <option value="disponible" {{ old('status', $item->status) === 'disponible' ? 'selected' : '' }}>Disponible para Alquiler</option>
                        <option value="en_mantenimiento" {{ old('status', $item->status) === 'en_mantenimiento' ? 'selected' : '' }}>En Mantenimiento</option>
                        <option value="dado_de_baja" {{ old('status', $item->status) === 'dado_de_baja' ? 'selected' : '' }}>Dado de Baja</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold">
                    <i class="bi bi-check-lg me-1"></i> Actualizar Equipo
                </button>
            </div>

            <div class="card border-0 shadow-sm p-4 bg-white rounded-3">
                <h5 class="fw-bold text-dark mb-3">
                    <i class="bi bi-camera me-1"></i> Imagen Asociada
                </h5>
                @if($item->media)
                    <div class="text-center p-2 mb-3 bg-light rounded border">
                        <img src="{{ Storage::url($item->media->path) }}" alt="{{ $item->name }}" class="img-fluid rounded" style="max-height: 140px;">
                    </div>
                @endif

                <ul class="nav nav-pills nav-fill mb-3" id="editItemImgTabs" role="tablist">
                    <li class="nav-item">
                        <button class="nav-link active btn-sm" data-bs-toggle="tab" data-bs-target="#tab-item-change" type="button">
                            Cambiar
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link btn-sm" data-bs-toggle="tab" data-bs-target="#tab-item-new" type="button">
                            Subir Nueva
                        </button>
                    </li>
                </ul>

                <div class="tab-content">
                    <div class="tab-pane fade show active" id="tab-item-change">
                        <select class="form-select mb-2" name="media_id">
                            <option value="">-- Sin imagen asociada --</option>
                            @foreach($mediaItems as $media)
                                <option value="{{ $media->id }}" {{ old('media_id', $item->media_id) == $media->id ? 'selected' : '' }}>
                                    #{{ $media->id }} - {{ $media->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="tab-pane fade" id="tab-item-new">
                        <input type="file" class="form-control mb-2" name="file" accept="image/*">
                        <div class="form-text small">Se almacenará en Storage y se vinculará a este artículo.</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection
