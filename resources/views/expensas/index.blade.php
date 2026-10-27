@extends('layouts.app')

@section('title', 'Expensas y Cuotas')
@section('page-title', Auth::user()->isAdmin() ? 'Gestión de Expensas y Cobranzas' : 'Mis Cuotas de Expensas')

@section('content')
<!-- Financial Summary Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card stat-card p-3 h-100">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase">Total Facturado</span>
                    <h3 class="fw-bold mt-1 mb-0 text-dark">Bs. {{ number_format($totalMonto, 2) }}</h3>
                    <small class="text-muted">Total cuotas registradas</small>
                </div>
                <div class="stat-icon bg-primary bg-opacity-10 text-primary">
                    <i class="bi bi-calculator"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card stat-card p-3 h-100">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase">Total Recaudado</span>
                    <h3 class="fw-bold mt-1 mb-0 text-success">Bs. {{ number_format($totalPagado, 2) }}</h3>
                    <small class="text-success"><i class="bi bi-check-circle me-1"></i>Pagos confirmados</small>
                </div>
                <div class="stat-icon bg-success bg-opacity-10 text-success">
                    <i class="bi bi-check2-circle"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card stat-card p-3 h-100">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase">Saldo Pendiente / Mora</span>
                    <h3 class="fw-bold mt-1 mb-0 text-warning">Bs. {{ number_format($totalPendiente, 2) }}</h3>
                    <small class="text-danger"><i class="bi bi-hourglass-split me-1"></i>Por cobrar</small>
                </div>
                <div class="stat-icon bg-warning bg-opacity-10 text-warning">
                    <i class="bi bi-exclamation-triangle"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm border-0 mb-4">
    <div class="card-body p-4">
        <!-- Header Actions -->
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
            <div>
                <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-receipt-cutoff text-primary me-2"></i>Historial de Expensas</h5>
                <small class="text-muted">Listado detallado de cuotas ordinarias y extraordinarias</small>
            </div>
            @if(Auth::user()->isAdmin())
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('expensas.create') }}" class="btn btn-primary shadow-sm">
                        <i class="bi bi-plus-lg me-1"></i>Nueva Expensa Individual
                    </a>
                </div>
            @endif
        </div>

        @if($sinDepartamento)
            <div class="alert alert-info d-flex align-items-center">
                <i class="bi bi-info-circle-fill me-2 fs-5"></i>
                <div>Actualmente no tienes un departamento asignado a tu cuenta. Contacta a la administración para asociar tu unidad.</div>
            </div>
        @else
            <!-- Filters -->
            <form method="GET" action="{{ route('expensas.index') }}" class="row g-2 mb-4 bg-light p-3 rounded-3 border">
                @if(Auth::user()->isAdmin())
                    <div class="col-md-4">
                        <select name="departamento_id" class="form-select">
                            <option value="">-- Todos los Departamentos --</option>
                            @foreach($departamentos as $depto)
                                <option value="{{ $depto->id }}" {{ request('departamento_id') == $depto->id ? 'selected' : '' }}>
                                    {{ $depto->identificador_completo }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <div class="col-md-2">
                    <select name="estado" class="form-select">
                        <option value="">-- Todos los Estados --</option>
                        <option value="pendiente" {{ request('estado') == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                        <option value="pagado" {{ request('estado') == 'pagado' ? 'selected' : '' }}>Pagado</option>
                        <option value="atrasado" {{ request('estado') == 'atrasado' ? 'selected' : '' }}>Atrasado</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <select name="mes" class="form-select">
                        <option value="">-- Todos los Meses --</option>
                        @for($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}" {{ request('mes') == $m ? 'selected' : '' }}>
                                {{ DateTime::createFromFormat('!m', $m)->format('F') }}
                            </option>
                        @endfor
                    </select>
                </div>

                <div class="col-md-2">
                    <select name="anio" class="form-select">
                        <option value="">-- Todos los Años --</option>
                        @for($y = date('Y') + 1; $y >= 2024; $y--)
                            <option value="{{ $y }}" {{ request('anio') == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>

                <div class="{{ Auth::user()->isAdmin() ? 'col-md-2' : 'col-md-4' }} d-flex gap-2">
                    <button type="submit" class="btn btn-secondary w-100">
                        <i class="bi bi-filter"></i> Filtrar
                    </button>
                    @if(request()->anyFilled(['departamento_id', 'estado', 'mes', 'anio']))
                        <a href="{{ route('expensas.index') }}" class="btn btn-outline-secondary" title="Limpiar">
                            <i class="bi bi-x-lg"></i>
                        </a>
                    @endif
                </div>
            </form>

            <!-- Expensas Table -->
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Departamento</th>
                            <th>Período</th>
                            <th>Monto</th>
                            <th>Estado</th>
                            <th>Fecha Pago</th>
                            <th>Método</th>
                            <th>Comprobante</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($expensas as $expensa)
                            <tr>
                                <td>
                                    <div class="fw-bold text-dark">{{ $expensa->departamento->identificador_completo }}</div>
                                    <small class="text-muted">Prop: {{ $expensa->departamento->propietario ? $expensa->departamento->propietario->name : 'Sin asignar' }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border px-2.5 py-1.5 fw-semibold">{{ $expensa->periodo }}</span>
                                </td>
                                <td>
                                    <strong class="text-dark fs-6">Bs. {{ number_format($expensa->monto, 2) }}</strong>
                                </td>
                                <td>{!! $expensa->estado_badge !!}</td>
                                <td>
                                    <span class="small text-muted">{{ $expensa->fecha_pago ? $expensa->fecha_pago->format('d/m/Y') : '-' }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-light text-muted border text-capitalize">{{ $expensa->metodo_pago ?? '-' }}</span>
                                </td>
                                <td>
                                    @if($expensa->comprobante_url)
                                        <a href="{{ $expensa->comprobante_url }}" target="_blank" class="btn btn-sm btn-outline-info py-1 px-2.5 shadow-sm">
                                            <i class="bi bi-file-earmark-image me-1"></i>Ver
                                        </a>
                                    @else
                                        <span class="text-muted small">Sin adjunto</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <div class="d-inline-flex gap-1">
                                        @if($expensa->estado !== 'pagado')
                                            <!-- Pagar Button (Accessible for Resident and Admin) -->
                                            <button type="button" class="btn btn-primary btn-sm py-1 px-2.5 shadow-sm"
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#pagoModal" 
                                                    data-id="{{ $expensa->id }}" 
                                                    data-periodo="{{ $expensa->periodo }}" 
                                                    data-monto="{{ $expensa->monto }}">
                                                <i class="bi bi-upload me-1"></i>Pagar
                                            </button>
                                        @endif

                                        @if(Auth::user()->isAdmin())
                                            <!-- Admin Status Quick Toggle Dropdown -->
                                            <div class="dropdown d-inline">
                                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle py-1 px-2" type="button" data-bs-toggle="dropdown">
                                                    Estado
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                                                    <li>
                                                        <form method="POST" action="{{ route('expensas.estado', $expensa) }}">
                                                            @csrf
                                                            <input type="hidden" name="estado" value="pagado">
                                                            <button type="submit" class="dropdown-item small text-success">
                                                                <i class="bi bi-check-circle me-2"></i>Marcar como Pagado
                                                            </button>
                                                        </form>
                                                    </li>
                                                    <li>
                                                        <form method="POST" action="{{ route('expensas.estado', $expensa) }}">
                                                            @csrf
                                                            <input type="hidden" name="estado" value="pendiente">
                                                            <button type="submit" class="dropdown-item small text-warning">
                                                                <i class="bi bi-hourglass-split me-2"></i>Marcar como Pendiente
                                                            </button>
                                                        </form>
                                                    </li>
                                                    <li>
                                                        <form method="POST" action="{{ route('expensas.estado', $expensa) }}">
                                                            @csrf
                                                            <input type="hidden" name="estado" value="atrasado">
                                                            <button type="submit" class="dropdown-item small text-danger">
                                                                <i class="bi bi-exclamation-triangle me-2"></i>Marcar como Atrasado
                                                            </button>
                                                        </form>
                                                    </li>
                                                </ul>
                                            </div>

                                            <form method="POST" action="{{ route('expensas.destroy', $expensa) }}" onsubmit="return confirm('¿Seguro que deseas eliminar este registro de expensa?');" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger py-1 px-2" title="Eliminar">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">
                                    <i class="bi bi-receipt fs-1 d-block mb-2 text-secondary opacity-50"></i>
                                    No se encontraron expensas registradas con los filtros seleccionados.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $expensas->links('pagination::bootstrap-5') }}
            </div>
        @endif
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
                        <label for="fecha_pago" class="form-label fw-semibold small">Fecha del Pago</label>
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
                        <small class="text-muted">Formatos: JPG, PNG, WEBP, PDF (Máx. 4MB).</small>
                    </div>

                    <div class="mb-3">
                        <label for="notas" class="form-label fw-semibold small">Notas / Referencia de Pago</label>
                        <textarea name="notas" id="notas" rows="2" class="form-control" placeholder="Ej: Transferencia Banco Unión nro #98124"></textarea>
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
