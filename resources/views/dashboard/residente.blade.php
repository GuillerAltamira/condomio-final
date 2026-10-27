@extends('layouts.app')

@section('title', 'Mi Panel')
@section('page-title', 'Portal del Residente')

@section('content')
<!-- Welcome Banner -->
<div class="card bg-primary text-white border-0 shadow-sm mb-4" style="background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%);">
    <div class="card-body p-4 p-md-5">
        <div class="row align-items-center">
            <div class="col-md-8">
                <span class="badge bg-white bg-opacity-20 text-white mb-2 px-3 py-1.5"><i class="bi bi-person-badge me-1"></i>Residente Propietario</span>
                <h3 class="fw-bold mb-1">¡Hola, {{ $user->name }}!</h3>
                <p class="mb-0 text-white-50">Bienvenido a tu panel de residencia. Consulta tus cuotas, gestiona pagos y reserva espacios comunes fácilmente.</p>
            </div>
            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                <a href="{{ route('reservas.create') }}" class="btn btn-light text-primary fw-semibold px-4 py-2 shadow-sm">
                    <i class="bi bi-calendar-plus me-1"></i>Reservar Espacio
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Unidad Habitacional Card -->
    <div class="col-lg-5">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white border-bottom py-3">
                <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-door-open-fill text-primary me-2"></i>Mi Departamento</h6>
            </div>
            <div class="card-body p-4">
                @if($departamento)
                    <div class="d-flex align-items-center gap-3 mb-4 pb-3 border-bottom">
                        <div class="stat-icon bg-primary bg-opacity-10 text-primary" style="width: 56px; height: 56px; font-size: 1.6rem;">
                            <i class="bi bi-building"></i>
                        </div>
                        <div>
                            <h4 class="fw-bold mb-0 text-dark">Unidad {{ $departamento->numero_departamento }}</h4>
                            <span class="text-muted small">Piso {{ $departamento->piso }} &bull; {{ $departamento->bloque ? 'Bloque ' . $departamento->bloque : 'Bloque Principal' }}</span>
                        </div>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted text-uppercase fw-semibold" style="font-size: 0.72rem;">Propietario Registrado</small>
                        <p class="fw-semibold text-dark mb-0">{{ $departamento->propietario ? $departamento->propietario->name : 'No asignado' }}</p>
                    </div>

                    <div>
                        <small class="text-muted text-uppercase fw-semibold" style="font-size: 0.72rem;">Residentes en la Unidad ({{ $departamento->residentes->count() }})</small>
                        <ul class="list-unstyled mt-1 mb-0">
                            @foreach($departamento->residentes as $res)
                                <li class="small py-1 text-dark d-flex align-items-center">
                                    <i class="bi bi-dot text-primary fs-4 me-1"></i>{{ $res->name }} 
                                    @if($res->id === $user->id) <span class="badge bg-light text-muted ms-2 border">Tú</span> @endif
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @else
                    <div class="text-center py-4 text-muted">
                        <i class="bi bi-info-circle fs-2 text-secondary opacity-50 mb-2 d-block"></i>
                        <p class="mb-0">Aún no tienes un departamento asignado.</p>
                        <small class="text-muted">Comunícate con la administración para vincular tu unidad.</small>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Estado Financiero Card -->
    <div class="col-lg-7">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-receipt text-primary me-2"></i>Estado de Cuotas y Expensas</h6>
                <a href="{{ route('expensas.index') }}" class="small text-primary text-decoration-none fw-semibold">Ver todo</a>
            </div>
            <div class="card-body p-4">
                @if($totalDeuda > 0)
                    <div class="alert alert-warning border border-warning-subtle d-flex justify-content-between align-items-center p-3 mb-4 rounded-3">
                        <div class="d-flex align-items-center gap-3">
                            <i class="bi bi-exclamation-triangle-fill fs-3 text-warning"></i>
                            <div>
                                <h6 class="fw-bold mb-0 text-dark">Tienes cuotas pendientes de pago</h6>
                                <span class="text-muted small">Total adeudado: <strong class="text-danger fs-6">Bs. {{ number_format($totalDeuda, 2) }}</strong></span>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="alert alert-success border border-success-subtle d-flex align-items-center gap-3 p-3 mb-4 rounded-3">
                        <i class="bi bi-check-circle-fill fs-3 text-success"></i>
                        <div>
                            <h6 class="fw-bold mb-0 text-success">¡Estás al día!</h6>
                            <span class="text-muted small">No registras cuotas de expensas pendientes ni atrasadas.</span>
                        </div>
                    </div>
                @endif

                <h6 class="fw-bold text-secondary small text-uppercase mb-2">Cuotas Recientes</h6>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Período</th>
                                <th>Monto</th>
                                <th>Estado</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($historialExpensas as $expensa)
                                <tr>
                                    <td class="fw-semibold text-dark">{{ $expensa->periodo }}</td>
                                    <td>Bs. {{ number_format($expensa->monto, 2) }}</td>
                                    <td>{!! $expensa->estado_badge !!}</td>
                                    <td>
                                        @if($expensa->estado !== 'pagado')
                                            <button type="button" class="btn btn-primary btn-sm py-1 px-2.5" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#pagoModal" 
                                                    data-id="{{ $expensa->id }}" 
                                                    data-periodo="{{ $expensa->periodo }}" 
                                                    data-monto="{{ $expensa->monto }}">
                                                <i class="bi bi-upload me-1"></i>Pagar
                                            </button>
                                        @else
                                            @if($expensa->comprobante_url)
                                                <a href="{{ $expensa->comprobante_url }}" target="_blank" class="btn btn-outline-secondary btn-sm py-1 px-2">
                                                    <i class="bi bi-file-earmark-check"></i> Recibo
                                                </a>
                                            @else
                                                <span class="text-muted small"><i class="bi bi-check2 text-success"></i> Pagado</span>
                                            @endif
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-3 text-muted">No se registran expensas.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bottom Section: Mis Reservas & Áreas Comunes Disponibles -->
<div class="row g-4">
    <!-- Mis Reservas Activas -->
    <div class="col-lg-6">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-calendar-event text-primary me-2"></i>Mis Reservas Programadas</h6>
                <a href="{{ route('reservas.create') }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-plus"></i> Nueva</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Área</th>
                                <th>Fecha & Horario</th>
                                <th>Estado</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($misReservas as $reserva)
                                <tr>
                                    <td class="fw-semibold text-dark">{{ $reserva->areaComun->nombre }}</td>
                                    <td>
                                        <div class="small fw-semibold">{{ $reserva->fecha_reserva->format('d/m/Y') }}</div>
                                        <small class="text-muted">{{ $reserva->horario_formateado }}</small>
                                    </td>
                                    <td>{!! $reserva->estado_badge !!}</td>
                                    <td>
                                        @if($reserva->estado === 'confirmada')
                                            <form method="POST" action="{{ route('reservas.cancelar', $reserva) }}" onsubmit="return confirm('¿Seguro que deseas cancelar esta reserva?');">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-danger py-1 px-2">
                                                    <i class="bi bi-x-circle"></i> Cancelar
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-muted small">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">
                                        <i class="bi bi-calendar-x fs-2 d-block mb-1 text-secondary opacity-50"></i>
                                        No tienes reservas activas.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Áreas Comunes Disponibles -->
    <div class="col-lg-6">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-tree text-primary me-2"></i>Áreas Comunes del Condominio</h6>
                <a href="{{ route('areas-comunes.index') }}" class="small text-primary text-decoration-none fw-semibold">Ver todas</a>
            </div>
            <div class="card-body p-3">
                <div class="row g-3">
                    @foreach($areasComunes->take(4) as $area)
                        <div class="col-sm-6">
                            <div class="card h-100 border overflow-hidden">
                                <img src="{{ $area->imagen_url }}" class="card-img-top" alt="{{ $area->nombre }}" style="height: 120px; object-fit: cover;">
                                <div class="card-body p-3">
                                    <h6 class="fw-bold mb-1 text-dark">{{ $area->nombre }}</h6>
                                    <small class="text-muted d-block mb-2"><i class="bi bi-people me-1"></i>Capacidad: {{ $area->capacidad }} pers.</small>
                                    <a href="{{ route('reservas.create', ['area_id' => $area->id]) }}" class="btn btn-outline-primary btn-sm w-100">
                                        Reservar
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Registrar Pago con Comprobante -->
<div class="modal fade" id="pagoModal" tabindex="-1" aria-labelledby="pagoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form id="formPagarExpensa" method="POST" action="" enctype="multipart/form-data">
                @csrf
                <div class="modal-header bg-light">
                    <h5 class="modal-title fw-bold" id="pagoModalLabel">
                        <i class="bi bi-upload text-primary me-2"></i>Registrar Pago de Expensa
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="p-3 bg-light rounded-3 mb-3 border">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted small">Período:</span>
                            <strong id="modalPeriodo" class="text-dark"></strong>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted small">Monto a pagar:</span>
                            <strong id="modalMonto" class="text-success fs-5"></strong>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="fecha_pago" class="form-label fw-semibold small">Fecha de Pago</label>
                        <input type="date" name="fecha_pago" id="fecha_pago" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>

                    <div class="mb-3">
                        <label for="metodo_pago" class="form-label fw-semibold small">Método de Pago</label>
                        <select name="metodo_pago" id="metodo_pago" class="form-select" required>
                            <option value="transferencia">Transferencia Bancaria</option>
                            <option value="qr">Pago QR</option>
                            <option value="deposito">Depósito Bancario</option>
                            <option value="efectivo">Efectivo (en Administración)</option>
                            <option value="tarjeta">Tarjeta Débito/Crédito</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="comprobante_imagen" class="form-label fw-semibold small">Adjuntar Comprobante (Foto o Recibo)</label>
                        <input type="file" name="comprobante_imagen" id="comprobante_imagen" class="form-control" accept="image/*,application/pdf">
                        <small class="text-muted">Formatos permitidos: JPG, PNG, WEBP, PDF (Máx. 4MB).</small>
                    </div>

                    <div class="mb-3">
                        <label for="notas" class="form-label fw-semibold small">Notas / Referencia de Transacción (Opcional)</label>
                        <textarea name="notas" id="notas" rows="2" class="form-control" placeholder="Ej: Nro de transacción 98234123"></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success px-4">
                        <i class="bi bi-check-lg me-1"></i>Confirmar Pago
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    const pagoModal = document.getElementById('pagoModal');
    if (pagoModal) {
        pagoModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const id = button.getAttribute('data-id');
            const periodo = button.getAttribute('data-periodo');
            const monto = button.getAttribute('data-monto');

            document.getElementById('modalPeriodo').textContent = periodo;
            document.getElementById('modalMonto').textContent = '$ ' + parseFloat(monto).toFixed(2);
            document.getElementById('formPagarExpensa').action = '/expensas/' + id + '/pagar';
        });
    }
</script>
@endpush
@endsection
