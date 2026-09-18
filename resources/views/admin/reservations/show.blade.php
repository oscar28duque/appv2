@extends('layouts.admin')

@section('title', 'Reserva ' . $reservation->code)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <a href="{{ route('admin.reservations.index') }}" class="btn btn-outline-secondary btn-sm mb-2">
            <i class="bi bi-arrow-left me-1"></i> Volver al listado
        </a>
        <div class="d-flex align-items-center gap-3">
            <h2 class="fw-bold text-dark mb-0">{{ $reservation->code }}</h2>
            <span class="badge fs-6 {{ $reservation->status === 'confirmada' ? 'bg-primary' : ($reservation->status === 'en_curso' ? 'bg-warning text-dark' : ($reservation->status === 'devuelta' ? 'bg-success' : 'bg-secondary')) }}">
                {{ ucfirst($reservation->status) }}
            </span>
            @if($reservation->is_overdue)
                <span class="badge bg-danger fs-6"><i class="bi bi-exclamation-octagon me-1"></i>Devolución Atrasada</span>
            @endif
        </div>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        <!-- Registrar Abono -->
        <button type="button" class="btn btn-success d-flex align-items-center gap-1 shadow-sm" data-bs-toggle="modal" data-bs-target="#paymentModal">
            <i class="bi bi-cash-stack"></i> Registrar Abono
        </button>

        <!-- Subir Evidencia de Entrega -->
        <button type="button" class="btn btn-outline-primary d-flex align-items-center gap-1" data-bs-toggle="modal" data-bs-target="#evidenceModal">
            <i class="bi bi-camera"></i> Evidencia Entrega
        </button>

        <!-- Asentar Devolución -->
        @if($reservation->status !== 'devuelta')
            <button type="button" class="btn btn-warning text-dark d-flex align-items-center gap-1" data-bs-toggle="modal" data-bs-target="#returnModal">
                <i class="bi bi-box-arrow-in-left"></i> Asentar Devolución
            </button>
        @endif
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Ficha del Servicio -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm p-4 bg-white rounded-3 mb-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold text-dark mb-0"><i class="bi bi-calendar-event me-2 text-primary"></i>Datos del Evento</h5>
                <!-- Cambio Rápido de Estado -->
                <form action="{{ route('admin.reservations.status', $reservation) }}" method="POST" class="d-flex gap-1 align-items-center">
                    @csrf
                    @method('PATCH')
                    <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                        @foreach($statuses as $val => $lbl)
                            <option value="{{ $val }}" {{ $reservation->status === $val ? 'selected' : '' }}>{{ $lbl }}</option>
                        @endforeach
                    </select>
                </form>
            </div>

            <div class="row g-3">
                <div class="col-md-6">
                    <small class="text-muted d-block">Evento:</small>
                    <span class="fw-bold text-dark fs-6">{{ $reservation->event_name }}</span>
                </div>
                <div class="col-md-6">
                    <small class="text-muted d-block">Lugar de Despacho / Evento:</small>
                    <span class="text-dark">{{ $reservation->event_location ?? 'En instalaciones' }}</span>
                </div>
                <div class="col-md-6">
                    <small class="text-muted d-block">Periodo del Alquiler:</small>
                    <span class="fw-bold text-primary">{{ $reservation->start_date->format('d/m/Y') }} al {{ $reservation->end_date->format('d/m/Y') }}</span>
                    <span class="text-muted small">({{ $reservation->days_count }} días)</span>
                </div>
                <div class="col-md-6">
                    <small class="text-muted d-block">Modalidad de Cobro:</small>
                    <span class="badge bg-light text-dark border">
                        {{ $reservation->pricing_type === 'weekend' ? 'Tarifa Plana Fin de Semana' : 'Tarifa Diaria' }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Equipos Alquilados -->
        <div class="card border-0 shadow-sm p-4 bg-white rounded-3 mb-4">
            <h5 class="fw-bold text-dark mb-3"><i class="bi bi-speaker me-2 text-primary"></i>Equipos en Contrato</h5>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light small">
                        <tr>
                            <th style="width: 60px;">Foto</th>
                            <th>Equipo</th>
                            <th class="text-center">Cant.</th>
                            <th class="text-end">Tarifa Unit.</th>
                            <th class="text-end">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reservation->items as $detail)
                            <tr>
                                <td>
                                    @if($detail->item && $detail->item->media)
                                        <img src="{{ Storage::url($detail->item->media->path) }}" alt="{{ $detail->item->name }}" class="rounded object-fit-cover" style="width: 50px; height: 40px;">
                                    @else
                                        <div class="bg-light rounded text-muted d-flex align-items-center justify-content-center" style="width: 50px; height: 40px; font-size: 10px;">
                                            <i class="bi bi-image"></i>
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <strong>{{ $detail->item->name ?? 'Equipo retirado' }}</strong>
                                    <div class="text-muted small">Código: <code>{{ $detail->item->code ?? '-' }}</code> &bull; {{ $detail->item->category ?? '' }}</div>
                                </td>
                                <td class="text-center fw-bold">{{ $detail->quantity }}</td>
                                <td class="text-end">${{ number_format($detail->unit_price, 2) }}</td>
                                <td class="text-end fw-bold">${{ number_format($detail->subtotal, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Evidencia Fotográfica y Devolución -->
        <div class="row g-4">
            <div class="col-md-6">
                <div class="card border-0 shadow-sm p-3 bg-white rounded-3 h-100">
                    <h6 class="fw-bold text-dark mb-2"><i class="bi bi-camera me-1"></i> Evidencia de Entrega</h6>
                    @if($reservation->evidenceMedia)
                        <div class="text-center">
                            <a href="{{ Storage::url($reservation->evidenceMedia->path) }}" target="_blank">
                                <img src="{{ Storage::url($reservation->evidenceMedia->path) }}" class="img-fluid rounded border shadow-sm mb-2" style="max-height: 160px;">
                            </a>
                            <div class="small text-muted">Foto de acta / entrega guardada en Storage</div>
                        </div>
                    @else
                        <div class="text-center py-3 text-muted">
                            <i class="bi bi-camera-fill fs-2 d-block mb-1 text-secondary opacity-50"></i>
                            Sin evidencia fotográfica adjunta.
                            <div class="mt-2">
                                <button type="button" class="btn btn-xs btn-outline-primary" data-bs-toggle="modal" data-bs-target="#evidenceModal">
                                    Subir Foto
                                </button>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <div class="col-md-6">
                <div class="card border-0 shadow-sm p-3 bg-white rounded-3 h-100">
                    <h6 class="fw-bold text-dark mb-2"><i class="bi bi-box-arrow-in-left me-1"></i> Estado de Devolución</h6>
                    @if($reservation->return_date_real)
                        <div class="small mb-1"><strong>Fecha Real:</strong> {{ $reservation->return_date_real->format('d/m/Y H:i') }}</div>
                        <div class="small mb-1"><strong>Inspección:</strong>
                            <span class="badge {{ $reservation->return_status === 'sin_novedad' ? 'bg-success' : 'bg-danger' }}">
                                {{ ucfirst(str_replace('_', ' ', $reservation->return_status)) }}
                            </span>
                        </div>
                        @if($reservation->return_notes)
                            <div class="small text-muted p-2 bg-light rounded mt-1">
                                <em>{{ $reservation->return_notes }}</em>
                            </div>
                        @endif
                    @else
                        <div class="text-center py-3 text-muted">
                            <i class="bi bi-clock-history fs-2 d-block mb-1 text-warning opacity-75"></i>
                            Equipos actualmente en poder del cliente.
                            <div class="mt-2">
                                <button type="button" class="btn btn-xs btn-outline-warning text-dark" data-bs-toggle="modal" data-bs-target="#returnModal">
                                    Registrar Retorno
                                </button>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Columna Derecha: Cliente y Finanzas -->
    <div class="col-lg-4">
        <!-- Ficha Cliente -->
        <div class="card border-0 shadow-sm p-4 bg-white rounded-3 mb-4">
            <h6 class="fw-bold text-dark mb-3"><i class="bi bi-person-circle me-2 text-primary"></i>Datos del Cliente</h6>
            <h5 class="fw-bold text-dark mb-1">
                <a href="{{ route('admin.clients.show', $reservation->client) }}" class="text-decoration-none">
                    {{ $reservation->client->name }}
                </a>
            </h5>
            <p class="text-muted small mb-2">{{ $reservation->client->company ?? 'Cliente Particular' }}</p>

            <ul class="list-unstyled small mb-0">
                <li class="mb-1"><i class="bi bi-card-text me-2 text-secondary"></i>{{ $reservation->client->document ?? 'Sin doc.' }}</li>
                <li class="mb-1"><i class="bi bi-envelope me-2 text-secondary"></i>{{ $reservation->client->email ?? 'Sin correo' }}</li>
                <li><i class="bi bi-telephone me-2 text-secondary"></i>{{ $reservation->client->phone ?? 'Sin teléfono' }}</li>
            </ul>
        </div>

        <!-- Resumen Financiero y Abonos -->
        <div class="card border-0 shadow-sm p-4 bg-white rounded-3">
            <h6 class="fw-bold text-dark mb-3"><i class="bi bi-cash-coin me-2 text-success"></i>Estado Financiero</h6>

            <div class="d-flex justify-content-between mb-2 small">
                <span class="text-muted">Subtotal:</span>
                <span class="fw-semibold">${{ number_format($reservation->subtotal, 2) }}</span>
            </div>
            @if($reservation->discount > 0)
                <div class="d-flex justify-content-between mb-2 small text-success">
                    <span>Descuento:</span>
                    <span class="fw-semibold">-${{ number_format($reservation->discount, 2) }}</span>
                </div>
            @endif
            <div class="d-flex justify-content-between mb-2 fs-5 fw-bold text-dark border-top pt-2">
                <span>Total:</span>
                <span>${{ number_format($reservation->total_amount, 2) }}</span>
            </div>
            <div class="d-flex justify-content-between mb-2 text-primary">
                <span>Abonado:</span>
                <span class="fw-bold">${{ number_format($reservation->paid_amount, 2) }}</span>
            </div>
            <div class="d-flex justify-content-between p-2 rounded {{ $reservation->pending_balance > 0 ? 'bg-danger-subtle text-danger' : 'bg-success-subtle text-success' }} mb-3 fw-bold">
                <span>Saldo Pendiente:</span>
                <span>${{ number_format($reservation->pending_balance, 2) }}</span>
            </div>

            <!-- Historial de Pagos -->
            <h6 class="fw-bold text-dark mb-2 small">Abonos Registrados ({{ $reservation->payments->count() }})</h6>
            @if($reservation->payments->isEmpty())
                <div class="small text-muted py-2">No se han registrado abonos aún.</div>
            @else
                <div class="list-group list-group-flush mb-3 small">
                    @foreach($reservation->payments as $pay)
                        <div class="list-group-item px-0 py-2 d-flex justify-content-between align-items-center">
                            <div>
                                <strong>${{ number_format($pay->amount, 2) }}</strong>
                                <div class="text-muted" style="font-size: 11px;">{{ $pay->payment_date->format('d/m/Y') }} &bull; {{ ucfirst($pay->payment_method) }}</div>
                            </div>
                            <span class="badge bg-light text-muted border">{{ $pay->reference ?? 'Sin ref.' }}</span>
                        </div>
                    @endforeach
                </div>
            @endif

            @if($reservation->pending_balance > 0)
                <button type="button" class="btn btn-outline-success w-100 btn-sm" data-bs-toggle="modal" data-bs-target="#paymentModal">
                    <i class="bi bi-plus-circle me-1"></i> Añadir Abono
                </button>
            @endif
        </div>
    </div>
</div>

<!-- Modal Registrar Abono -->
<div class="modal fade" id="paymentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title"><i class="bi bi-cash-stack me-2"></i>Registrar Abono a Reserva</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.reservations.payment', $reservation) }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="alert alert-info py-2 small mb-3">
                        Saldo pendiente actual: <strong>${{ number_format($reservation->pending_balance, 2) }}</strong>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Monto a Abonar ($)</label>
                        <input type="number" step="0.01" max="{{ $reservation->pending_balance }}" class="form-control" name="amount" value="{{ $reservation->pending_balance }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Medio de Pago</label>
                        <select name="payment_method" class="form-select" required>
                            <option value="transferencia">Transferencia Bancaria</option>
                            <option value="nequi_daviplata">Nequi / Daviplata</option>
                            <option value="efectivo">Efectivo</option>
                            <option value="tarjeta">Tarjeta Débito / Crédito</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Número de Comprobante / Referencia</label>
                        <input type="text" class="form-control" name="reference" placeholder="Ej: TR-9823491">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Fecha del Pago</label>
                        <input type="date" class="form-control" name="payment_date" value="{{ now()->toDateString() }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Notas Adicionales</label>
                        <textarea class="form-control" name="notes" rows="2" placeholder="Observaciones sobre el pago..."></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Guardar Abono</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Subir Evidencia de Entrega -->
<div class="modal fade" id="evidenceModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="bi bi-camera me-2"></i>Adjuntar Evidencia Fotográfica</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.reservations.evidence', $reservation) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-4">
                    <p class="text-muted small">Carga una fotografía del montaje o del acta de entrega firmada por el cliente.</p>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Fotografía (JPG, PNG, WEBP)</label>
                        <input type="file" name="evidence_file" class="form-control" accept="image/*" required>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Subir a Storage</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Asentar Devolución -->
<div class="modal fade" id="returnModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title fw-bold"><i class="bi bi-box-arrow-in-left me-2"></i>Recepción de Devolución</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.reservations.return', $reservation) }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Fecha y Hora Real de Devolución</label>
                        <input type="datetime-local" name="return_date_real" class="form-control" value="{{ now()->format('Y-m-d\TH:i') }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Estado de los Equipos al Recibir</label>
                        <select name="return_status" class="form-select" required>
                            <option value="sin_novedad">Sin Novedad (Buen Estado Completo)</option>
                            <option value="con_danos">Con Daños Técnicos / Estéticos</option>
                            <option value="faltantes">Con Accesorios o Equipos Faltantes</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Observaciones / Novedades</label>
                        <textarea class="form-control" name="return_notes" rows="3" placeholder="Detalle si hubo cables faltantes, daños por mal uso o notas del técnico receptor..."></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-warning fw-bold">Confirmar Devolución</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
