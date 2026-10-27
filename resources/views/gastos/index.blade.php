@extends('layouts.app')

@section('title', 'Gastos y Egresos')
@section('page-title', 'Gastos y Egresos de la Administración')

@section('content')
<!-- Financial Transparency Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card stat-card p-3 h-100">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase">Total Recaudado</span>
                    <h3 class="fw-bold mt-1 mb-0 text-success">Bs. {{ number_format($totalExpensasRecaudadas, 2) }}</h3>
                    <small class="text-muted">Por expensas canceladas</small>
                </div>
                <div class="stat-icon bg-success bg-opacity-10 text-success">
                    <i class="bi bi-graph-up-arrow"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card stat-card p-3 h-100">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase">Total Egresos</span>
                    <h3 class="fw-bold mt-1 mb-0 text-danger">Bs. {{ number_format($totalGastos, 2) }}</h3>
                    <small class="text-muted">Según filtros aplicados</small>
                </div>
                <div class="stat-icon bg-danger bg-opacity-10 text-danger">
                    <i class="bi bi-graph-down-arrow"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card stat-card p-3 h-100">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase">Balance Neto en Caja</span>
                    <h3 class="fw-bold mt-1 mb-0 {{ $balanceGeneral >= 0 ? 'text-primary' : 'text-danger' }}">
                        Bs. {{ number_format($balanceGeneral, 2) }}
                    </h3>
                    <small class="text-muted">Superávit / Déficit histórico</small>
                </div>
                <div class="stat-icon {{ $balanceGeneral >= 0 ? 'bg-primary bg-opacity-10 text-primary' : 'bg-danger bg-opacity-10 text-danger' }}">
                    <i class="bi bi-wallet2"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm border-0 mb-4">
    <div class="card-body p-4">
        <!-- Top bar -->
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
            <div>
                <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-cash-stack text-primary me-2"></i>Libro de Egresos y Gastos</h5>
                <small class="text-muted">Registro contable y transparente de todos los desembolsos de la copropiedad</small>
            </div>
            <a href="{{ route('gastos.create') }}" class="btn btn-primary shadow-sm">
                <i class="bi bi-dash-circle me-1"></i>Registrar Nuevo Gasto
            </a>
        </div>

        <!-- Filters -->
        <form method="GET" action="{{ route('gastos.index') }}" class="row g-2 mb-4 bg-light p-3 rounded-3 border">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control" placeholder="Buscar por concepto o detalle..." value="{{ request('search') }}">
            </div>

            <div class="col-md-3">
                <select name="categoria" class="form-select">
                    <option value="">-- Todas las Categorías --</option>
                    @foreach($categorias as $key => $label)
                        <option value="{{ $key }}" {{ request('categoria') == $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2">
                @php
                    $mesesEnEspanol = [
                        1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
                        5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
                        9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
                    ];
                @endphp
                <select name="mes" class="form-select">
                    <option value="">-- Mes --</option>
                    @for($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ request('mes') == $m ? 'selected' : '' }}>
                            {{ $mesesEnEspanol[$m] }}
                        </option>
                    @endfor
                </select>
            </div>

            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-secondary w-100">
                    <i class="bi bi-filter"></i> Filtrar
                </button>
                @if(request()->anyFilled(['search', 'categoria', 'mes', 'anio']))
                    <a href="{{ route('gastos.index') }}" class="btn btn-outline-secondary" title="Limpiar filtros">
                        <i class="bi bi-x-lg"></i>
                    </a>
                @endif
            </div>
        </form>

        <!-- Gastos Table -->
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Descripción del Gasto</th>
                        <th>Categoría</th>
                        <th>Monto</th>
                        <th>Comprobante / Factura</th>
                        <th>Registrado Por</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($gastos as $gasto)
                        <tr>
                            <td>
                                <span class="fw-semibold text-dark">{{ $gasto->fecha->format('d/m/Y') }}</span>
                            </td>
                            <td>
                                <span class="fw-bold text-dark d-block">{{ $gasto->descripcion }}</span>
                            </td>
                            <td>{!! $gasto->categoria_badge !!}</td>
                            <td>
                                <strong class="text-danger fs-6">Bs. {{ number_format($gasto->monto, 2) }}</strong>
                            </td>
                            <td>
                                @if($gasto->comprobante_url)
                                    <a href="{{ $gasto->comprobante_url }}" target="_blank" class="btn btn-sm btn-outline-info py-0 px-2.5">
                                        <i class="bi bi-file-earmark-pdf"></i> Ver Factura
                                    </a>
                                @else
                                    <span class="text-muted small">Sin adjunto</span>
                                @endif
                            </td>
                            <td>
                                <span class="small text-muted">{{ $gasto->registrador ? $gasto->registrador->name : 'Sistema' }}</span>
                            </td>
                            <td class="text-end">
                                <div class="d-inline-flex gap-1">
                                    <a href="{{ route('gastos.edit', $gasto) }}" class="btn btn-sm btn-outline-primary" title="Editar">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form method="POST" action="{{ route('gastos.destroy', $gasto) }}" onsubmit="return confirm('¿Seguro que deseas eliminar este registro de gasto?');" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Eliminar">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="bi bi-journal-x fs-1 d-block mb-2 text-secondary opacity-50"></i>
                                No se encontraron gastos registrados con los filtros seleccionados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $gastos->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>
@endsection