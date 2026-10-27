@extends('layouts.app')

@section('title', 'Residentes')
@section('page-title', 'Padrón de Residentes y Propietarios')

@section('content')
<div class="card shadow-sm border-0 mb-4">
    <div class="card-body p-4">
        <!-- Top bar -->
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
            <div>
                <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-people-fill text-primary me-2"></i>Residentes y Propietarios Registrados</h5>
                <small class="text-muted">Directorio de habitantes con validación estricta de CI y Correo Electrónico</small>
            </div>
            <a href="{{ route('residentes.create') }}" class="btn btn-primary shadow-sm">
                <i class="bi bi-person-plus-fill me-1"></i>Nuevo Residente
            </a>
        </div>

        <!-- Filter bar -->
        <form method="GET" action="{{ route('residentes.index') }}" class="row g-2 mb-4 bg-light p-3 rounded-3 border">
            <div class="col-md-5">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control border-start-0" placeholder="Buscar por nombre, CI, correo o teléfono..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-3">
                <select name="departamento_id" class="form-select">
                    <option value="">-- Todos los Departamentos --</option>
                    @foreach($departamentos as $depto)
                        <option value="{{ $depto->id }}" {{ request('departamento_id') == $depto->id ? 'selected' : '' }}>
                            {{ $depto->identificador_completo }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="estado" class="form-select">
                    <option value="">-- Estado --</option>
                    <option value="activo" {{ request('estado') == 'activo' ? 'selected' : '' }}>Activo</option>
                    <option value="inactivo" {{ request('estado') == 'inactivo' ? 'selected' : '' }}>Inactivo</option>
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-secondary w-100">
                    <i class="bi bi-filter"></i> Filtrar
                </button>
                @if(request()->anyFilled(['search', 'departamento_id', 'estado']))
                    <a href="{{ route('residentes.index') }}" class="btn btn-outline-secondary" title="Limpiar filtros">
                        <i class="bi bi-x-lg"></i>
                    </a>
                @endif
            </div>
        </form>

        <!-- Table -->
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Residente</th>
                        <th>CI</th>
                        <th>Contacto</th>
                        <th>Departamento Actual</th>
                        <th>Propiedades</th>
                        <th>Estado</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($residentes as $residente)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-2.5">
                                    <img src="{{ $residente->avatar_url }}" alt="{{ $residente->name }}" class="rounded-circle" style="width: 36px; height: 36px; object-fit: cover;">
                                    <div>
                                        <a href="{{ route('residentes.show', $residente) }}" class="fw-bold text-dark text-decoration-none d-block">
                                            {{ $residente->name }}
                                        </a>
                                        <small class="text-muted">{{ $residente->email }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border font-monospace">{{ $residente->ci ?? 'No registrado' }}</span>
                            </td>
                            <td>
                                <span class="small text-dark"><i class="bi bi-telephone text-muted me-1"></i>{{ $residente->telefono ?? 'Sin teléfono' }}</span>
                            </td>
                            <td>
                                @if($residente->departamento)
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle">
                                        <i class="bi bi-door-closed me-1"></i>{{ $residente->departamento->identificador_completo }}
                                    </span>
                                @else
                                    <span class="badge bg-secondary-subtle text-muted">Sin asignar</span>
                                @endif
                            </td>
                            <td>
                                @if($residente->departamentosPropiedad->isNotEmpty())
                                    <span class="badge bg-success-subtle text-success border border-success-subtle" title="{{ $residente->departamentosPropiedad->pluck('identificador_completo')->join(', ') }}">
                                        <i class="bi bi-key-fill me-1"></i>Propietario ({{ $residente->departamentosPropiedad->count() }})
                                    </span>
                                @else
                                    <span class="text-muted small">Inquilino / Habitante</span>
                                @endif
                            </td>
                            <td>
                                @if($residente->activo)
                                    <span class="badge bg-success-subtle text-success border border-success-subtle">Activo</span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle">Inactivo</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <div class="d-inline-flex gap-1">
                                    <a href="{{ route('residentes.show', $residente) }}" class="btn btn-sm btn-outline-info" title="Ver Expediente">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('residentes.edit', $residente) }}" class="btn btn-sm btn-outline-primary" title="Editar">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form method="POST" action="{{ route('residentes.destroy', $residente) }}" onsubmit="return confirm('¿Estás seguro de eliminar a este residente del sistema?');" class="d-inline">
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
                                <i class="bi bi-people fs-1 d-block mb-2 text-secondary opacity-50"></i>
                                No se encontraron residentes registrados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $residentes->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>
@endsection
