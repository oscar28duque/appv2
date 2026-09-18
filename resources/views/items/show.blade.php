<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $item->name }} ({{ $item->code }}) | Alquiler de Equipos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { background-color: #f8fafc; font-family: system-ui, -apple-system, sans-serif; }
        .hero-img { max-height: 420px; width: 100%; object-fit: cover; border-radius: 12px; }
        .rate-box { background: #f1f5f9; border-radius: 10px; border-left: 4px solid #022766; }
    </style>
</head>
<body>
    <nav class="navbar navbar-dark bg-dark py-3">
        <div class="container">
            <a class="navbar-brand fw-bold d-flex align-items-center gap-2" href="{{ url('/') }}">
                <i class="bi bi-speaker-fill text-primary fs-4"></i>
                <span>{{ config('app.name', 'Eventos & Equipos CMS') }}</span>
            </a>
            <div class="d-flex gap-2">
                <a href="{{ url('/#equipos') }}" class="btn btn-outline-light btn-sm">&larr; Catálogo</a>
                @auth
                    <a href="{{ route('dashboard') }}" class="btn btn-primary btn-sm">Dashboard</a>
                @endauth
            </div>
        </div>
    </nav>

    <div class="container py-5">
        <div class="row g-5">
            <div class="col-lg-6">
                @if($item->media)
                    <img src="{{ Storage::url($item->media->path) }}" alt="{{ $item->name }}" class="hero-img shadow">
                @else
                    <div class="bg-secondary bg-opacity-10 rounded d-flex align-items-center justify-content-center border" style="height: 380px;">
                        <i class="bi bi-image text-muted fs-1"></i>
                    </div>
                @endif
            </div>
            <div class="col-lg-6">
                <span class="badge bg-primary px-3 py-2 rounded-pill mb-2">{{ $item->category }}</span>
                <span class="badge bg-secondary px-3 py-2 rounded-pill mb-2 font-monospace">{{ $item->code }}</span>
                <h1 class="fw-bold text-dark mb-3">{{ $item->name }}</h1>

                <div class="rate-box p-3 mb-4">
                    <div class="row text-center">
                        <div class="col-6 border-end">
                            <small class="text-muted fw-bold d-block">TARIFA DÍA HÁBIL</small>
                            <span class="fs-4 fw-bold text-primary">${{ number_format($item->daily_rate, 2) }}</span>
                        </div>
                        <div class="col-6">
                            <small class="text-muted fw-bold d-block">TARIFA FIN DE SEMANA</small>
                            <span class="fs-4 fw-bold text-success">${{ number_format($item->weekend_rate, 2) }}</span>
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <h5 class="fw-bold text-dark">Descripción del Equipo</h5>
                    <p class="text-secondary" style="line-height: 1.7;">
                        {{ $item->description ?? 'Equipo profesional para eventos y espectáculos, probado y calibrado antes de cada despacho.' }}
                    </p>
                </div>

                <div class="card bg-white p-3 border rounded-3 mb-4">
                    <div class="d-flex align-items-center justify-content-between">
                        <span class="text-muted">Stock Total de la Empresa:</span>
                        <span class="fw-bold text-dark">{{ $item->total_quantity }} unidades</span>
                    </div>
                    <div class="d-flex align-items-center justify-content-between mt-2">
                        <span class="text-muted">Disponibilidad Hoy:</span>
                        <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">
                            {{ $item->available_today }} disponibles
                        </span>
                    </div>
                </div>

                <div class="d-flex flex-column gap-2">
                    <a href="{{ url('/#contacto') }}" class="btn btn-primary btn-lg w-100 py-3 fw-bold d-flex align-items-center justify-content-center gap-2 shadow-sm">
                        <i class="bi bi-calendar-check-fill"></i> Solicitar Alquiler / Cotización
                    </a>
                    @php
                        $waItemText = urlencode("Hola! Deseo cotizar el alquiler de {$item->name} (Código: {$item->code})");
                    @endphp
                    <a href="https://wa.me/573204892865?text={{ $waItemText }}" target="_blank" class="btn btn-success btn-lg w-100 py-3 fw-bold d-flex align-items-center justify-content-center gap-2 shadow-sm" style="background-color: #25D366; border: none;">
                        <i class="bi bi-whatsapp"></i> Cotizar este equipo por WhatsApp
                    </a>
                </div>
            </div>
        </div>

        @if($relatedItems->isNotEmpty())
            <div class="mt-5 pt-5 border-top">
                <h4 class="fw-bold text-dark mb-4">Otros equipos en {{ $item->category }}</h4>
                <div class="row row-cols-1 row-cols-md-3 g-4">
                    @foreach($relatedItems as $rel)
                        <div class="col">
                            <div class="card h-100 border-0 shadow-sm rounded-3 overflow-hidden">
                                <div style="height: 160px; overflow: hidden; background: #e2e8f0;">
                                    @if($rel->media)
                                        <img src="{{ Storage::url($rel->media->path) }}" alt="{{ $rel->name }}" class="w-100 h-100 object-fit-cover">
                                    @endif
                                </div>
                                <div class="card-body p-3">
                                    <h6 class="fw-bold mb-1">{{ $rel->name }}</h6>
                                    <p class="text-primary fw-bold mb-2">${{ number_format($rel->daily_rate, 2) }} / día</p>
                                    <a href="{{ route('items.show', $rel->code) }}" class="btn btn-sm btn-outline-primary w-100">Ver Detalles</a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</body>
</html>
