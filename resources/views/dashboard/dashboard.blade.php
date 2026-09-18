@extends('layouts.admin')

@section('title', 'Dashboard Operativo')

@section('content')
<!-- Header del Dashboard -->
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h2 class="fw-bold text-dark mb-1">
            <i class="bi bi-speedometer2 text-primary me-2"></i>Centro de Control & Logística de Eventos
        </h2>
        <p class="text-muted mb-0">Indicadores clave de inventario, reservas en tiempo real y flujo financiero.</p>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        <a href="{{ route('admin.reservations.create') }}" class="btn btn-primary d-flex align-items-center gap-1 shadow-sm">
            <i class="bi bi-plus-circle-fill"></i> Nueva Reserva
        </a>
        <a href="{{ route('admin.items.create') }}" class="btn btn-outline-primary d-flex align-items-center gap-1">
            <i class="bi bi-box-seam"></i> Nuevo Equipo
        </a>
        <a href="{{ route('admin.finances.index') }}" class="btn btn-outline-success d-flex align-items-center gap-1">
            <i class="bi bi-cash"></i> Cobros & Gastos
        </a>
    </div>
</div>

<!-- Alertas Críticas (Requerimiento 3.1: Devoluciones atrasadas y vencimientos) -->
@if($overdueReturns->isNotEmpty())
    <div class="alert alert-danger shadow-sm border-0 mb-4 p-3 rounded-3" role="alert">
        <div class="d-flex align-items-center gap-2 mb-2">
            <i class="bi bi-exclamation-triangle-fill fs-4 text-danger"></i>
            <h5 class="fw-bold mb-0 text-danger">Alerta de Devoluciones Atrasadas ({{ $overdueReturns->count() }})</h5>
        </div>
        <p class="small mb-2 text-dark">Las siguientes reservas han superado su fecha de finalización y los equipos aún no han sido reintegrados al inventario:</p>
        <div class="table-responsive bg-white rounded border">
            <table class="table table-sm table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Código</th>
                        <th>Cliente</th>
                        <th>Fecha Fin Prevista</th>
                        <th>Días de Retraso</th>
                        <th>Saldo Pendiente</th>
                        <th class="text-end">Acción</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($overdueReturns as $ret)
                        <tr>
                            <td><strong>{{ $ret->code }}</strong></td>
                            <td>{{ $ret->client->name }} ({{ $ret->client->phone ?? 'Sin tel' }})</td>
                            <td class="text-danger fw-semibold">{{ $ret->end_date->format('d/m/Y') }}</td>
                            <td><span class="badge bg-danger">{{ $ret->end_date->diffInDays(now()) }} días tarde</span></td>
                            <td>${{ number_format($ret->pending_balance, 2) }}</td>
                            <td class="text-end">
                                <a href="{{ route('admin.reservations.show', $ret) }}" class="btn btn-xs btn-outline-primary py-0">
                                    Gestionar Devolución &rarr;
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endif

