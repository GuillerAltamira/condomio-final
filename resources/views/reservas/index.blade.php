@extends('layouts.app')

@section('title', 'Reservas')
@section('page-title', Auth::user()->isAdmin() ? 'Gestión de Reservas de Espacios' : 'Mis Reservas de Áreas Comunes')

@section('content')
<div class="card shadow-sm border-0 mb-4">
    <div class="card-body p-4">
        <!-- Top bar -->
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
            <div>
                <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-calendar-check-fill text-primary me-2"></i>Agenda de Reservas</h5>
                <small class="text-muted">Control de reservas para evitar solapamiento de horarios</small>
            </div>
            <a href="{{ route('reservas.create') }}" class="btn btn-primary shadow-sm">
                <i class="bi bi-calendar-plus me-1"></i>Hacer Nueva Reserva
            </a>
        </div>

        <!-- Filters -->
        <form method="GET" action="{{ route('reservas.index') }}" class="row g-2 mb-4 bg-light p-3 rounded-3 border">
            <div class="col-md-3">
                <select name="area_comun_id" class="form-select">
                    <option value="">-- Todas las Áreas Comunes --</option>
                    @foreach($areas as $area)
                        <option value="{{ $area->id }}" {{ request('area_comun_id') == $area->id ? 'selected' : '' }}>
                            {{ $area->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>

            @if(Auth::user()->isAdmin())
                <div class="col-md-3">
                    <select name="residente_id" class="form-select">
                        <option value="">-- Todos los Residentes --</option>
                        @foreach($residentes as $res)
                            <option value="{{ $res->id }}" {{ request('residente_id') == $res->id ? 'selected' : '' }}>
                                {{ $res->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif

            <div class="col-md-2">
                <select name="estado" class="form-select">
                    <option value="">-- Estado --</option>
                    <option value="confirmada" {{ request('estado') == 'confirmada' ? 'selected' : '' }}>Confirmada</option>
                    <option value="cancelada" {{ request('estado') == 'cancelada' ? 'selected' : '' }}>Cancelada</option>
                </select>
            </div>

            <div class="col-md-2">
                <input type="date" name="fecha" class="form-control" value="{{ request('fecha') }}" placeholder="Fecha">
            </div>

            <div class="{{ Auth::user()->isAdmin() ? 'col-md-2' : 'col-md-5' }} d-flex gap-2">
                <button type="submit" class="btn btn-secondary w-100">
                    <i class="bi bi-filter"></i> Filtrar
                </button>
                @if(request()->anyFilled(['area_comun_id', 'residente_id', 'estado', 'fecha']))
                    <a href="{{ route('reservas.index') }}" class="btn btn-outline-secondary" title="Limpiar filtros">
                        <i class="bi bi-x-lg"></i>
                    </a>
                @endif
            </div>
        </form>

        <!-- Reservations Table -->
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Área Común</th>
                        <th>Residente</th>
                        <th>Fecha</th>
                        <th>Horario</th>
                        <th>Motivo</th>
                        <th>Estado</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reservas as $reserva)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <img src="{{ $reserva->areaComun->imagen_url }}" alt="{{ $reserva->areaComun->nombre }}" class="rounded" style="width: 44px; height: 44px; object-fit: cover;">
                                    <div>
                                        <span class="fw-bold text-dark d-block">{{ $reserva->areaComun->nombre }}</span>
                                        <small class="text-muted"><i class="bi bi-people me-1"></i>Cap. {{ $reserva->areaComun->capacidad }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="d-block fw-semibold text-dark small">{{ $reserva->residente->name }}</span>
                                <small class="text-muted">{{ $reserva->residente->departamento ? $reserva->residente->departamento->identificador_completo : 'Sin depto' }}</small>
                            </td>
                            <td>
                                <span class="fw-semibold text-dark">{{ $reserva->fecha_reserva->format('d/m/Y') }}</span>
                                <small class="d-block text-muted">{{ $reserva->fecha_reserva->translatedFormat('l') }}</small>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border px-2.5 py-1.5 font-monospace">
                                    <i class="bi bi-clock me-1 text-primary"></i>{{ $reserva->horario_formateado }}
                                </span>
                            </td>
                            <td>
                                <span class="small text-muted">{{ $reserva->motivo ? Str::limit($reserva->motivo, 40) : 'Sin motivo registrado' }}</span>
                            </td>
                            <td>{!! $reserva->estado_badge !!}</td>
                            <td class="text-end">
                                <div class="d-inline-flex gap-1">
                                    @if($reserva->estado === 'confirmada')
                                        <form method="POST" action="{{ route('reservas.cancelar', $reserva) }}" onsubmit="return confirm('¿Seguro que deseas cancelar esta reserva?');">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-warning py-1 px-2.5" title="Cancelar Reserva">
                                                <i class="bi bi-x-circle me-1"></i>Cancelar
                                            </button>
                                        </form>
                                    @endif

                                    @if(Auth::user()->isAdmin())
                                        <form method="POST" action="{{ route('reservas.destroy', $reserva) }}" onsubmit="return confirm('¿Eliminar esta reserva definitivamente del historial?');" class="d-inline">
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
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="bi bi-calendar-x fs-1 d-block mb-2 text-secondary opacity-50"></i>
                                No se encontraron reservas que coincidan con la búsqueda.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $reservas->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>
@endsection
