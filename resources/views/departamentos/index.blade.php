@extends('layouts.app')

@section('title', 'Departamentos')
@section('page-title', 'Unidades Habitacionales')

@section('content')
<div class="card shadow-sm border-0 mb-4">
    <div class="card-body p-4">
        <!-- Top bar with actions -->
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
            <div>
                <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-door-open-fill text-primary me-2"></i>Listado de Departamentos</h5>
                <small class="text-muted">Administración y asignación de unidades residenciales</small>
            </div>
            <a href="{{ route('departamentos.create') }}" class="btn btn-primary shadow-sm">
                <i class="bi bi-plus-lg me-1"></i>Nuevo Departamento
            </a>
        </div>

        <!-- Filter and Search -->
        <form method="GET" action="{{ route('departamentos.index') }}" class="row g-2 mb-4 bg-light p-3 rounded-3 border">
            <div class="col-md-5">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control border-start-0" placeholder="Buscar por número, bloque o propietario..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-3">
                <select name="bloque" class="form-select">
                    <option value="">-- Todos los Bloques --</option>
                    @foreach($bloques as $b)
                        <option value="{{ $b }}" {{ request('bloque') == $b ? 'selected' : '' }}>Bloque {{ $b }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="piso" class="form-select">
                    <option value="">-- Todos los Pisos --</option>
                    @foreach($pisos as $p)
                        <option value="{{ $p }}" {{ request('piso') == $p ? 'selected' : '' }}>Piso {{ $p }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-secondary w-100">
                    <i class="bi bi-filter"></i> Filtrar
                </button>
                @if(request()->anyFilled(['search', 'bloque', 'piso']))
                    <a href="{{ route('departamentos.index') }}" class="btn btn-outline-secondary" title="Limpiar filtros">
                        <i class="bi bi-x-lg"></i>
                    </a>
                @endif
            </div>
        </form>

        <!-- Departamentos Table -->
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Unidad / Bloque</th>
                        <th>Piso</th>
                        <th>Propietario</th>
                        <th>Residentes Asignados</th>
                        <th>Historial Expensas</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($departamentos as $depto)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="stat-icon bg-light text-primary border" style="width: 38px; height: 38px; font-size: 1.1rem;">
                                        <i class="bi bi-door-closed"></i>
                                    </div>
                                    <div>
                                        <span class="fw-bold text-dark d-block">Depto {{ $depto->numero_departamento }}</span>
                                        <small class="text-muted">{{ $depto->bloque ? 'Bloque ' . $depto->bloque : 'Bloque Único' }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border px-2.5 py-1">Piso {{ $depto->piso }}</span>
                            </td>
                            <td>
                                @if($depto->propietario)
                                    <div class="d-flex align-items-center gap-2">
                                        <img src="{{ $depto->propietario->avatar_url }}" alt="{{ $depto->propietario->name }}" class="rounded-circle" style="width: 28px; height: 28px; object-fit: cover;">
                                        <div>
                                            <span class="d-block fw-semibold text-dark small lh-1">{{ $depto->propietario->name }}</span>
                                            <small class="text-muted" style="font-size: 0.72rem;">CI: {{ $depto->propietario->ci ?? 'S/N' }}</small>
                                        </div>
                                    </div>
                                @else
                                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle">Sin Propietario</span>
                                @endif
                            </td>
                            <td>
                                @if($depto->residentes_count > 0)
                                    <span class="badge bg-info-subtle text-info border border-info-subtle px-2 py-1">
                                        <i class="bi bi-people me-1"></i>{{ $depto->residentes_count }} {{ Str::plural('residente', $depto->residentes_count) }}
                                    </span>
                                @else
                                    <span class="text-muted small">Desocupado</span>
                                @endif
                            </td>
                            <td>
                                <span class="small text-muted">{{ $depto->expensas_count }} registradas</span>
                            </td>
                            <td class="text-end">
                                <div class="d-inline-flex gap-1">
                                    <a href="{{ route('departamentos.edit', $depto) }}" class="btn btn-sm btn-outline-primary" title="Editar">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form method="POST" action="{{ route('departamentos.destroy', $depto) }}" onsubmit="return confirm('¿Estás seguro de eliminar este departamento?');" class="d-inline">
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
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="bi bi-building-exclamation fs-1 d-block mb-2 text-secondary opacity-50"></i>
                                No se encontraron departamentos registrados con los criterios especificados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $departamentos->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>
@endsection
