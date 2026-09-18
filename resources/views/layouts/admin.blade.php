<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Panel de Control') | {{ config('app.name', 'CMS Eventos') }}</title>
    <!-- Bootstrap 5.3 & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        :root {
            --cms-primary: #022766;
            --cms-secondary: #0ea5e9;
            --cms-dark: #0f172a;
            --cms-light: #f8fafc;
        }
        body {
            background-color: #f1f5f9;
            font-family: system-ui, -apple-system, "Segoe UI", Roboto, sans-serif;
            color: #334155;
            min-height: 100vh;
        }
        .navbar-cms {
            background: linear-gradient(135deg, #022766 0%, #083b96 100%);
            box-shadow: 0 4px 12px rgba(2, 39, 102, 0.15);
        }
        .nav-link.active-link {
            background-color: rgba(255, 255, 255, 0.18);
            border-radius: 6px;
            font-weight: 600;
        }
        .stat-card {
            border-radius: 12px;
            border: none;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
            transition: transform 0.2s ease;
        }
        .stat-card:hover {
            transform: translateY(-2px);
        }
        .media-card {
            background: #fff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.06);
            border: 1px solid #e2e8f0;
            height: 100%;
            display: flex;
            flex-direction: column;
        }
        .media-card-img-container {
            height: 180px;
            background: #f8fafc;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            border-bottom: 1px solid #e2e8f0;
        }
        .media-card-img-container img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
    </style>
    @stack('styles')
</head>
<body>
    <!-- Navbar del CMS para Alquiler de Equipos y Logística de Eventos -->
    <nav class="navbar navbar-expand-xl navbar-dark navbar-cms sticky-top py-2">
        <div class="container-fluid px-4">
            <a class="navbar-brand fw-bold d-flex align-items-center gap-2" href="{{ route('dashboard') }}">
                <i class="bi bi-speaker-fill fs-4 text-info"></i>
                <span>{{ config('app.name', 'Eventos & Equipos') }} <small class="fw-normal opacity-75 fs-6">CMS</small></span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navContent">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navContent">
                <ul class="navbar-nav me-auto mb-2 mb-xl-0 ms-xl-3 gap-1">
                    <li class="nav-item">
                        <a class="nav-link px-3 {{ request()->routeIs('dashboard') ? 'active active-link' : '' }}" href="{{ route('dashboard') }}">
                            <i class="bi bi-speedometer2 me-1"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-3 {{ request()->routeIs('admin.items.*') ? 'active active-link' : '' }}" href="{{ route('admin.items.index') }}">
                            <i class="bi bi-box-seam me-1"></i> Inventario
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-3 {{ request()->routeIs('admin.reservations.*') ? 'active active-link' : '' }}" href="{{ route('admin.reservations.index') }}">
                            <i class="bi bi-calendar-event me-1"></i> Reservas
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-3 {{ request()->routeIs('admin.clients.*') ? 'active active-link' : '' }}" href="{{ route('admin.clients.index') }}">
                            <i class="bi bi-people me-1"></i> Clientes
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-3 {{ request()->routeIs('admin.finances.*') ? 'active active-link' : '' }}" href="{{ route('admin.finances.index') }}">
                            <i class="bi bi-cash-coin me-1"></i> Cuentas & Finanzas
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-3 {{ request()->routeIs('admin.staff.*') ? 'active active-link' : '' }}" href="{{ route('admin.staff.index') }}">
                            <i class="bi bi-person-badge me-1"></i> Personal / Nómina
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-3 {{ request()->routeIs('admin.media.*') ? 'active active-link' : '' }}" href="{{ route('admin.media.index') }}">
                            <i class="bi bi-images me-1"></i> Multimedia
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-3 {{ request()->routeIs('admin.news.*') ? 'active active-link' : '' }}" href="{{ route('admin.news.index') }}">
                            <i class="bi bi-newspaper me-1"></i> Noticias
                        </a>
                    </li>
                </ul>
                <div class="d-flex align-items-center gap-3">
                    <a href="{{ url('/') }}" target="_blank" class="btn btn-sm btn-outline-light d-flex align-items-center gap-1">
                        <i class="bi bi-box-arrow-up-right"></i> Ver Portal Web
                    </a>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-light dropdown-toggle d-flex align-items-center gap-2" type="button" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle"></i>
                            <span class="fw-medium">{{ auth()->user()->name ?? 'Administrador' }}</span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                            <li><span class="dropdown-item-text text-muted small">{{ auth()->user()->email ?? '' }}</span></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger d-flex align-items-center gap-2">
                                        <i class="bi bi-box-arrow-right"></i> Cerrar sesión
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Contenedor Principal -->
    <main class="container-fluid px-4 py-4">
        <!-- Flash Alerts -->
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 d-flex align-items-center" role="alert">
                <i class="bi bi-check-circle-fill me-2 fs-5"></i>
                <div>{{ session('success') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 d-flex align-items-center" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
                <div>{{ session('error') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-warning alert-dismissible fade show shadow-sm border-0" role="alert">
                <div class="d-flex align-items-center mb-2">
                    <i class="bi bi-exclamation-circle-fill me-2 fs-5"></i>
                    <strong>Por favor corrige los siguientes errores:</strong>
                </div>
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Bootstrap Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
