@extends('layouts.admin')

@section('title', 'Redactar Noticia')

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.news.index') }}" class="btn btn-outline-secondary btn-sm mb-2">
        <i class="bi bi-arrow-left me-1"></i> Volver al listado
    </a>
    <h2 class="fw-bold text-dark mb-1">
        <i class="bi bi-pencil-square text-primary me-2"></i>Redactar Nueva Publicación
    </h2>
    <p class="text-muted">Crea una publicación asociándola a una imagen de la biblioteca multimedia o subiendo una nueva.</p>
</div>

<form action="{{ route('admin.news.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="row g-4">
        <!-- Columna Principal -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm p-4 bg-white rounded-3 mb-4">
                <div class="mb-3">
                    <label for="title" class="form-label fw-semibold">Título de la Noticia <span class="text-danger">*</span></label>
                    <input type="text" class="form-control form-control-lg" id="title" name="title" value="{{ old('title') }}" placeholder="Ingresa un título llamativo..." required>
                </div>

                <div class="mb-3">
                    <label for="slug" class="form-label fw-semibold">Slug (identificador URL único)</label>
                    <input type="text" class="form-control" id="slug" name="slug" value="{{ old('slug') }}" placeholder="ejemplo-noticia-institucional (opcional, se genera automáticamente)">
                    <div class="form-text">Si se deja en blanco, se creará automáticamente a partir del título.</div>
                </div>

                <div class="mb-3">
                    <label for="excerpt" class="form-label fw-semibold">Resumen / Extracto</label>
                    <textarea class="form-control" id="excerpt" name="excerpt" rows="2" placeholder="Breve introducción para los listados y tarjetas...">{{ old('excerpt') }}</textarea>
                </div>

                <div class="mb-3">
                    <label for="content" class="form-label fw-semibold">Contenido Completo <span class="text-danger">*</span></label>
                    <textarea class="form-control" id="content" name="content" rows="10" placeholder="Redacta el contenido completo de la noticia aquí..." required>{{ old('content') }}</textarea>
                </div>
            </div>
        </div>

        <!-- Columna Lateral: Multimedia y Publicación -->
        <div class="col-lg-4">
            <!-- Publicación -->
            <div class="card border-0 shadow-sm p-4 bg-white rounded-3 mb-4">
                <h5 class="fw-bold text-dark mb-3">
                    <i class="bi bi-gear me-1"></i> Publicación
                </h5>
                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" role="switch" id="published" name="published" value="1" {{ old('published') ? 'checked' : '' }}>
                    <label class="form-check-label fw-medium" for="published">Publicar de inmediato</label>
                </div>
                <div class="text-muted small mb-3">Si no se activa, la noticia quedará guardada en modo borrador.</div>
                <button type="submit" class="btn btn-primary w-100 py-2 d-flex align-items-center justify-content-center gap-2">
                    <i class="bi bi-check-lg fs-5"></i> Guardar Publicación
                </button>
            </div>

            <!-- Asignación de Imagen (Reto B: reutilizar medios o subir nuevo) -->
            <div class="card border-0 shadow-sm p-4 bg-white rounded-3">
                <h5 class="fw-bold text-dark mb-3">
                    <i class="bi bi-image me-1"></i> Imagen de Portada
                </h5>
                <p class="text-muted small">Selecciona una imagen existente de la biblioteca para evitar duplicados o sube un archivo nuevo.</p>

                <!-- Pestañas para elegir método -->
                <ul class="nav nav-pills nav-fill mb-3" id="imageTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active btn-sm" id="library-tab" data-bs-toggle="tab" data-bs-target="#tab-library" type="button" role="tab">
                            <i class="bi bi-collection me-1"></i> Biblioteca
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link btn-sm" id="upload-tab" data-bs-toggle="tab" data-bs-target="#tab-upload" type="button" role="tab">
                            <i class="bi bi-upload me-1"></i> Subir Nueva
                        </button>
                    </li>
                </ul>

                <div class="tab-content">
                    <!-- Pestaña Biblioteca -->
                    <div class="tab-pane fade show active" id="tab-library" role="tabpanel">
                        <label class="form-label fw-semibold small">Elegir de la biblioteca:</label>
                        <select class="form-select mb-3" name="media_id" id="media_id">
                            <option value="">-- Sin imagen asociada --</option>
                            @foreach($mediaItems as $media)
                                <option value="{{ $media->id }}" {{ old('media_id') == $media->id ? 'selected' : '' }} data-img-url="{{ Storage::url($media->path) }}">
                                    #{{ $media->id }} - {{ $media->name }} ({{ $media->formatted_size }})
                                </option>
                            @endforeach
                        </select>
                        <div id="media-preview" class="text-center p-2 border rounded bg-light d-none">
                            <img id="preview-img" src="" class="img-fluid rounded" style="max-height: 160px;">
                        </div>
                    </div>

                    <!-- Pestaña Subir Nueva -->
                    <div class="tab-pane fade" id="tab-upload" role="tabpanel">
                        <label for="file" class="form-label fw-semibold small">Subir archivo directo:</label>
                        <input type="file" class="form-control mb-2" id="file" name="file" accept="image/*">
                        <div class="form-text small">JPG, PNG, WEBP o GIF (máx 5MB). Se agregará automáticamente a la biblioteca multimedia.</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const select = document.getElementById('media_id');
        const previewContainer = document.getElementById('media-preview');
        const previewImg = document.getElementById('preview-img');

        function updatePreview() {
            const selectedOption = select.options[select.selectedIndex];
            const url = selectedOption.getAttribute('data-img-url');
            if (url) {
                previewImg.src = url;
                previewContainer.classList.remove('d-none');
            } else {
                previewContainer.classList.add('d-none');
                previewImg.src = '';
            }
        }

        select.addEventListener('change', updatePreview);
        updatePreview();
    });
</script>
@endpush
