@extends('layouts.admin')

@section('title', 'Editar Noticia')

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.news.index') }}" class="btn btn-outline-secondary btn-sm mb-2">
        <i class="bi bi-arrow-left me-1"></i> Volver al listado
    </a>
    <h2 class="fw-bold text-dark mb-1">
        <i class="bi bi-pencil-square text-primary me-2"></i>Editar Publicación: {{ $news->title }}
    </h2>
    <p class="text-muted">Modifica los detalles, estado o reemplaza la imagen asociada según sección 5.13.</p>
</div>

<form action="{{ route('admin.news.update', $news) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <div class="row g-4">
        <!-- Columna Principal -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm p-4 bg-white rounded-3 mb-4">
                <div class="mb-3">
                    <label for="title" class="form-label fw-semibold">Título de la Noticia <span class="text-danger">*</span></label>
                    <input type="text" class="form-control form-control-lg" id="title" name="title" value="{{ old('title', $news->title) }}" required>
                </div>

                <div class="mb-3">
                    <label for="slug" class="form-label fw-semibold">Slug (identificador único)</label>
                    <input type="text" class="form-control" id="slug" name="slug" value="{{ old('slug', $news->slug) }}" required>
                </div>

                <div class="mb-3">
                    <label for="excerpt" class="form-label fw-semibold">Resumen / Extracto</label>
                    <textarea class="form-control" id="excerpt" name="excerpt" rows="2">{{ old('excerpt', $news->excerpt) }}</textarea>
                </div>

                <div class="mb-3">
                    <label for="content" class="form-label fw-semibold">Contenido Completo <span class="text-danger">*</span></label>
                    <textarea class="form-control" id="content" name="content" rows="10" required>{{ old('content', $news->content) }}</textarea>
                </div>
            </div>
        </div>

        <!-- Columna Lateral -->
        <div class="col-lg-4">
            <!-- Publicación -->
            <div class="card border-0 shadow-sm p-4 bg-white rounded-3 mb-4">
                <h5 class="fw-bold text-dark mb-3">
                    <i class="bi bi-gear me-1"></i> Estado
                </h5>
                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" role="switch" id="published" name="published" value="1" {{ old('published', $news->published) ? 'checked' : '' }}>
                    <label class="form-check-label fw-medium" for="published">Publicado en portal web</label>
                </div>
                <button type="submit" class="btn btn-primary w-100 py-2 d-flex align-items-center justify-content-center gap-2">
                    <i class="bi bi-check-lg fs-5"></i> Guardar Cambios
                </button>
            </div>

            <!-- Gestión de Imagen y Reemplazo -->
            <div class="card border-0 shadow-sm p-4 bg-white rounded-3">
                <h5 class="fw-bold text-dark mb-3">
                    <i class="bi bi-image me-1"></i> Imagen Asociada
                </h5>

                @if($news->media)
                    <div class="mb-3 text-center p-3 border rounded bg-light">
                        <img src="{{ Storage::url($news->media->path) }}" alt="{{ $news->title }}" class="img-fluid rounded shadow-sm mb-2" style="max-height: 150px;">
                        <div class="fw-semibold text-truncate small">{{ $news->media->name }}</div>
                        <div class="text-muted small"><code>{{ $news->media->path }}</code></div>
                    </div>
                @else
                    <div class="alert alert-secondary py-2 small mb-3">
                        <i class="bi bi-info-circle me-1"></i> No tiene imagen asignada actualmente.
                    </div>
                @endif

                <!-- Pestañas para reemplazar o seleccionar -->
                <ul class="nav nav-pills nav-fill mb-3" id="editImageTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active btn-sm" id="replace-file-tab" data-bs-toggle="tab" data-bs-target="#tab-replace-file" type="button" role="tab">
                            <i class="bi bi-arrow-repeat me-1"></i> Reemplazar
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link btn-sm" id="select-lib-tab" data-bs-toggle="tab" data-bs-target="#tab-select-lib" type="button" role="tab">
                            <i class="bi bi-collection me-1"></i> Cambiar
                        </button>
                    </li>
                </ul>

                <div class="tab-content">
                    <!-- Reemplazo directo (Sección 5.13) -->
                    <div class="tab-pane fade show active" id="tab-replace-file" role="tabpanel">
                        <label for="file" class="form-label fw-semibold small">Subir nueva imagen de sustitución:</label>
                        <input type="file" class="form-control mb-2" id="file" name="file" accept="image/*">
                        <div class="form-text small text-muted">
                            Conforme a la sección 5.13, el archivo anterior será eliminado físicamente del disco para evitar residuos de almacenamiento.
                        </div>
                    </div>

                    <!-- Cambiar por otra imagen existente de la biblioteca -->
                    <div class="tab-pane fade" id="tab-select-lib" role="tabpanel">
                        <label for="media_id" class="form-label fw-semibold small">Seleccionar otra de la biblioteca:</label>
                        <select class="form-select mb-2" name="media_id" id="media_id">
                            <option value="">-- Quitar imagen / Sin imagen --</option>
                            @foreach($mediaItems as $media)
                                <option value="{{ $media->id }}" {{ old('media_id', $news->media_id) == $media->id ? 'selected' : '' }}>
                                    #{{ $media->id }} - {{ $media->name }} ({{ $media->formatted_size }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection
