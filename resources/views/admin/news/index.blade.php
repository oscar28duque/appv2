@extends('layouts.admin')

@section('title', 'Gestión de Publicaciones y Noticias')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h2 class="fw-bold text-dark mb-1">
            <i class="bi bi-newspaper text-primary me-2"></i>Módulo de Noticias
        </h2>
        <p class="text-muted mb-0">Publicaciones del CMS vinculadas con la biblioteca multimedia mediante <code>media_id</code></p>
    </div>
    <a href="{{ route('admin.news.create') }}" class="btn btn-primary d-flex align-items-center gap-2 shadow-sm">
        <i class="bi bi-plus-circle-fill fs-5"></i> Redactar Nueva Noticia
    </a>
</div>

@if($news->isEmpty())
    <div class="card border-0 shadow-sm p-5 text-center my-4 bg-white rounded-4">
        <div class="mb-3">
            <i class="bi bi-journal-text text-muted" style="font-size: 4rem;"></i>
        </div>
        <h4 class="fw-bold text-secondary">No hay publicaciones registradas</h4>
        <p class="text-muted">Crea tu primera noticia y asóciale una imagen desde la biblioteca de medios.</p>
        <div>
            <a href="{{ route('admin.news.create') }}" class="btn btn-primary px-4 py-2">
                <i class="bi bi-plus-circle me-1"></i> Redactar primera noticia
            </a>
        </div>
    </div>
@else
    <div class="card border-0 shadow-sm rounded-3 overflow-hidden bg-white">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th scope="col" style="width: 100px;">Imagen</th>
                        <th scope="col">Título & Extracto</th>
                        <th scope="col">Slug</th>
                        <th scope="col" style="width: 130px;">Estado</th>
                        <th scope="col" style="width: 140px;">Fecha</th>
                        <th scope="col" class="text-end" style="width: 180px;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($news as $item)
                        <tr>
                            <td>
                                @if($item->media)
                                    <img src="{{ Storage::url($item->media->path) }}" alt="{{ $item->title }}" class="rounded object-fit-cover shadow-sm" style="width: 80px; height: 55px;">
                                @else
                                    <div class="bg-light text-muted rounded d-flex align-items-center justify-content-center border" style="width: 80px; height: 55px; font-size: 11px;">
                                        <i class="bi bi-image me-1"></i> Sin imagen
                                    </div>
                                @endif
                            </td>
                            <td>
                                <div class="fw-bold text-dark">{{ $item->title }}</div>
                                <div class="text-muted small text-truncate" style="max-width: 450px;">
                                    {{ $item->excerpt ?? Str::limit(strip_tags($item->content), 80) }}
                                </div>
                            </td>
                            <td>
                                <code class="small">{{ $item->slug }}</code>
                            </td>
                            <td>
                                <form action="{{ route('admin.news.toggle', $item) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-sm p-0 border-0" title="Clic para alternar estado">
                                        @if($item->published)
                                            <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">
                                                <i class="bi bi-check-circle-fill me-1"></i> Publicado
                                            </span>
                                        @else
                                            <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2 py-1">
                                                <i class="bi bi-pause-circle me-1"></i> Borrador
                                            </span>
                                        @endif
                                    </button>
                                </form>
                            </td>
                            <td class="text-muted small">
                                {{ $item->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="text-end">
                                <div class="btn-group btn-group-sm">
                                    @if($item->published)
                                        <a href="{{ route('news.show', $item->slug) }}" target="_blank" class="btn btn-outline-info" title="Ver en portal público">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    @endif
                                    <a href="{{ route('admin.news.edit', $item) }}" class="btn btn-outline-secondary" title="Editar noticia">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('admin.news.destroy', $item) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Deseas eliminar esta publicación?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger" title="Eliminar noticia">
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

    <!-- Paginación -->
    <div class="d-flex justify-content-center mt-4">
        {{ $news->links() }}
    </div>
@endif
@endsection
