@extends('layouts.admin')

@section('title', 'Registrar Cliente')

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.clients.index') }}" class="btn btn-outline-secondary btn-sm mb-2">
        <i class="bi bi-arrow-left me-1"></i> Volver a clientes
    </a>
    <h2 class="fw-bold text-dark mb-1">
        <i class="bi bi-person-plus text-primary me-2"></i>Registrar Nuevo Cliente
    </h2>
</div>

<div class="card border-0 shadow-sm p-4 bg-white rounded-3 max-w-2xl" style="max-width: 750px;">
    <form action="{{ route('admin.clients.store') }}" method="POST">
        @csrf
        <div class="row g-3">
            <div class="col-md-7">
                <label for="name" class="form-label fw-semibold">Nombre Completo / Razón Social <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="name" name="name" placeholder="Ej: Juan Pérez / Eventos Globales SAS" value="{{ old('name') }}" required>
            </div>

            <div class="col-md-5">
                <label for="document" class="form-label fw-semibold">Cédula o NIT</label>
                <input type="text" class="form-control" id="document" name="document" placeholder="Ej: 1020304050 / 900.123.456-1" value="{{ old('document') }}">
            </div>

            <div class="col-md-6">
                <label for="email" class="form-label fw-semibold">Correo Electrónico</label>
                <input type="email" class="form-control" id="email" name="email" placeholder="cliente@correo.com" value="{{ old('email') }}">
            </div>

            <div class="col-md-6">
                <label for="phone" class="form-label fw-semibold">Teléfono / WhatsApp</label>
                <input type="text" class="form-control" id="phone" name="phone" placeholder="+57 300 123 4567" value="{{ old('phone') }}">
            </div>

            <div class="col-md-6">
                <label for="company" class="form-label fw-semibold">Empresa / Productora</label>
                <input type="text" class="form-control" id="company" name="company" placeholder="Ej: Producciones Audiovisuales" value="{{ old('company') }}">
            </div>

            <div class="col-md-6">
                <label for="address" class="form-label fw-semibold">Dirección</label>
                <input type="text" class="form-control" id="address" name="address" placeholder="Ej: Cra 15 # 80-20 Oficina 402" value="{{ old('address') }}">
            </div>

            <div class="col-12">
                <label for="notes" class="form-label fw-semibold">Observaciones / Notas del Cliente</label>
                <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="Historial de puntualidad, preferencias de equipamiento...">{{ old('notes') }}</textarea>
            </div>
        </div>

        <div class="mt-4 pt-3 border-top d-flex gap-2">
            <button type="submit" class="btn btn-primary px-4">Guardar Cliente</button>
            <a href="{{ route('admin.clients.index') }}" class="btn btn-secondary">Cancelar</a>
        </div>
    </form>
</div>
@endsection
