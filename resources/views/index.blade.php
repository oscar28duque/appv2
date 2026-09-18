<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Eventos & Equipos') }} - Alquiler de Equipos, Mobiliario y Logística para Eventos</title>
    <!-- Google Fonts & Bootstrap 5.3 -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        :root {
            --cms-primary: #022766;
            --cms-primary-dark: #01163e;
            --cms-accent: #0ea5e9;
            --cms-accent-hover: #0284c7;
            --cms-success: #10b981;
            --cms-card-border: #e2e8f0;
            --cms-bg: #f8fafc;
        }
        body {
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
            color: #334155;
            background-color: var(--cms-bg);
            overflow-x: hidden;
        }
        h1, h2, h3, h4, h5, h6, .brand-title {
            font-family: 'Outfit', sans-serif;
        }
        .cms-mode-bar {
            background: #090d16;
            border-bottom: 1px solid rgba(255,255,255,0.08);
            font-size: 0.82rem;
            color: #94a3b8;
        }
        .navbar-public {
            background: rgba(15, 23, 42, 0.96) !important;
            backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        }
        .hero-banner {
            background: radial-gradient(circle at 80% 20%, rgba(14, 165, 233, 0.22) 0%, transparent 50%),
                        linear-gradient(135deg, #022766 0%, #073587 50%, #0b192e 100%);
            color: white;
            padding: 95px 0 85px;
            position: relative;
        }
        .hero-banner::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 40px;
            background: linear-gradient(to top, var(--cms-bg), transparent);
        }
        .stat-badge {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 12px;
            padding: 12px 18px;
            color: white;
            transition: transform 0.2s ease;
        }
        .stat-badge:hover {
            transform: translateY(-3px);
            background: rgba(255, 255, 255, 0.16);
        }
        .category-pill {
            border-radius: 30px;
            padding: 8px 18px;
            font-size: 0.88rem;
            font-weight: 600;
            border: 1px solid #cbd5e1;
            background: white;
            color: #475569;
            text-decoration: none;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            display: inline-flex;
            align-items: center;
            gap: 6px;
            white-space: nowrap;
        }
        .category-pill:hover, .category-pill.active {
            background: var(--cms-primary);
            color: white !important;
            border-color: var(--cms-primary);
            box-shadow: 0 4px 12px rgba(2, 39, 102, 0.25);
            transform: translateY(-2px);
        }
        .equipment-card {
            border-radius: 16px;
            overflow: hidden;
            border: 1px solid var(--cms-card-border);
            background: white;
            box-shadow: 0 4px 14px -2px rgba(15, 23, 42, 0.05);
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            height: 100%;
            display: flex;
            flex-direction: column;
        }
        .equipment-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 16px 30px -6px rgba(2, 39, 102, 0.15);
            border-color: #93c5fd;
        }
        .equipment-img-container {
            height: 200px;
            background: #f1f5f9;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            position: relative;
        }
        .equipment-img-container img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.4s ease;
        }
        .equipment-card:hover .equipment-img-container img {
            transform: scale(1.06);
        }
        .rate-box {
            background: #f8fafc;
            border-radius: 10px;
            padding: 10px 14px;
            border: 1px solid #e2e8f0;
        }
        .search-box-wrap {
            position: relative;
            box-shadow: 0 10px 25px -5px rgba(2, 39, 102, 0.08);
            border-radius: 14px;
        }
        .search-box-wrap .form-control {
            border-radius: 14px;
            padding: 14px 20px 14px 50px;
            border: 2px solid #e2e8f0;
            font-size: 1rem;
        }
        .search-box-wrap .form-control:focus {
            border-color: var(--cms-accent);
            box-shadow: 0 0 0 4px rgba(14, 165, 233, 0.15);
        }
        .search-box-wrap .search-icon {
            position: absolute;
            left: 18px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 1.2rem;
            color: #94a3b8;
            pointer-events: none;
        }
        .floating-badge {
            font-size: 0.75rem;
            font-weight: 700;
            letter-spacing: 0.5px;
            padding: 5px 10px;
            border-radius: 8px;
            backdrop-filter: blur(6px);
        }
        .scroll-pill-container {
            display: flex;
            gap: 8px;
            overflow-x: auto;
            padding-bottom: 8px;
            scrollbar-width: thin;
        }
        .btn-whatsapp {
            background-color: #25D366;
            color: white;
            border: none;
            font-weight: 600;
            transition: all 0.2s ease;
        }
        .btn-whatsapp:hover {
            background-color: #1eb954;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(37, 211, 102, 0.35);
        }
    </style>
