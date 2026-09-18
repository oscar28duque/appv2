<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $noticia->title }} | {{ config('app.name') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { background-color: #f8fafc; font-family: system-ui, -apple-system, sans-serif; }
        .hero-title { font-weight: 800; letter-spacing: -0.5px; }
        .article-img { max-height: 480px; width: 100%; object-fit: cover; border-radius: 12px; }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark py-3">
        <div class="container">
            <a class="navbar-brand fw-bold d-flex align-items-center gap-2" href="{{ url('/') }}">
                <i class="bi bi-shield-lock-fill text-primary fs-4"></i>
                <span>{{ config('app.name') }}</span>
            </a>
            <div class="d-flex gap-2">
                <a href="{{ url('/') }}" class="btn btn-outline-light btn-sm">&larr; Volver al inicio</a>
                @auth
                    <a href="{{ route('dashboard') }}" class="btn btn-primary btn-sm">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="btn btn-outline-primary btn-sm text-white">Ingresar</a>
                @endauth
            </div>
        </div>
    </nav>

    <!-- Contenido del Artículo -->
    <article class="container py-5 max-w-3xl" style="max-width: 860px;">
        <div class="mb-4">
            <span class="badge bg-primary px-3 py-2 rounded-pill mb-2">Publicación Oficial</span>
            <h1 class="hero-title text-dark display-5 mb-3">{{ $noticia->title }}</h1>
            <div class="text-muted d-flex align-items-center gap-3">
                <span><i class="bi bi-calendar3 me-1"></i>{{ $noticia->created_at->format('d de M, Y') }}</span>
                <span>&bull;</span>
                <span><i class="bi bi-link-45deg me-1"></i>/noticias/{{ $noticia->slug }}</span>
            </div>
        </div>

        <!-- Renderizado de Imagen según Sección 5.11 de la Guía -->
        @if($noticia->media)
            <figure class="mb-4">
                <img
                    src="{{ Storage::url($noticia->media->path) }}"
                    alt="{{ $noticia->title }}"
                    class="img-fluid article-img shadow"
                >
                <figcaption class="text-muted small text-center mt-2">
                    <i class="bi bi-camera me-1"></i> Archivo: <code>{{ $noticia->media->name }}</code> &bull; Servido desde <code>public/storage</code>
                </figcaption>
            </figure>
        @endif

        @if($noticia->excerpt)
            <div class="lead text-secondary fw-normal mb-4 p-3 bg-white rounded border-start border-4 border-primary shadow-sm">
                {{ $noticia->excerpt }}
            </div>
        @endif

        <div class="bg-white p-4 p-md-5 rounded-3 shadow-sm text-secondary fs-5" style="line-height: 1.8; white-space: pre-line;">
            {{ $noticia->content }}
        </div>

        <div class="mt-5 pt-4 border-top text-center">
            <a href="{{ url('/#noticias') }}" class="btn btn-primary px-4 py-2">
                <i class="bi bi-arrow-left me-1"></i> Ver más noticias
            </a>
        </div>
    </article>

    <footer class="bg-white border-top py-4 text-center text-muted small mt-5">
        <div class="container">
            &copy; {{ date('Y') }} {{ config('app.name') }} &bull; Gestión segura de imágenes y envío de correos en Laravel
        </div>
    </footer>
</body>
</html>
