@extends('layouts.app')

@section('title', 'Áreas Comunes')
@section('page-title', 'Áreas Comunes y Espacios Compartidos')

@section('content')
<div class="card shadow-sm border-0 mb-4">
    <div class="card-body p-4">
        <!-- Top bar -->
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
            <div>
                <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-tree-fill text-primary me-2"></i>Espacios Sociales y Recreativos</h5>
                <small class="text-muted">Áreas disponibles para el uso y esparcimiento de los residentes</small>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('reservas.create') }}" class="btn btn-outline-primary shadow-sm">
                    <i class="bi bi-calendar-plus me-1"></i>Hacer una Reserva
                </a>
                @if(Auth::user()->isAdmin())
                    <a href="{{ route('areas-comunes.create') }}" class="btn btn-primary shadow-sm">
                        <i class="bi bi-plus-lg me-1"></i>Nueva Área Común
                    </a>
                @endif
            </div>
        </div>

        <!-- Areas Grid -->
        <div class="row g-4">
            @forelse($areas as $area)
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 shadow-sm border overflow-hidden">
                        <!-- Image banner -->
                        <div style="height: 200px; position: relative; overflow: hidden; background-color: #0f172a;">
                            <img src="{{ $area->imagen_url }}" alt="{{ $area->nombre }}" class="w-100 h-100" style="object-fit: cover;">
                            <div style="position: absolute; top: 12px; right: 12px;">
                                @if($area->activo)
                                    <span class="badge bg-success shadow-sm"><i class="bi bi-check-circle me-1"></i>Habilitado</span>
                                @else
                                    <span class="badge bg-danger shadow-sm"><i class="bi bi-slash-circle me-1"></i>Mantenimiento</span>
                                @endif
                            </div>
                            <div style="position: absolute; bottom: 12px; left: 12px;">
                                <span class="badge bg-dark bg-opacity-75 text-white shadow-sm px-2.5 py-1.5">
                                    <i class="bi bi-people-fill me-1 text-info"></i>Capacidad: {{ $area->capacidad }} personas
                                </span>
                            </div>
                        </div>

                        <!-- Card Body -->
                        <div class="card-body p-4 d-flex flex-column justify-content-between">
                            <div>
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h5 class="fw-bold text-dark mb-0">{{ $area->nombre }}</h5>
                                    <span class="fw-bold text-primary small">{{ $area->costo_formateado }}</span>
                                </div>
                                <p class="text-muted small mb-3">
                                    {{ Str::limit($area->descripcion, 130, '...') }}
                                </p>

                                @if($area->normas)
                                    <div class="mb-3 p-2.5 bg-light rounded-3 border">
                                        <small class="fw-semibold text-secondary d-block mb-1"><i class="bi bi-shield-exclamation me-1"></i>Normas clave:</small>
                                        <small class="text-muted d-block" style="font-size: 0.78rem;">{{ Str::limit($area->normas, 100) }}</small>
                                    </div>
                                @endif
                            </div>

                            <div class="pt-3 border-top mt-auto">
                                <div class="d-flex gap-2">
                                    @if($area->activo)
                                        <a href="{{ route('reservas.create', ['area_id' => $area->id]) }}" class="btn btn-primary btn-sm flex-grow-1">
                                            <i class="bi bi-calendar-check me-1"></i>Reservar
                                        </a>
                                    @else
                                        <button class="btn btn-secondary btn-sm flex-grow-1" disabled>No Disponible</button>
                                    @endif

                                    @if(Auth::user()->isAdmin())
                                        <a href="{{ route('areas-comunes.edit', $area) }}" class="btn btn-outline-primary btn-sm px-2.5" title="Editar">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form method="POST" action="{{ route('areas-comunes.destroy', $area) }}" onsubmit="return confirm('¿Seguro que deseas eliminar esta área común?');" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger btn-sm px-2.5" title="Eliminar">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center py-5 text-muted">
                    <i class="bi bi-tree fs-1 d-block mb-2 text-secondary opacity-50"></i>
                    No hay áreas comunes registradas actualmente en el condominio.
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