</head>
<body>

    <!-- Barra de Modo CMS / Conexión entre Web Pública y Administración -->
    <div class="cms-mode-bar py-2 px-3">
        <div class="container d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-primary bg-opacity-25 text-info border border-info border-opacity-25 px-2 py-1">
                    <i class="bi bi-globe me-1"></i> Portal Web Comercial (CMS)
                </span>
                <span class="d-none d-md-inline text-secondary small">Vista para clientes y cotizaciones en línea</span>
            </div>
            <div class="d-flex align-items-center gap-3">
                <a href="#contacto" class="text-light text-decoration-none small d-flex align-items-center gap-1">
                    <i class="bi bi-whatsapp text-success"></i> WhatsApp de Alquiler: 320 489 2865
                </a>
                <span class="text-secondary opacity-50">|</span>
                @auth
                    <a href="{{ route('dashboard') }}" class="btn btn-sm btn-primary px-3 py-1 fw-semibold d-flex align-items-center gap-1">
                        <i class="bi bi-speedometer2"></i> Ir al Panel de Admin
                    </a>
                @else
                    <a href="{{ route('login') }}" class="btn btn-sm btn-outline-info px-3 py-1 fw-semibold d-flex align-items-center gap-1">
                        <i class="bi bi-lock-fill"></i> Acceso Administrador (CMS)
                    </a>
                @endauth
            </div>
        </div>
    </div>

    <!-- Navegación Principal del Sitio -->
    <nav class="navbar navbar-expand-lg navbar-dark navbar-public sticky-top py-3">
        <div class="container">
            <a class="navbar-brand fw-bold d-flex align-items-center gap-2" href="{{ url('/') }}">
                <div class="bg-primary rounded-3 p-2 d-flex align-items-center justify-content-center text-white" style="width: 38px; height: 38px;">
                    <i class="bi bi-boxes fs-5"></i>
                </div>
                <div class="d-flex flex-column">
                    <span class="brand-title fs-5 fw-bold text-white lh-1">{{ config('app.name', 'Eventos & Equipos') }}</span>
                    <small class="text-info fw-semibold" style="font-size: 0.68rem; letter-spacing: 1px;">ALQUILER & LOGÍSTICA</small>
                </div>
            </a>
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navMain">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navMain">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-4 gap-1">
                    <li class="nav-item">
                        <a class="nav-link {{ empty($category) && empty($search) ? 'active fw-bold text-white' : '' }}" href="{{ url('/') }}">Inicio</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#equipos">Catálogo de Alquiler</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#beneficios">¿Por qué Elegirnos?</a>
                    </li>
                    @if(!empty($news) && $news->isNotEmpty())
                        <li class="nav-item">
                            <a class="nav-link" href="#noticias">Noticias & Eventos</a>
                        </li>
                    @endif
                    <li class="nav-item">
                        <a class="nav-link" href="#contacto">Cotizador / Contacto</a>
                    </li>
                </ul>
                <div class="d-flex gap-2 align-items-center">
                    <a href="#contacto" class="btn btn-info text-white fw-bold px-3 shadow-sm d-flex align-items-center gap-1">
                        <i class="bi bi-calendar2-check"></i> Cotizar Ahora
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Banner -->
    <header class="hero-banner">
        <div class="container">
            <div class="row align-items-center g-5">
                <div class="col-lg-7 text-center text-lg-start">
                    <span class="badge bg-info text-dark px-3 py-2 rounded-pill fw-bold mb-3 shadow-sm">
                        <i class="bi bi-patch-check-fill text-primary me-1"></i> Catálogo Completo para Eventos y Logística
                    </span>
                    <h1 class="display-4 fw-extrabold mb-3 lh-sm">
                        Alquiler de Carpas, Mobiliario, Sonido, Luces, Herramientas & Conectividad
                    </h1>
                    <p class="lead text-light opacity-90 mb-4 fw-normal">
                        Desde carpas 2x2, hangares monumentales 20x8, sillas y mesones de madera, hasta internet satelital Starlink, guirnaldas vintage y bafles de alta potencia. Disponibilidad garantizada en tiempo real.
                    </p>
                    <div class="d-flex gap-3 justify-content-center justify-content-lg-start flex-wrap mb-4">
                        <a href="#equipos" class="btn btn-info text-white btn-lg px-4 fw-bold shadow-lg">
                            <i class="bi bi-grid-3x3-gap-fill me-1"></i> Explorar los {{ $items->count() }} Artículos
                        </a>
                        <a href="#contacto" class="btn btn-outline-light btn-lg px-4 fw-semibold">
                            <i class="bi bi-calculator me-1"></i> Cotizar Pedido
                        </a>
                    </div>
                    <!-- Badges de Métricas Rápidas -->
                    <div class="row g-2 text-start pt-2">
                        <div class="col-4">
                            <div class="stat-badge">
                                <div class="fs-4 fw-bold text-info">{{ $items->count() }}</div>
                                <div class="small opacity-75">Equipos Listos</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="stat-badge">
                                <div class="fs-4 fw-bold text-warning">{{ count($categories) }}</div>
                                <div class="small opacity-75">Categorías</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="stat-badge">
                                <div class="fs-4 fw-bold text-success">100%</div>
                                <div class="small opacity-75">Sin Sobrecupo</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-5">
                    <div class="p-4 bg-white bg-opacity-10 rounded-4 border border-white border-opacity-15 shadow-2-strong text-white" style="backdrop-filter: blur(10px);">
                        <h4 class="fw-bold mb-3 d-flex align-items-center gap-2">
                            <i class="bi bi-stars text-warning fs-3"></i> Servicios Destacados
                        </h4>
                        <div class="d-flex flex-column gap-3 mb-4">
                            <div class="d-flex align-items-start gap-3">
                                <div class="bg-info bg-opacity-25 p-2 rounded-3 text-info">
                                    <i class="bi bi-shield-check fs-4"></i>
                                </div>
                                <div>
                                    <strong class="d-block text-white">Tarifas Transparentes</strong>
                                    <small class="text-light opacity-75">Tarifas por día hábil y tarifa plana especial de fin de semana para optimizar tu presupuesto.</small>
                                </div>
                            </div>
                            <div class="d-flex align-items-start gap-3">
                                <div class="bg-warning bg-opacity-25 p-2 rounded-3 text-warning">
                                    <i class="bi bi-truck fs-4"></i>
                                </div>
                                <div>
                                    <strong class="d-block text-white">Logística & Despacho</strong>
                                    <small class="text-light opacity-75">Entrega puntual, soporte técnico en sitio y calibración antes de cada entrega.</small>
                                </div>
                            </div>
                            <div class="d-flex align-items-start gap-3">
                                <div class="bg-success bg-opacity-25 p-2 rounded-3 text-success">
                                    <i class="bi bi-clock-history fs-4"></i>
                                </div>
                                <div>
                                    <strong class="d-block text-white">Inventario en Vivo</strong>
                                    <small class="text-light opacity-75">Consulta el stock real para el día de hoy antes de realizar tu reserva.</small>
                                </div>
                            </div>
                        </div>
                        <a href="#equipos" class="btn btn-light w-100 fw-bold text-dark py-2">
                            <i class="bi bi-arrow-down-circle me-1"></i> Ver Catálogo Completo
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Catálogo de Alquiler Interactivo (Requerimiento 3.2 y 3.4) -->
    <section id="equipos" class="py-5">
        <div class="container py-3">
            
            <div class="d-flex justify-content-between align-items-end mb-4 flex-wrap gap-3">
                <div>
                    <span class="text-primary fw-bold text-uppercase tracking-wider small d-block mb-1">
                        <i class="bi bi-collection-fill me-1"></i> Inventario Disponible para Alquiler
                    </span>
                    <h2 class="fw-extrabold text-dark mb-0 fs-1">Catálogo de Equipos</h2>
                    <p class="text-muted mb-0">Selecciona una categoría o busca por nombre de artículo para cotizar.</p>
                </div>
                <!-- Barra de Búsqueda -->
                <div style="min-width: 280px; max-width: 380px;" class="w-100">
                    <form method="GET" action="{{ url('/#equipos') }}">
                        @if(!empty($category))
                            <input type="hidden" name="category" value="{{ $category }}">
                        @endif
                        <div class="search-box-wrap">
                            <i class="bi bi-search search-icon"></i>
                            <input type="text" name="search" class="form-control" placeholder="Buscar carpas, sillas, bafles..." value="{{ $search ?? '' }}">
                            @if(!empty($search))
                                <a href="{{ url('/?category=' . urlencode($category ?? '') . '#equipos') }}" class="position-absolute end-0 top-50 translate-middle-y me-3 text-muted text-decoration-none">
                                    <i class="bi bi-x-circle-fill"></i>
                                </a>
                            @endif
                        </div>
                    </form>
                </div>
            </div>

            <!-- Filtro de Categorías con Scroll Horizontal -->
            <div class="scroll-pill-container mb-4">
                <a href="{{ url('/' . (empty($search) ? '#equipos' : '?search=' . urlencode($search) . '#equipos')) }}" 
                   class="category-pill {{ empty($category) ? 'active' : '' }}">
                    <i class="bi bi-grid-fill"></i> Todos ({{ $items->count() }})
                </a>
                @foreach($categories as $cat)
                    <a href="{{ url('/?category=' . urlencode($cat) . (!empty($search) ? '&search=' . urlencode($search) : '') . '#equipos') }}" 
                       class="category-pill {{ $category === $cat ? 'active' : '' }}">
                        @if(str_contains($cat, 'Carpa')) <i class="bi bi-triangle"></i>
                        @elseif(str_contains($cat, 'Mobiliario')) <i class="bi bi-table"></i>
                        @elseif(str_contains($cat, 'Mantelería') || str_contains($cat, 'Decoración')) <i class="bi bi-stars"></i>
                        @elseif(str_contains($cat, 'Audio')) <i class="bi bi-speaker"></i>
                        @elseif(str_contains($cat, 'Iluminación')) <i class="bi bi-lightbulb"></i>
                        @elseif(str_contains($cat, 'Herramientas')) <i class="bi bi-tools"></i>
                        @elseif(str_contains($cat, 'Tecnología')) <i class="bi bi-wifi"></i>
                        @elseif(str_contains($cat, 'Camping') || str_contains($cat, 'Viajes')) <i class="bi bi-compass"></i>
                        @elseif(str_contains($cat, 'Estructura')) <i class="bi bi-layers"></i>
                        @elseif(str_contains($cat, 'Video')) <i class="bi bi-display"></i>
                        @else <i class="bi bi-tag"></i>
                        @endif
                        {{ $cat }}
                    </a>
                @endforeach
            </div>

            @if($items->isEmpty())
                <div class="card border-0 shadow-sm p-5 text-center bg-white rounded-4 my-4">
                    <div class="text-muted mb-3"><i class="bi bi-search fs-1 text-primary opacity-50"></i></div>
                    <h4 class="fw-bold text-dark">No se encontraron artículos disponibles</h4>
                    <p class="text-muted mb-3">No hay equipos que coincidan con "{{ $search ?? $category }}".</p>
                    <div>
                        <a href="{{ url('/#equipos') }}" class="btn btn-primary px-4 py-2 fw-semibold">
                            <i class="bi bi-arrow-left me-1"></i> Ver Todo el Catálogo
                        </a>
                    </div>
                </div>
            @else
                <!-- Grid de Artículos -->
                <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4" id="catalog-grid">
                    @foreach($items as $eq)
                        <div class="col item-card-wrapper" data-category="{{ $eq->category }}" data-name="{{ strtolower($eq->name) }}">
                            <article class="equipment-card">
                                <div class="equipment-img-container">
                                    @if($eq->media)
                                        <img src="{{ Storage::url($eq->media->path) }}" alt="{{ $eq->name }}" loading="lazy">
                                    @else
                                        <div class="w-100 h-100 d-flex align-items-center justify-content-center text-muted">
                                            <i class="bi bi-box-seam fs-1 opacity-50"></i>
                                        </div>
                                    @endif
                                    <!-- Badges superiores -->
                                    <span class="badge bg-dark bg-opacity-80 text-white position-absolute top-0 start-0 m-2 font-monospace floating-badge">
                                        {{ $eq->code }}
                                    </span>
                                    <span class="badge bg-primary position-absolute top-0 end-0 m-2 floating-badge shadow-sm">
                                        {{ $eq->category }}
                                    </span>
                                </div>

                                <div class="card-body p-3 d-flex flex-column flex-grow-1">
                                    <h5 class="card-title fw-bold text-dark fs-6 mb-2 text-truncate" title="{{ $eq->name }}">
                                        {{ $eq->name }}
                                    </h5>
                                    <p class="card-text text-muted small flex-grow-1 mb-3" style="min-height: 40px; font-size: 0.84rem; line-height: 1.45;">
                                        {{ Str::limit($eq->description ?? 'Artículo profesional para alquiler de eventos y logística con altos estándares técnicos.', 85) }}
                                    </p>

                                    <!-- Tarifas del Negocio -->
                                    <div class="rate-box mb-3">
                                        <div class="d-flex justify-content-between align-items-center small mb-1">
                                            <span class="text-muted">Día Hábil:</span>
                                            <strong class="text-primary fs-6">${{ number_format($eq->daily_rate, 0, ',', '.') }}</strong>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center small">
                                            <span class="text-muted">Tarifa Fin de Sem.:</span>
                                            <strong class="text-success fs-6">${{ number_format($eq->weekend_rate, 0, ',', '.') }}</strong>
                                        </div>
                                    </div>

                                    <!-- Disponibilidad en Tiempo Real -->
                                    <div class="d-flex align-items-center justify-content-between mb-3 small">
                                        <span class="text-muted" style="font-size: 0.8rem;">Disponibilidad hoy:</span>
                                        <span class="badge {{ $eq->available_today > 0 ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-danger-subtle text-danger' }} px-2 py-1">
                                            <i class="bi bi-check-circle me-1"></i>{{ $eq->available_today }} unid(s)
                                        </span>
                                    </div>

                                    <!-- Acciones -->
                                    <div class="d-flex gap-2 mt-auto">
                                        <a href="{{ route('items.show', $eq->code) }}" class="btn btn-sm btn-outline-primary w-50 fw-semibold">
                                            <i class="bi bi-info-circle"></i> Ficha
                                        </a>
                                        @php
                                            $waText = urlencode("Hola! Estoy interesado en cotizar el alquiler de: {$eq->name} (Código: {$eq->code})");
                                        @endphp
                                        <a href="https://wa.me/573204892865?text={{ $waText }}" target="_blank" class="btn btn-sm btn-whatsapp w-50 d-flex align-items-center justify-content-center gap-1 shadow-sm" title="Cotizar en WhatsApp">
                                            <i class="bi bi-whatsapp"></i> Cotizar
                                        </a>
                                    </div>
                                </div>
                            </article>
                        </div>
                    @endforeach
                </div>
            @endif

        </div>
    </section>

    <!-- Sección de Beneficios / Servicios del CMS -->
    <section id="beneficios" class="py-5 bg-white border-top border-bottom">
        <div class="container py-4">
            <div class="text-center mb-5">
                <span class="text-primary fw-bold text-uppercase tracking-wider small d-block mb-1">
                    Garantía y Logística Integral
                </span>
                <h2 class="fw-extrabold text-dark">¿Por qué Alquilar con Nosotros?</h2>
                <p class="text-muted mx-auto" style="max-width: 620px;">
                    Plataforma CMS especializada en logística de eventos que asegura stock exacto, cero sobrecupos y máxima transparencia en tarifas.
                </p>
            </div>

            <div class="row g-4">
                <div class="col-md-4">
                    <div class="p-4 rounded-4 bg-light h-100 border border-light-subtle text-center">
                        <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3 shadow" style="width: 60px; height: 60px;">
                            <i class="bi bi-calendar-check fs-3"></i>
                        </div>
                        <h5 class="fw-bold text-dark">Control de Disponibilidad</h5>
                        <p class="text-secondary small mb-0">
                            Nuestro motor calcula reservas activas día por día. Evitamos sobrecupos y te garantizamos que el equipo cotizado estará listo y reservado para tu fecha.
                        </p>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="p-4 rounded-4 bg-light h-100 border border-light-subtle text-center">
                        <div class="bg-success text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3 shadow" style="width: 60px; height: 60px;">
                            <i class="bi bi-currency-dollar fs-3"></i>
                        </div>
                        <h5 class="fw-bold text-dark">Tarifas Diferenciadas</h5>
                        <p class="text-secondary small mb-0">
                            Tarifas competitivas por día hábil y tarifa plana económica para fines de semana (sábado y domingo completo sin cargos ocultos).
                        </p>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="p-4 rounded-4 bg-light h-100 border border-light-subtle text-center">
                        <div class="bg-info text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3 shadow" style="width: 60px; height: 60px;">
                            <i class="bi bi-tools fs-3"></i>
                        </div>
                        <h5 class="fw-bold text-dark">Mantenimiento Certificado</h5>
                        <p class="text-secondary small mb-0">
                            Todo el equipamiento (carpas, cableado, consolas, herramientas e hidrolavadoras) se somete a protocolo de inspección y limpieza antes de cada entrega.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Sección de Noticias Institucionales (CMS Reto B) -->
    @if(!empty($news) && $news->isNotEmpty())
        <section id="noticias" class="py-5 bg-light">
            <div class="container py-3">
                <div class="d-flex justify-content-between align-items-end mb-4 flex-wrap gap-2">
                    <div>
                        <span class="text-primary fw-bold text-uppercase tracking-wider small d-block mb-1">Comunicaciones</span>
                        <h2 class="fw-extrabold text-dark mb-0">Novedades & Eventos Recientes</h2>
                    </div>
                    <a href="{{ route('admin.news.index') }}" class="btn btn-sm btn-outline-primary d-flex align-items-center gap-1">
                        <i class="bi bi-newspaper"></i> Administrar Noticias (CMS)
                    </a>
                </div>

                <div class="row row-cols-1 row-cols-md-3 g-4">
                    @foreach($news as $n)
                        <div class="col">
                            <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden bg-white">
                                <div style="height: 180px; overflow: hidden; background: #e2e8f0;">
                                    @if($n->media)
                                        <img src="{{ Storage::url($n->media->path) }}" alt="{{ $n->title }}" class="w-100 h-100 object-fit-cover">
                                    @else
                                        <div class="w-100 h-100 d-flex align-items-center justify-content-center text-muted">
                                            <i class="bi bi-newspaper fs-1 opacity-50"></i>
                                        </div>
                                    @endif
                                </div>
                                <div class="card-body p-4 d-flex flex-column">
                                    <small class="text-muted mb-2 d-flex align-items-center gap-1">
                                        <i class="bi bi-calendar3"></i> {{ $n->created_at->format('d/m/Y') }}
                                    </small>
                                    <h5 class="fw-bold text-dark mb-2">{{ $n->title }}</h5>
                                    <p class="text-secondary small flex-grow-1">{{ $n->excerpt ?? Str::limit(strip_tags($n->content), 100) }}</p>
                                    <a href="{{ route('news.show', $n->slug) }}" class="btn btn-sm btn-outline-primary mt-auto fw-semibold">
                                        Leer Noticia Completa &rarr;
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <!-- Formulario de Cotización y Solicitud Directa (Requerimiento 3.3) -->
    <section id="contacto" class="py-5 bg-white border-top">
        <div class="container py-4">
            <div class="row justify-content-center">
                <div class="col-lg-9 col-xl-8">
                    <div class="text-center mb-4">
                        <span class="badge bg-primary text-white px-3 py-2 rounded-pill fw-bold mb-2 shadow-sm">
                            <i class="bi bi-calculator-fill me-1"></i> Cotizador & Solicitud de Alquiler
                        </span>
                        <h2 class="fw-extrabold text-dark fs-1">Cotiza tu Evento al Instante</h2>
                        <p class="text-muted mx-auto" style="max-width: 600px;">
                            Selecciona el equipo requerido, indica tus fechas y nuestro equipo comercial te enviará una confirmación automática con disponibilidad confirmada.
                        </p>
                    </div>

                    <div class="card border-0 shadow-lg p-4 p-md-5 bg-white rounded-4 border-top border-4 border-primary">
                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 mb-4" role="alert">
                                <i class="bi bi-check-circle-fill fs-5"></i>
                                <div>{{ session('success') }}</div>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2 mb-4" role="alert">
                                <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                                <div>{{ session('error') }}</div>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if ($errors->any())
                            <div class="alert alert-danger mb-4">
                                <ul class="mb-0 ps-3">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('contact.send') }}" method="POST" id="quoteForm">
                            @csrf
                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label for="client_name" class="form-label fw-bold small text-dark">Nombre Completo o Empresa <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="bi bi-person"></i></span>
                                        <input type="text" class="form-control" id="client_name" name="name" placeholder="Ej: Juan Pérez / Eventos SAS" value="{{ old('name') }}" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label for="client_email" class="form-label fw-bold small text-dark">Correo Electrónico <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="bi bi-envelope"></i></span>
                                        <input type="email" class="form-control" id="client_email" name="email" placeholder="correo@ejemplo.com" value="{{ old('email') }}" required>
                                    </div>
                                </div>
                            </div>

                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label for="select_item" class="form-label fw-bold small text-dark">Equipo Principal a Alquilar</label>
                                    <select class="form-select" id="select_item">
                                        <option value="">-- Seleccionar del catálogo (Opcional) --</option>
                                        @foreach($items as $eqOption)
                                            <option value="{{ $eqOption->name }} ({{ $eqOption->code }})" data-daily="{{ $eqOption->daily_rate }}" data-weekend="{{ $eqOption->weekend_rate }}">
                                                {{ $eqOption->name }} - ${{ number_format($eqOption->daily_rate, 0, ',', '.') }}/día
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="pricing_plan" class="form-label fw-bold small text-dark">Tipo de Tarifa</label>
                                    <select class="form-select" id="pricing_plan">
                                        <option value="daily">Día Hábil (24 Horas)</option>
                                        <option value="weekend">Fin de Semana (Tarifa Plana Especial)</option>
                                    </select>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="client_message" class="form-label fw-bold small text-dark">Detalle del Evento, Cantidades y Ubicación <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="client_message" name="message" rows="4" placeholder="Ej: Necesito 2 carpas 4x4, 40 sillas plásticas blancas, 5 mesones de madera y 1 bafle para una boda en Chía el fin de semana del 15 de Octubre..." required>{{ old('message') }}</textarea>
                            </div>

                            <div class="d-flex gap-3 flex-column flex-sm-row">
                                <button type="submit" class="btn btn-primary flex-grow-1 py-3 fw-bold d-flex align-items-center justify-content-center gap-2 shadow">
                                    <i class="bi bi-send-fill"></i> Enviar Solicitud de Cotización
                                </button>
                                <button type="button" id="btnWhatsappQuote" class="btn btn-whatsapp px-4 py-3 fw-bold d-flex align-items-center justify-content-center gap-2 shadow-sm">
                                    <i class="bi bi-whatsapp fs-5"></i> Cotizar por WhatsApp
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-white py-5 border-top border-secondary border-opacity-25" style="background: #090d16 !important;">
        <div class="container">
            <div class="row g-4 mb-4">
                <div class="col-lg-4">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <div class="bg-primary rounded-3 p-2 text-white d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                            <i class="bi bi-boxes fs-5"></i>
                        </div>
                        <h5 class="fw-bold mb-0 text-white brand-title">{{ config('app.name', 'Eventos & Equipos') }}</h5>
                    </div>
                    <p class="text-secondary small">
                        Plataforma CMS profesional para la gestión integral de alquiler de equipos, carpas, mobiliario, sonido e infraestructura técnica de eventos.
                    </p>
                    <div class="d-flex gap-2">
                        <a href="{{ route('dashboard') }}" class="btn btn-sm btn-outline-light d-inline-flex align-items-center gap-1">
                            <i class="bi bi-speedometer2"></i> Acceso CMS
                        </a>
                        <a href="{{ route('login') }}" class="btn btn-sm btn-outline-info">Login</a>
                    </div>
                </div>

                <div class="col-sm-6 col-lg-4">
                    <h6 class="text-white fw-bold mb-3">Categorías Principales</h6>
                    <div class="row g-1 small text-secondary">
                        <div class="col-6">
                            <ul class="list-unstyled">
                                <li>&bull; Carpas y Toldos</li>
                                <li>&bull; Mobiliario para Eventos</li>
                                <li>&bull; Mantelería y Gala</li>
                                <li>&bull; Sonido & Bafles</li>
                                <li>&bull; Iluminación & Guirnaldas</li>
                            </ul>
                        </div>
                        <div class="col-6">
                            <ul class="list-unstyled">
                                <li>&bull; Herramientas & Obras</li>
                                <li>&bull; Starlink Satelital</li>
                                <li>&bull; Camping y Vivac</li>
                                <li>&bull; Pantallas LED & Video</li>
                                <li>&bull; Estructuras Truss</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-lg-4">
                    <h6 class="text-white fw-bold mb-3">Contacto & Soporte</h6>
                    <ul class="list-unstyled small text-secondary d-flex flex-column gap-2">
                        <li><i class="bi bi-geo-alt text-primary me-2"></i> Zona Industrial de Eventos, Bogotá / Cobertura Nacional</li>
                        <li><i class="bi bi-whatsapp text-success me-2"></i> +57 320 489 2865</li>
                        <li><i class="bi bi-envelope text-info me-2"></i> {{ config('mail.admin_address', 'oscarduquegarcia@outlook.com') }}</li>
                        <li><i class="bi bi-clock text-warning me-2"></i> Lunes a Domingo: 7:00 AM - 9:00 PM</li>
                    </ul>
                </div>
            </div>

            <div class="border-top border-secondary border-opacity-25 pt-4 text-center text-secondary small">
                &copy; {{ date('Y') }} {{ config('app.name', 'Eventos & Equipos') }}. Sistema CMS de Alquiler y Logística de Eventos. Desarrollado con Laravel, MySQL y Bootstrap.
            </div>
        </div>
    </footer>

    <!-- Bootstrap Bundle JS & Quick Script -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Enlazar selección rápida del cotizador con el textarea
        document.getElementById('select_item')?.addEventListener('change', function() {
            const textarea = document.getElementById('client_message');
            if (this.value) {
                const plan = document.getElementById('pricing_plan').value === 'weekend' ? 'Fin de Semana' : 'Día Hábil';
                const currentText = textarea.value.trim();
                const prefix = `Deseo cotizar: ${this.value} con tarifa de ${plan}.`;
                textarea.value = currentText ? `${currentText}\n- ${prefix}` : prefix;
            }
        });

        // Botón cotizar por WhatsApp directo
        document.getElementById('btnWhatsappQuote')?.addEventListener('click', function() {
            const name = document.getElementById('client_name').value.trim() || 'Cliente';
            const msg = document.getElementById('client_message').value.trim() || 'Deseo cotizar equipos de alquiler.';
            const text = `Hola, mi nombre es ${name}. ${msg}`;
            window.open('https://wa.me/573204892865?text=' + encodeURIComponent(text), '_blank');
        });
    </script>
</body>
</html>