<!-- Indicadores Clave (KPIs en tiempo real) -->
<div class="row g-3 mb-4">
    <!-- KPI 1: Disponibilidad Hoy (Fijo del día actual) -->
    <div class="col-sm-6 col-xl-3">
        <div class="card stat-card bg-white p-3 h-100">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-muted fw-bold small">DISPONIBILIDAD HOY</span>
                <span class="p-2 rounded-3 bg-primary bg-opacity-10 text-primary">
                    <i class="bi bi-box-seam fs-5"></i>
                </span>
            </div>
            <div class="d-flex align-items-baseline gap-2">
                <h3 class="fw-bold text-dark mb-0">{{ $availableTodayCount }}</h3>
                <span class="text-muted small">/ {{ $totalItemsCount }} unids</span>
            </div>
            <div class="mt-2">
                <div class="progress" style="height: 6px;">
                    <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $availabilityRate }}%;"></div>
                </div>
                <div class="d-flex justify-content-between small text-muted mt-1">
                    <span>{{ $availabilityRate }}% libre hoy</span>
                    <span>{{ $bookedTodayCount }} alquilados</span>
                </div>
            </div>
        </div>
    </div>

    <!-- KPI 2: Reservas Activas y Próximas -->
    <div class="col-sm-6 col-xl-3">
        <div class="card stat-card bg-white p-3 h-100">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-muted fw-bold small">RESERVAS ACTIVAS</span>
                <span class="p-2 rounded-3 bg-info bg-opacity-10 text-info">
                    <i class="bi bi-calendar-check fs-5"></i>
                </span>
            </div>
            <h3 class="fw-bold text-dark mb-0">{{ $activeReservationsCount }}</h3>
            <div class="text-muted small mt-2">
                <i class="bi bi-clock-history me-1"></i><strong>{{ $upcomingReservations->count() }}</strong> en las próximas 72 horas
            </div>
        </div>
    </div>

    <!-- KPI 3: Ingresos del Mes y Comparativo -->
    <div class="col-sm-6 col-xl-3">
        <div class="card stat-card bg-white p-3 h-100">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-muted fw-bold small">INGRESOS DEL MES</span>
                <span class="p-2 rounded-3 bg-success bg-opacity-10 text-success">
                    <i class="bi bi-cash-stack fs-5"></i>
                </span>
            </div>
            <h3 class="fw-bold text-dark mb-0">${{ number_format($currentMonthIncome, 2) }}</h3>
            <div class="small mt-2 {{ $incomeDifference >= 0 ? 'text-success' : 'text-danger' }}">
                <i class="bi {{ $incomeDifference >= 0 ? 'bi-arrow-up-right' : 'bi-arrow-down-right' }} me-1"></i>
                ${{ number_format(abs($incomeDifference), 2) }} vs mes anterior
            </div>
        </div>
    </div>

    <!-- KPI 4: Cuentas por Cobrar Pendientes -->
    <div class="col-sm-6 col-xl-3">
        <div class="card stat-card bg-white p-3 h-100">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-muted fw-bold small">POR COBRAR PENDIENTE</span>
                <span class="p-2 rounded-3 bg-warning bg-opacity-10 text-warning">
                    <i class="bi bi-wallet2 fs-5"></i>
                </span>
            </div>
            <h3 class="fw-bold text-dark mb-0">${{ number_format($totalReceivable, 2) }}</h3>
            <div class="text-muted small mt-2">
                <a href="{{ route('admin.finances.index') }}" class="text-decoration-none text-warning fw-semibold">
                    Ver {{ $pendingReceivableReservations->count() }} saldos pendientes &rarr;
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Gráficas y Secciones Operativas -->
<div class="row g-4 mb-4">
    <!-- Reservas Recientes & Próximas -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-3 bg-white p-4 h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold text-dark mb-0">
                    <i class="bi bi-calendar-range text-primary me-2"></i>Reservas & Alquileres Recientes
                </h5>
                <a href="{{ route('admin.reservations.index') }}" class="btn btn-sm btn-link text-decoration-none">Ver todas &rarr;</a>
            </div>

            @if($recentReservations->isEmpty())
                <div class="text-center py-4 text-muted">
                    <i class="bi bi-calendar-x fs-2 d-block mb-2"></i>
                    No hay reservas registradas en el sistema.
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light small">
                            <tr>
                                <th>Código</th>
                                <th>Cliente</th>
                                <th>Fechas</th>
                                <th>Estado</th>
                                <th>Total</th>
                                <th>Saldo</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentReservations as $rsv)
                                <tr>
                                    <td>
                                        <a href="{{ route('admin.reservations.show', $rsv) }}" class="fw-bold text-decoration-none">
                                            {{ $rsv->code }}
                                        </a>
                                    </td>
                                    <td>
                                        <div class="fw-semibold text-dark">{{ $rsv->client->name }}</div>
                                        <small class="text-muted">{{ $rsv->event_name }}</small>
                                    </td>
                                    <td class="small">
                                        {{ $rsv->start_date->format('d/m') }} - {{ $rsv->end_date->format('d/m/Y') }}
                                        <div class="text-muted">{{ $rsv->days_count }} día(s)</div>
                                    </td>
                                    <td>
                                        <span class="badge {{ $rsv->status === 'confirmada' ? 'bg-primary' : ($rsv->status === 'en_curso' ? 'bg-warning text-dark' : ($rsv->status === 'devuelta' ? 'bg-success' : 'bg-secondary')) }}">
                                            {{ ucfirst($rsv->status) }}
                                        </span>
                                    </td>
                                    <td class="fw-semibold">${{ number_format($rsv->total_amount, 2) }}</td>
                                    <td>
                                        @if($rsv->pending_balance > 0)
                                            <span class="text-danger fw-bold">${{ number_format($rsv->pending_balance, 2) }}</span>
                                        @else
                                            <span class="text-success"><i class="bi bi-check-circle"></i> Pagado</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    <!-- Ocupación / Distribución por Categoría de Equipo (Requerimiento 3.1) -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm rounded-3 bg-white p-4 h-100">
            <h5 class="fw-bold text-dark mb-3">
                <i class="bi bi-pie-chart text-primary me-2"></i>Inventario por Categoría
            </h5>
            <p class="text-muted small mb-3">Distribución física de unidades en catálogo para eventos:</p>

            @if(empty($categoriesDistribution))
                <div class="text-center py-4 text-muted">Sin equipos registrados.</div>
            @else
                <div class="list-group list-group-flush mb-3">
                    @foreach($categoriesDistribution as $cat => $qty)
                        <div class="list-group-item px-0 py-2 d-flex justify-content-between align-items-center">
                            <div>
                                <i class="bi bi-disc text-primary me-2"></i>
                                <span class="fw-medium">{{ $cat }}</span>
                            </div>
                            <span class="badge bg-light text-dark border">{{ $qty }} unidades</span>
                        </div>
                    @endforeach
                </div>
            @endif

            <hr>

            <div class="d-flex align-items-center justify-content-between small text-muted">
                <span><i class="bi bi-hdd-network me-1"></i>Storage Multimedia:</span>
                <span class="fw-bold text-dark">{{ $storageUsed }} ({{ $mediaCount }} archivos)</span>
            </div>
            <div class="d-flex align-items-center justify-content-between small text-muted mt-2">
                <span><i class="bi bi-tools me-1"></i>En mantenimiento técnico:</span>
                <span class="fw-bold {{ $itemsInMaintenance > 0 ? 'text-warning' : 'text-success' }}">
                    {{ $itemsInMaintenance }} artículo(s)
                </span>
            </div>
        </div>
    </div>
</div>
@endsection
