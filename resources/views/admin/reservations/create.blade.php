@extends('layouts.admin')

@section('title', 'Crear Reserva')

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.reservations.index') }}" class="btn btn-outline-secondary btn-sm mb-2">
        <i class="bi bi-arrow-left me-1"></i> Volver a reservas
    </a>
    <h2 class="fw-bold text-dark mb-1">
        <i class="bi bi-calendar-plus text-primary me-2"></i>Registrar Nueva Reserva de Equipos
    </h2>
    <p class="text-muted">Aplica tarifas diferenciadas (día / fin de semana) y validación de disponibilidad automática.</p>
</div>

<form action="{{ route('admin.reservations.store') }}" method="POST" id="reservationForm">
    @csrf
    <div class="row g-4">
        <!-- Columna Izquierda: Datos del Evento y Equipos -->
        <div class="col-lg-8">
            <!-- Datos del Evento -->
            <div class="card border-0 shadow-sm p-4 bg-white rounded-3 mb-4">
                <h5 class="fw-bold text-dark mb-3"><i class="bi bi-info-circle me-1"></i> Datos del Servicio & Cliente</h5>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="client_id" class="form-label fw-semibold">Cliente <span class="text-danger">*</span></label>
                        <select class="form-select" id="client_id" name="client_id" required>
                            <option value="">-- Seleccionar Cliente --</option>
                            @foreach($clients as $c)
                                <option value="{{ $c->id }}" {{ old('client_id') == $c->id ? 'selected' : '' }}>
                                    {{ $c->name }} ({{ $c->company ?? $c->document ?? 'Particular' }})
                                </option>
                            @endforeach
                        </select>
                        <div class="form-text small">
                            ¿Cliente nuevo? <a href="{{ route('admin.clients.create') }}" target="_blank">Crear cliente aquí</a>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label for="event_name" class="form-label fw-semibold">Nombre del Evento <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="event_name" name="event_name" placeholder="Ej: Boda Gómez & Silva / Concierto Acústico" value="{{ old('event_name') }}" required>
                    </div>

                    <div class="col-12">
                        <label for="event_location" class="form-label fw-semibold">Lugar o Dirección de Entrega</label>
                        <input type="text" class="form-control" id="event_location" name="event_location" placeholder="Ej: Hacienda Los Robles, Salón Principal, Calle 45 # 12-34" value="{{ old('event_location') }}">
                    </div>

                    <div class="col-md-4">
                        <label for="start_date" class="form-label fw-semibold">Fecha de Inicio <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="start_date" name="start_date" value="{{ old('start_date', now()->toDateString()) }}" required>
                    </div>

                    <div class="col-md-4">
                        <label for="end_date" class="form-label fw-semibold">Fecha de Devolución <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="end_date" name="end_date" value="{{ old('end_date', now()->addDay()->toDateString()) }}" required>
                    </div>

                    <div class="col-md-4">
                        <label for="pricing_type" class="form-label fw-semibold">Esquema de Tarifa <span class="text-danger">*</span></label>
                        <select class="form-select" id="pricing_type" name="pricing_type" required>
                            <option value="daily" {{ old('pricing_type') === 'daily' ? 'selected' : '' }}>Tarifa por Día Hábil</option>
                            <option value="weekend" {{ old('pricing_type') === 'weekend' ? 'selected' : '' }}>Tarifa Plana Fin de Semana</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Selección de Equipos (Requerimiento 3.2 y 3.3) -->
            <div class="card border-0 shadow-sm p-4 bg-white rounded-3">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold text-dark mb-0"><i class="bi bi-speaker me-1"></i> Equipos Solicitados</h5>
                    <button type="button" class="btn btn-sm btn-outline-primary" id="addItemRowBtn">
                        <i class="bi bi-plus-lg"></i> Agregar Equipo
                    </button>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered align-middle" id="itemsTable">
                        <thead class="table-light small">
                            <tr>
                                <th style="width: 45%;">Equipo en Inventario</th>
                                <th style="width: 20%;" class="text-center">Cantidad</th>
                                <th style="width: 25%;" class="text-end">Tarifa Aplicada</th>
                                <th style="width: 10%;" class="text-center">Quitar</th>
                            </tr>
                        </thead>
                        <tbody id="itemsContainer">
                            <!-- Fila inicial -->
                            <tr class="item-row">
                                <td>
                                    <select name="items[0][item_id]" class="form-select form-select-sm item-select" required>
                                        <option value="">-- Seleccionar Equipo --</option>
                                        @foreach($items as $it)
                                            <option value="{{ $it->id }}" data-daily="{{ $it->daily_rate }}" data-weekend="{{ $it->weekend_rate }}" data-stock="{{ $it->total_quantity }}">
                                                [{{ $it->code }}] {{ $it->name }} (Stock: {{ $it->total_quantity }})
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <input type="number" name="items[0][quantity]" class="form-control form-control-sm text-center item-qty" value="1" min="1" required>
                                </td>
                                <td class="text-end">
                                    <span class="fw-bold row-price">$0.00</span>
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-outline-danger remove-row-btn" disabled>
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Columna Derecha: Resumen de Facturación y Anticipo -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm p-4 bg-white rounded-3 sticky-top" style="top: 80px;">
                <h5 class="fw-bold text-dark mb-3"><i class="bi bi-receipt me-1"></i> Resumen de Facturación</h5>

                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Subtotal Equipos:</span>
                    <span class="fw-bold text-dark" id="calcSubtotal">$0.00</span>
                </div>

                <div class="mb-3">
                    <label for="discount" class="form-label small fw-semibold">Descuento Especial ($)</label>
                    <input type="number" step="0.01" min="0" class="form-control form-control-sm text-end" id="discount" name="discount" value="0.00">
                </div>

                <hr>

                <div class="d-flex justify-content-between mb-3">
                    <span class="fs-5 fw-bold text-primary">Total Alquiler:</span>
                    <span class="fs-5 fw-bold text-primary" id="calcTotal">$0.00</span>
                </div>

                <div class="p-3 bg-light rounded-3 mb-3 border">
                    <label for="initial_payment" class="form-label small fw-semibold mb-1">Abono Inicial / Anticipo ($)</label>
                    <input type="number" step="0.01" min="0" class="form-control form-control-sm text-end mb-2" id="initial_payment" name="initial_payment" value="0.00">

                    <label for="payment_method" class="form-label small fw-semibold mb-1">Medio de Pago</label>
                    <select name="payment_method" id="payment_method" class="form-select form-select-sm">
                        <option value="transferencia">Transferencia Bancaria</option>
                        <option value="nequi_daviplata">Nequi / Daviplata</option>
                        <option value="efectivo">Efectivo</option>
                        <option value="tarjeta">Tarjeta Débito / Crédito</option>
                    </select>
                </div>

                <div class="d-flex justify-content-between mb-4 small">
                    <span class="text-muted">Saldo Pendiente al Despacho:</span>
                    <span class="fw-bold text-danger" id="calcBalance">$0.00</span>
                </div>

                <button type="submit" class="btn btn-primary w-100 py-3 fw-bold d-flex align-items-center justify-content-center gap-2 shadow-sm">
                    <i class="bi bi-check-circle-fill"></i> Confirmar y Guardar Reserva
                </button>
            </div>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    let rowIndex = 1;
    const itemsContainer = document.getElementById('itemsContainer');
    const addItemBtn = document.getElementById('addItemRowBtn');
    const pricingSelect = document.getElementById('pricing_type');
    const startDateInput = document.getElementById('start_date');
    const endDateInput = document.getElementById('end_date');
    const discountInput = document.getElementById('discount');
    const initialPaymentInput = document.getElementById('initial_payment');

    const calcSubtotal = document.getElementById('calcSubtotal');
    const calcTotal = document.getElementById('calcTotal');
    const calcBalance = document.getElementById('calcBalance');

    function calculateDays() {
        const start = new Date(startDateInput.value);
        const end = new Date(endDateInput.value);
        if (isNaN(start) || isNaN(end) || end < start) return 1;
        const diffTime = Math.abs(end - start);
        return Math.max(1, Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1);
    }

    function updateCalculations() {
        const days = calculateDays();
        const isWeekend = pricingSelect.value === 'weekend';
        let subtotal = 0;

        document.querySelectorAll('.item-row').forEach(row => {
            const select = row.querySelector('.item-select');
            const qtyInput = row.querySelector('.item-qty');
            const priceLabel = row.querySelector('.row-price');

            const option = select.options[select.selectedIndex];
            if (option && option.value) {
                const daily = parseFloat(option.getAttribute('data-daily')) || 0;
                const weekend = parseFloat(option.getAttribute('data-weekend')) || 0;
                const qty = parseInt(qtyInput.value) || 1;

                const unitRate = isWeekend ? weekend : (daily * days);
                const lineTotal = unitRate * qty;
                subtotal += lineTotal;
                priceLabel.textContent = '$' + lineTotal.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            } else {
                priceLabel.textContent = '$0.00';
            }
        });

        const discount = parseFloat(discountInput.value) || 0;
        const total = Math.max(0, subtotal - discount);
        const initialPayment = parseFloat(initialPaymentInput.value) || 0;
        const balance = Math.max(0, total - initialPayment);

        calcSubtotal.textContent = '$' + subtotal.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        calcTotal.textContent = '$' + total.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        calcBalance.textContent = '$' + balance.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    addItemBtn.addEventListener('click', function () {
        const firstRow = document.querySelector('.item-row');
        const newRow = firstRow.cloneNode(true);

        const select = newRow.querySelector('.item-select');
        select.name = `items[${rowIndex}][item_id]`;
        select.value = '';

        const qtyInput = newRow.querySelector('.item-qty');
        qtyInput.name = `items[${rowIndex}][quantity]`;
        qtyInput.value = '1';

        const priceLabel = newRow.querySelector('.row-price');
        priceLabel.textContent = '$0.00';

        const removeBtn = newRow.querySelector('.remove-row-btn');
        removeBtn.disabled = false;

        itemsContainer.appendChild(newRow);
        rowIndex++;
        attachRowEvents(newRow);
    });

    function attachRowEvents(row) {
        row.querySelector('.item-select').addEventListener('change', updateCalculations);
        row.querySelector('.item-qty').addEventListener('input', updateCalculations);
        row.querySelector('.remove-row-btn').addEventListener('click', function () {
            if (document.querySelectorAll('.item-row').length > 1) {
                row.remove();
                updateCalculations();
            }
        });
    }

    document.querySelectorAll('.item-row').forEach(attachRowEvents);
    pricingSelect.addEventListener('change', updateCalculations);
    startDateInput.addEventListener('change', updateCalculations);
    endDateInput.addEventListener('change', updateCalculations);
    discountInput.addEventListener('input', updateCalculations);
    initialPaymentInput.addEventListener('input', updateCalculations);

    updateCalculations();
});
</script>
@endpush
