@extends('layouts.app')

@section('title', 'Panel de Control')
@section('page-title', 'Resumen General del Condominio')

@section('content')
<!-- Top Quick Actions Bar -->
<div class="d-flex flex-wrap gap-2 justify-content-between align-items-center mb-4">
    <div>
        <p class="text-muted mb-0 small">Bienvenido al sistema administrativo integral. Fecha actual: <strong>{{ date('d/m/Y') }}</strong></p>
    </div>
    <div class="d-flex flex-wrap gap-2">
        
        <a href="{{ route('gastos.create') }}" class="btn btn-outline-danger btn-sm">
            <i class="bi bi-dash-circle me-1"></i>Registrar Gasto
        </a>
        <a href="{{ route('residentes.create') }}" class="btn btn-outline-success btn-sm">
            <i class="bi bi-person-plus me-1"></i>Nuevo Residente
        </a>
        <a href="{{ route('reservas.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-calendar-plus me-1"></i>Nueva Reserva
        </a>
    </div>
</div>

<!-- Key Performance Indicators (KPI Cards) -->
<div class="row g-3 mb-4">
    <!-- Departamentos -->
    <div class="col-sm-6 col-xl-3">
        <div class="card stat-card p-3 h-100">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase">Departamentos</span>
                    <h3 class="fw-bold mt-1 mb-0">{{ $totalDeptos }}</h3>
                    <small class="text-success"><i class="bi bi-check-circle me-1"></i>{{ $deptosOcupados }} Ocupados</small>
                    <small class="text-muted"> &bull; {{ $deptosLibres }} Libres</small>
                </div>
                <div class="stat-icon bg-primary bg-opacity-10 text-primary">
                    <i class="bi bi-door-open-fill"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Residentes -->
    <div class="col-sm-6 col-xl-3">
        <div class="card stat-card p-3 h-100">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase">Residentes Activos</span>
                    <h3 class="fw-bold mt-1 mb-0">{{ $totalResidentes }}</h3>
                    <small class="text-muted">Propietarios e inquilinos</small>
                </div>
                <div class="stat-icon bg-info bg-opacity-10 text-info">
                    <i class="bi bi-people-fill"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Expensas del Mes -->
    <div class="col-sm-6 col-xl-3">
        <div class="card stat-card p-3 h-100">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase">Cobrado este Mes</span>
                    <h3 class="fw-bold mt-1 mb-0 text-success">Bs. {{ number_format($totalRecaudadoMes, 2) }}</h3>
                    <small class="text-muted">De Bs. {{ number_format($totalFacturadoMes, 2) }} facturados</small>
                </div>
                <div class="stat-icon bg-success bg-opacity-10 text-success">
                    <i class="bi bi-cash-coin"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Balance Financiero Neto -->
    <div class="col-sm-6 col-xl-3">
        <div class="card stat-card p-3 h-100">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase">Balance General</span>
                    <h3 class="fw-bold mt-1 mb-0 {{ $balanceGeneral >= 0 ? 'text-primary' : 'text-danger' }}">
                        Bs. {{ number_format($balanceGeneral, 2) }}
                    </h3>
                    <small class="text-muted">Ingresos - Gastos históricos</small>
                </div>
                <div class="stat-icon {{ $balanceGeneral >= 0 ? 'bg-primary bg-opacity-10 text-primary' : 'bg-danger bg-opacity-10 text-danger' }}">
                    <i class="bi bi-wallet2"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Financial Chart and Quick Summary -->
