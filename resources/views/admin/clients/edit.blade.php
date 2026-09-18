@extends('layouts.admin')

@section('title', 'Editar Cliente')

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.clients.show', $client) }}" class="btn btn-outline-secondary btn-sm mb-2">
        <i class="bi bi-arrow-left me-1"></i> Volver a ficha
    </a>
    <h2 class="fw-bold text-dark mb-1">
        <i class="bi bi-pencil-square text-primary me-2"></i>Editar Cliente: {{ $client->name }}
    </h2>
</div>

<div class="card border-0 shadow-sm p-4 bg-white rounded-3 max-w-2xl" style="max-width: 750px;">
    <form action="{{ route('admin.clients.update', $client) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="row g-3">
            <div class="col-md-7">
                <label for="name" class="form-label fw-semibold">Nombre Completo <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $client->name) }}" required>
            </div>

            <div class="col-md-5">
                <label for="document" class="form-label fw-semibold">Cédula o NIT</label>
                <input type="text" class="form-control" id="document" name="document" value="{{ old('document', $client->document) }}">
            </div>

            <div class="col-md-6">
                <label for="email" class="form-label fw-semibold">Correo Electrónico</label>
                <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $client->email) }}">
            </div>

            <div class="col-md-6">
                <label for="phone" class="form-label fw-semibold">Teléfono / WhatsApp</label>
                <input type="text" class="form-control" id="phone" name="phone" value="{{ old('phone', $client->phone) }}">
            </div>

            <div class="col-md-6">
                <label for="company" class="form-label fw-semibold">Empresa / Productora</label>
                <input type="text" class="form-control" id="company" name="company" value="{{ old('company', $client->company) }}">
            </div>

            <div class="col-md-6">
                <label for="address" class="form-label fw-semibold">Dirección</label>
                <input type="text" class="form-control" id="address" name="address" value="{{ old('address', $client->address) }}">
            </div>

            <div class="col-12">
                <label for="notes" class="form-label fw-semibold">Notas</label>
                <textarea class="form-control" id="notes" name="notes" rows="3">{{ old('notes', $client->notes) }}</textarea>
            </div>
        </div>

        <div class="mt-4 pt-3 border-top d-flex gap-2">
            <button type="submit" class="btn btn-primary px-4">Actualizar Cliente</button>
            <a href="{{ route('admin.clients.show', $client) }}" class="btn btn-secondary">Cancelar</a>
        </div>
    </form>
</div>
@endsection
