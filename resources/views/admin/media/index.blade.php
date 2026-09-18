@extends('layouts.admin')

@section('title', 'Biblioteca Multimedia')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h2 class="fw-bold text-dark mb-1">
            <i class="bi bi-images text-primary me-2"></i>Biblioteca Multimedia
        </h2>
        <p class="text-muted mb-0">Gestión segura de archivos en <code>storage/app/public/media</code> servidos mediante <code>public/storage</code></p>
    </div>
    <button type="button" class="btn btn-primary d-flex align-items-center gap-2 shadow-sm" data-bs-toggle="modal" data-bs-target="#uploadModal">
        <i class="bi bi-cloud-arrow-up-fill fs-5"></i> Subir Nueva Imagen
    </button>
</div>

<!-- Modal de Carga Segura de Imágenes -->
<div class="modal fade" id="uploadModal" tabindex="-1" aria-labelledby="uploadModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="uploadModalLabel">
                    <i class="bi bi-shield-check me-2"></i>Cargar Imagen Segura
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.media.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-4">
                    <div class="alert alert-info py-2 px-3 small d-flex align-items-center gap-2 mb-3">
                        <i class="bi bi-info-circle-fill fs-5"></i>
                        <div>Formatos permitidos: <strong>JPG, JPEG, PNG, WEBP, GIF</strong>. Tamaño máximo: <strong>5 MB</strong>.</div>
                    </div>

                    <div class="mb-3">
                        <label for="upload_name" class="form-label fw-semibold">Nombre o Título descriptivo</label>
                        <input type="text" class="form-control" id="upload_name" name="name" placeholder="Ej. Convocatoria Innovación 2026" required value="{{ old('name') }}">
                    </div>

                    <div class="mb-3">
                        <label for="upload_file" class="form-label fw-semibold">Seleccionar Archivo de Imagen</label>
                        <input type="file" class="form-control" id="upload_file" name="file" accept="image/*" required>
                        <div class="form-text text-muted">La validación de tipo MIME y tamaño se realiza en el servidor.</div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary d-flex align-items-center gap-2">
                        <i class="bi bi-upload"></i> Subir Archivo
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Cuadrícula / Galería de Archivos Multimedia -->
@if($media->isEmpty())
    <div class="card border-0 shadow-sm p-5 text-center my-4 bg-white rounded-4">
        <div class="mb-3">
            <i class="bi bi-image text-muted" style="font-size: 4rem;"></i>
        </div>
        <h4 class="fw-bold text-secondary">Aún no hay archivos multimedia</h4>
        <p class="text-muted">Carga tu primera imagen para utilizarla en las publicaciones y noticias de la plataforma.</p>
        <div>
            <button type="button" class="btn btn-primary px-4 py-2" data-bs-toggle="modal" data-bs-target="#uploadModal">
                <i class="bi bi-cloud-arrow-up-fill me-1"></i> Subir primer archivo
            </button>
        </div>
    </div>
@else
    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4 mb-4">
        @foreach($media as $item)
            <div class="col">
                <article class="media-card">
                    <div class="media-card-img-container position-relative">
                        <img src="{{ Storage::url($item->path) }}" alt="{{ $item->name }}" loading="lazy">
                        <span class="badge bg-dark bg-opacity-75 position-absolute top-0 end-0 m-2 font-monospace small">
                            ID: #{{ $item->id }}
                        </span>
                    </div>
                    <div class="card-body p-3 d-flex flex-column flex-grow-1">
                        <h5 class="card-title text-truncate fw-bold mb-1" title="{{ $item->name }}">
                            {{ $item->name }}
                        </h5>
                        <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
                            <span class="badge bg-info-subtle text-info-emphasis border border-info-subtle">
                                <i class="bi bi-file-earmark-image me-1"></i>{{ $item->mime_type ?? 'image/jpeg' }}
                            </span>
                            <span class="badge bg-secondary-subtle text-secondary-emphasis border border-secondary-subtle">
                                <i class="bi bi-hdd me-1"></i>{{ $item->formatted_size }}
                            </span>
                        </div>
                        <p class="text-muted small mb-3 text-truncate" title="{{ $item->path }}">
                            <i class="bi bi-folder2 me-1"></i><code>{{ $item->path }}</code>
                        </p>

                        <div class="mt-auto pt-2 border-top d-flex gap-2 justify-content-between align-items-center">
                            <a href="{{ Storage::url($item->path) }}" target="_blank" class="btn btn-sm btn-outline-primary" title="Ver archivo original">
                                <i class="bi bi-box-arrow-up-right"></i> Ver
                            </a>
                            <div class="d-flex gap-1">
                                <!-- Botón Reemplazar -->
                                <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#replaceModal-{{ $item->id }}" title="Reemplazar archivo">
                                    <i class="bi bi-arrow-repeat"></i> Reemplazar
                                </button>
                                <!-- Formulario Eliminar -->
                                <form action="{{ route('admin.media.destroy', $item) }}" method="POST" onsubmit="return confirm('¿Estás seguro de eliminar este archivo permanentemente? Esta acción borrará el archivo de storage.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Eliminar permanentemente">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </article>

                <!-- Modal de Reemplazo para cada medio -->
                <div class="modal fade" id="replaceModal-{{ $item->id }}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content border-0 shadow">
                            <div class="modal-header bg-dark text-white">
                                <h5 class="modal-title">
                                    <i class="bi bi-arrow-repeat me-2"></i>Reemplazar Imagen #{{ $item->id }}
                                </h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <form action="{{ route('admin.media.update', $item) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                <div class="modal-body p-4">
                                    <div class="text-center mb-3">
                                        <img src="{{ Storage::url($item->path) }}" alt="{{ $item->name }}" class="img-thumbnail" style="max-height: 120px;">
                                        <div class="text-muted small mt-1">Archivo actual: {{ $item->path }}</div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Nombre del Recurso</label>
                                        <input type="text" class="form-control" name="name" value="{{ $item->name }}" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Nuevo Archivo de Imagen (opcional para sólo renombrar)</label>
                                        <input type="file" class="form-control" name="file" accept="image/*">
                                        <div class="form-text text-muted">Al subir una nueva imagen, el archivo anterior será eliminado físicamente del disco.</div>
                                    </div>
                                </div>
                                <div class="modal-footer bg-light">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                    <button type="submit" class="btn btn-dark">Guardar Cambios</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Paginación -->
    <div class="d-flex justify-content-center">
        {{ $media->links() }}
    </div>
@endif
@endsection