<div class="row g-4 mb-4">
    <!-- Chart -->
    <div class="col-lg-8">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0 text-dark">
                    <i class="bi bi-graph-up-arrow me-2 text-primary"></i>Flujo Financiero (Últimos 6 Meses)
                </h6>
                <span class="badge bg-light text-muted border">En Bs.</span>
            </div>
            <div class="card-body p-3 p-md-4">
                <div style="height: 300px; position: relative;">
                    <canvas id="financialChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Month Snapshot Card -->
    <div class="col-lg-4">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white border-bottom py-3">
                <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-pie-chart me-2 text-primary"></i>Estado Mensual</h6>
            </div>
            <div class="card-body p-4 d-flex flex-column justify-content-around">
                <div class="p-3 bg-light rounded-3 mb-3 border">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="text-muted small">Total Facturado Mes</span>
                        <strong class="text-dark">Bs. {{ number_format($totalFacturadoMes, 2) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="text-success small"><i class="bi bi-check-circle me-1"></i>Cobrado</span>
                        <strong class="text-success">Bs. {{ number_format($totalRecaudadoMes, 2) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-warning small"><i class="bi bi-hourglass-split me-1"></i>Pendiente / Mora</span>
                        <strong class="text-warning">Bs. {{ number_format($totalPendienteMes, 2) }}</strong>
                    </div>
                    <!-- Progress Bar -->
                    @php
                        $porcentajeCobrado = $totalFacturadoMes > 0 ? round(($totalRecaudadoMes / $totalFacturadoMes) * 100) : 0;
                    @endphp
                    <div class="progress mt-3" style="height: 8px;">
                        <div class="progress-bar bg-success" role="progressbar" style="width: {{ $porcentajeCobrado }}%"></div>
                    </div>
                    <small class="text-muted d-block text-end mt-1 font-monospace" style="font-size: 0.72rem;">{{ $porcentajeCobrado }}% recaudado</small>
                </div>

                <div class="p-3 bg-danger bg-opacity-10 rounded-3 border border-danger-subtle">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-danger small fw-semibold d-block">Gastos del Mes</span>
                            <small class="text-muted">Mantenimiento, servicios, etc.</small>
                        </div>
                        <h4 class="fw-bold text-danger mb-0">Bs. {{ number_format($totalGastosMes, 2) }}</h4>
                    </div>
                </div>

                <div class="mt-3 text-center">
                    <a href="{{ route('gastos.index') }}" class="btn btn-outline-secondary btn-sm w-100">
                        <i class="bi bi-journal-text me-1"></i>Ver Detalle de Gastos
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bottom Section: Proximas Reservas & Ultimos Pagos -->
<div class="row g-4">
    <!-- Proximas Reservas -->
    <div class="col-lg-6">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-calendar-check me-2 text-primary"></i>Próximas Reservas de Áreas Comunes</h6>
                <a href="{{ route('reservas.index') }}" class="small text-primary text-decoration-none fw-semibold">Ver todas</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Área</th>
                                <th>Residente</th>
                                <th>Fecha & Horario</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($proximasReservas as $reserva)
                                <tr>
                                    <td>
                                        <div class="fw-semibold text-dark">{{ $reserva->areaComun->nombre }}</div>
                                    </td>
                                    <td>
                                        <span class="d-block text-dark small fw-medium">{{ $reserva->residente->name }}</span>
                                        <small class="text-muted">{{ $reserva->residente->departamento ? $reserva->residente->departamento->identificador_completo : 'Sin depto' }}</small>
                                    </td>
                                    <td>
                                        <div class="small fw-semibold text-dark">{{ $reserva->fecha_reserva->format('d/m/Y') }}</div>
                                        <small class="text-muted"><i class="bi bi-clock me-1"></i>{{ $reserva->horario_formateado }}</small>
                                    </td>
                                    <td>{!! $reserva->estado_badge !!}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">
                                        <i class="bi bi-calendar-x fs-3 d-block mb-1 text-secondary opacity-50"></i>
                                        No hay reservas programadas próximamente.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Últimos Pagos Registrados -->
    <div class="col-lg-6">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-check-circle me-2 text-success"></i>Últimos Pagos Registrados</h6>
                <a href="{{ route('expensas.index') }}" class="small text-primary text-decoration-none fw-semibold">Ver historial</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Depto</th>
                                <th>Período</th>
                                <th>Monto</th>
                                <th>Fecha</th>
                                <th>Comprobante</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($ultimosPagos as $pago)
                                <tr>
                                    <td>
                                        <span class="fw-bold text-dark">{{ $pago->departamento->identificador_completo }}</span>
                                    </td>
                                    <td>{{ $pago->periodo }}</td>
                                    <td class="text-success fw-bold">Bs. {{ number_format($pago->monto, 2) }}</td>
                                    <td class="small text-muted">{{ $pago->fecha_pago ? $pago->fecha_pago->format('d/m/Y') : '-' }}</td>
                                    <td>
                                        @if($pago->comprobante_url)
                                            <a href="{{ $pago->comprobante_url }}" target="_blank" class="btn btn-sm btn-outline-info py-0 px-2">
                                                <i class="bi bi-file-earmark-image"></i> Ver
                                            </a>
                                        @else
                                            <span class="text-muted small">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">
                                        <i class="bi bi-receipt fs-3 d-block mb-1 text-secondary opacity-50"></i>
                                        No hay pagos recientes registrados.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const ctx = document.getElementById('financialChart');
        if (ctx) {
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: {!! json_encode($chartLabels) !!},
                    datasets: [
                        {
                            label: 'Ingresos por Expensas (Bs.)',
                            data: {!! json_encode($chartIngresos) !!},
                            backgroundColor: 'rgba(37, 99, 235, 0.85)',
                            borderColor: 'rgb(37, 99, 235)',
                            borderWidth: 1,
                            borderRadius: 6,
                        },
                        {
                            label: 'Egresos / Gastos (Bs.)',
                            data: {!! json_encode($chartGastos) !!},
                            backgroundColor: 'rgba(239, 68, 68, 0.75)',
                            borderColor: 'rgb(239, 68, 68)',
                            borderWidth: 1,
                            borderRadius: 6,
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: {
                                font: {
                                    family: "'Plus Jakarta Sans', sans-serif",
                                    weight: 500
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(226, 232, 240, 0.8)'
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });
        }
    });
</script>
@endpush
@endsection
