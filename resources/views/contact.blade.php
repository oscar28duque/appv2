<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Contacto | {{ config('app.name') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { background-color: #f1f5f9; font-family: system-ui, -apple-system, sans-serif; }
        .contact-card { max-width: 650px; margin: 40px auto; border-radius: 16px; border: none; box-shadow: 0 10px 25px rgba(0,0,0,0.08); overflow: hidden; }
        .contact-header { background: linear-gradient(135deg, #022766 0%, #083b96 100%); color: white; padding: 35px 30px; text-align: center; }
    </style>
</head>
<body>
    <nav class="navbar navbar-dark bg-dark py-3">
        <div class="container">
            <a class="navbar-brand fw-bold d-flex align-items-center gap-2" href="{{ url('/') }}">
                <i class="bi bi-shield-lock-fill text-primary fs-4"></i>
                <span>{{ config('app.name') }}</span>
            </a>
            <div class="d-flex gap-2">
                <a href="{{ url('/') }}" class="btn btn-outline-light btn-sm">&larr; Inicio</a>
                @auth
                    <a href="{{ route('dashboard') }}" class="btn btn-primary btn-sm">Dashboard</a>
                @endauth
            </div>
        </div>
    </nav>

    <div class="container py-4">
        <div class="card contact-card bg-white">
            <div class="contact-header">
                <i class="bi bi-envelope-check-fill fs-1 text-info mb-2"></i>
                <h2 class="fw-bold mb-1">Contáctanos</h2>
                <p class="mb-0 opacity-75">Envíanos tus consultas y recibirás una respuesta y confirmación automática.</p>
            </div>

            <div class="p-4 p-md-5">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2" role="alert">
                        <i class="bi bi-check-circle-fill fs-5"></i>
                        <div>{{ session('success') }}</div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2" role="alert">
                        <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                        <div>{{ session('error') }}</div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0 ps-3">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Formulario según sección 6.5 de la guía -->
                <form action="{{ route('contact.send') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="name" class="form-label fw-semibold">Nombre Completo</label>
                        <input type="text" class="form-control" id="name" name="name" placeholder="Tu nombre" value="{{ old('name') }}" required>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label fw-semibold">Correo Electrónico</label>
                        <input type="email" class="form-control" id="email" name="email" placeholder="correo@ejemplo.com" value="{{ old('email') }}" required>
                        <div class="form-text">Recibirás una copia de confirmación en este correo.</div>
                    </div>

                    <div class="mb-4">
                        <label for="message" class="form-label fw-semibold">Mensaje</label>
                        <textarea class="form-control" id="message" name="message" rows="5" placeholder="Escribe aquí tu consulta o solicitud..." required>{{ old('message') }}</textarea>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold d-flex align-items-center justify-content-center gap-2">
                        <i class="bi bi-send-fill"></i> Enviar Mensaje
                    </button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
