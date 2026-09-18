@extends('layouts.admin')

@section('title', 'Personal & Nómina')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h2 class="fw-bold text-dark mb-1">
            <i class="bi bi-person-badge text-primary me-2"></i>Personal Operativo & Bitácora de Turnos
        </h2>
        <p class="text-muted mb-0">Gestión de técnicos de audio, luces, montaje, tarifas diarias y liquidación de jornadas.</p>
    </div>
    <div class="d-flex gap-2">
        <button type="button" class="btn btn-outline-primary d-flex align-items-center gap-1" data-bs-toggle="modal" data-bs-target="#newWorkLogModal">
            <i class="bi bi-journal-plus"></i> Registrar Turno / Jornada
        </button>
        <button type="button" class="btn btn-primary d-flex align-items-center gap-1 shadow-sm" data-bs-toggle="modal" data-bs-target="#newStaffModal">
            <i class="bi bi-person-plus-fill"></i> Agregar Técnico / Operador
        </button>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Columna Personal Activo -->
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm p-4 bg-white rounded-3 h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold text-dark mb-0"><i class="bi bi-people me-1"></i> Equipo Técnico ({{ $staffMembers->count() }})</h5>
            </div>

            @if($staffMembers->isEmpty())
                <div class="text-center py-4 text-muted">
                    <i class="bi bi-person-x fs-2 d-block mb-2"></i>
                    No hay miembros del equipo registrados.
                </div>
            @else
                <div class="list-group list-group-flush">
                    @foreach($staffMembers as $member)
                        <div class="list-group-item px-0 py-3">
                            <div class="d-flex justify-content-between align-items-start mb-1">
                                <div>
                                    <h6 class="mb-0 fw-bold text-dark">{{ $member->name }}</h6>
                                    <small class="text-muted">{{ $member->role }} &bull; {{ $member->phone ?? 'Sin teléfono' }}</small>
                                </div>
                                <span class="badge bg-light text-dark border">
                                    ${{ number_format($member->daily_rate, 2) }} / día
                                </span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mt-2 small">
                                <span class="text-muted">Frecuencia: {{ ucfirst($member->payment_frequency) }}</span>
                                <div>
                                    @if($member->pending_earnings > 0)
                                        <span class="text-danger fw-bold">Por pagar: ${{ number_format($member->pending_earnings, 2) }}</span>
                                    @else
                                        <span class="text-success"><i class="bi bi-check-circle"></i> Al día</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <!-- Columna Bitácora Diaria de Trabajo -->
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm p-4 bg-white rounded-3 h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold text-dark mb-0"><i class="bi bi-journal-text me-1"></i> Bitácora de Turnos</h5>
                <span class="badge bg-warning-subtle text-warning border px-2 py-1">
                    Total Pendiente: ${{ number_format($totalPayrollPending, 2) }}
                </span>
            </div>

            @if($recentWorkLogs->isEmpty())
                <div class="text-center py-4 text-muted">
                    <i class="bi bi-calendar-minus fs-2 d-block mb-2"></i>
                    No hay turnos ni jornadas registradas en la bitácora.
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light small">
                            <tr>
                                <th>Fecha</th>
                                <th>Técnico</th>
                                <th>Servicio / Evento</th>
                                <th>Jornadas</th>
                                <th class="text-end">Honorarios</th>
                                <th class="text-center">Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentWorkLogs as $log)
                                <tr>
                                    <td class="small">{{ $log->work_date->format('d/m/Y') }}</td>
                                    <td>
                                        <strong>{{ $log->staffMember->name ?? '-' }}</strong>
                                        <div class="text-muted small">{{ $log->staffMember->role ?? '' }}</div>
                                    </td>
                                    <td class="small">
                                        @if($log->reservation)
                                            <a href="{{ route('admin.reservations.show', $log->reservation) }}" class="text-decoration-none">
                                                {{ $log->reservation->code }}
                                            </a>
                                            <div class="text-muted text-truncate" style="max-width: 150px;">{{ $log->reservation->event_name }}</div>
                                        @else
                                            <span class="text-muted">En Bodega / Mantenimiento</span>
                                        @endif
                                    </td>
                                    <td>{{ $log->hours_or_days }} día(s)</td>
                                    <td class="text-end fw-bold">${{ number_format($log->amount_earned, 2) }}</td>
                                    <td class="text-center">
                                        <form action="{{ route('admin.staff.worklog.pay', $log) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-xs p-1 border-0" title="Clic para alternar estado de pago">
                                                @if($log->paid)
                                                    <span class="badge bg-success">Pagado</span>
                                                @else
                                                    <span class="badge bg-danger">Pendiente</span>
                                                @endif
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-center mt-3">
                    {{ $recentWorkLogs->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal Nuevo Miembro de Personal -->
<div class="modal fade" id="newStaffModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="bi bi-person-plus me-2"></i>Registrar Operador / Técnico</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.staff.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nombre Completo</label>
                        <input type="text" name="name" class="form-control" placeholder="Ej: Andrés Morales" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Cargo / Especialidad</label>
                        <select name="role" class="form-select" required>
                            <option value="Técnico de Audio">Técnico de Audio / Sonidista</option>
                            <option value="Operador de Iluminación">Operador de Iluminación</option>
                            <option value="Técnico de Video & LED">Técnico de Video & LED</option>
                            <option value="Logística y Montaje de Estructuras">Logística y Montaje de Estructuras</option>
                            <option value="Conductor / Transportador">Conductor / Transportador</option>
                            <option value="Coordinador de Evento">Coordinador de Evento</option>
                        </select>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold">Cédula</label>
                            <input type="text" name="document" class="form-control" placeholder="Cédula">
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold">Teléfono</label>
                            <input type="text" name="phone" class="form-control" placeholder="+57 300...">
                        </div>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold">Tarifa por Turno / Día ($)</label>
                            <input type="number" step="0.01" name="daily_rate" class="form-control" placeholder="120000.00" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold">Frecuencia de Pago</label>
                            <select name="payment_frequency" class="form-select" required>
                                <option value="quincenal">Quincenal</option>
                                <option value="diario">Por Turno (Diario)</option>
                                <option value="semanal">Semanal</option>
                                <option value="mensual">Mensual</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar Personal</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Registrar Turno / Jornada -->
<div class="modal fade" id="newWorkLogModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title"><i class="bi bi-journal-plus me-2"></i>Asignar Turno en Bitácora</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.staff.worklog.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Técnico / Operador</label>
                        <select name="staff_member_id" class="form-select" required>
                            <option value="">-- Seleccionar Técnico --</option>
                            @foreach($staffMembers as $st)
                                <option value="{{ $st->id }}">{{ $st->name }} ({{ $st->role }} - ${{ number_format($st->daily_rate, 2) }}/día)</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Reserva / Evento Asociado (opcional)</label>
                        <select name="reservation_id" class="form-select">
                            <option value="">-- Turno Interno / Bodega / Mantenimiento --</option>
                            @foreach($activeReservations as $r)
                                <option value="{{ $r->id }}">{{ $r->code }} - {{ $r->event_name }} ({{ $r->client->name }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold">Fecha de Jornada</label>
                            <input type="date" name="work_date" class="form-control" value="{{ now()->toDateString() }}" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold">Jornadas / Días</label>
                            <input type="number" step="0.5" min="0.5" name="hours_or_days" class="form-control" value="1" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Observaciones</label>
                        <input type="text" name="notes" class="form-control" placeholder="Horario especial, viáticos, tareas asignadas...">
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-dark">Registrar Turno</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
