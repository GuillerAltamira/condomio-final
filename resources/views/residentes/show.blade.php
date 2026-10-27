@extends('layouts.app')

@section('title', 'Expediente del Residente')
@section('page-title', 'Expediente del Residente')

@section('content')
<div class="row g-4 mb-4">
    <!-- Resident Profile Card -->
    <div class="col-lg-4">
        <div class="card shadow-sm h-100 border-0">
            <div class="card-body p-4 text-center">
                <img src="{{ $residente->avatar_url }}" alt="{{ $residente->name }}" class="rounded-circle shadow-sm mb-3" style="width: 100px; height: 100px; object-fit: cover;">
                <h5 class="fw-bold text-dark mb-1">{{ $residente->name }}</h5>
                <div class="mb-3">{!! $residente->rol_badge !!}</div>

                <div class="list-group list-group-flush text-start border-top pt-3">
                    <div class="list-group-item px-0 py-2 d-flex justify-content-between align-items-center">
                        <span class="text-muted small">Carnet de Identidad:</span>
                        <strong class="font-monospace text-dark">{{ $residente->ci ?? 'No registrado' }}</strong>
                    </div>
                    <div class="list-group-item px-0 py-2 d-flex justify-content-between align-items-center">
                        <span class="text-muted small">Correo Electrónico:</span>
                        <span class="text-dark small fw-medium">{{ $residente->email }}</span>
                    </div>
                    <div class="list-group-item px-0 py-2 d-flex justify-content-between align-items-center">
                        <span class="text-muted small">Teléfono:</span>
                        <span class="text-dark small fw-medium">{{ $residente->telefono ?? 'Sin teléfono' }}</span>
                    </div>
                    <div class="list-group-item px-0 py-2 d-flex justify-content-between align-items-center">
                        <span class="text-muted small">Estado de Cuenta:</span>
                        @if($residente->activo)
                            <span class="badge bg-success-subtle text-success">Activo</span>
                        @else
                            <span class="badge bg-danger-subtle text-danger">Inactivo</span>
                        @endif
                    </div>
                    <div class="list-group-item px-0 py-2 d-flex justify-content-between align-items-center">
                        <span class="text-muted small">Unidad Asignada:</span>
                        <span class="fw-semibold text-primary small">
                            {{ $residente->departamento ? $residente->departamento->identificador_completo : 'Ninguna' }}
                        </span>
                    </div>
                </div>

                @if($residente->departamentosPropiedad->isNotEmpty())
                    <div class="mt-3 p-3 bg-success bg-opacity-10 rounded-3 text-start border border-success-subtle">
                        <span class="text-success small fw-bold d-block mb-1"><i class="bi bi-key-fill me-1"></i>Inmuebles en Propiedad:</span>
                        <ul class="mb-0 small ps-3">
                            @foreach($residente->departamentosPropiedad as $prop)
                                <li>{{ $prop->identificador_completo }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="mt-4 d-flex gap-2 justify-content-center">
                    <a href="{{ route('residentes.edit', $residente) }}" class="btn btn-outline-primary btn-sm px-3">
                        <i class="bi bi-pencil me-1"></i>Editar Datos
                    </a>
                    <a href="{{ route('residentes.index') }}" class="btn btn-light border btn-sm px-3">
                        <i class="bi bi-arrow-left me-1"></i>Volver
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Activity & History Tabs -->
    <div class="col-lg-8">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white border-bottom py-3">
                <ul class="nav nav-pills card-header-pills" id="residentTabs" role="tablist">
                    <li class="nav-item">
                        <button class="nav-link active" id="expensas-tab" data-bs-toggle="pill" data-bs-target="#expensas-pane" type="button" role="tab">
                            <i class="bi bi-receipt me-1"></i>Expensas del Departamento ({{ $expensas->count() }})
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" id="reservas-tab" data-bs-toggle="pill" data-bs-target="#reservas-pane" type="button" role="tab">
                            <i class="bi bi-calendar-check me-1"></i>Reservas Realizadas ({{ $residente->reservas->count() }})
                        </button>
                    </li>
                </ul>
            </div>
            <div class="card-body p-4">
                <div class="tab-content" id="residentTabsContent">
                    <!-- Expensas Tab -->
                    <div class="tab-pane fade show active" id="expensas-pane" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Período</th>
                                        <th>Monto</th>
                                        <th>Estado</th>
                                        <th>Fecha Pago</th>
                                        <th>Comprobante</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($expensas as $exp)
                                        <tr>
                                            <td class="fw-bold text-dark">{{ $exp->periodo }}</td>
                                            <td>$ {{ number_format($exp->monto, 2) }}</td>
                                            <td>{!! $exp->estado_badge !!}</td>
                                            <td class="small text-muted">{{ $exp->fecha_pago ? $exp->fecha_pago->format('d/m/Y') : '-' }}</td>
                                            <td>
                                                @if($exp->comprobante_url)
                                                    <a href="{{ $exp->comprobante_url }}" target="_blank" class="btn btn-sm btn-outline-info py-0 px-2">
                                                        <i class="bi bi-file-earmark-check"></i> Ver
                                                    </a>
                                                @else
                                                    <span class="text-muted small">-</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center py-4 text-muted">
                                                No se registran expensas para la unidad de este residente.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Reservas Tab -->
                    <div class="tab-pane fade" id="reservas-pane" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Área Común</th>
                                        <th>Fecha</th>
                                        <th>Horario</th>
                                        <th>Estado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($residente->reservas as $reserva)
                                        <tr>
                                            <td class="fw-semibold text-dark">{{ $reserva->areaComun->nombre }}</td>
                                            <td>{{ $reserva->fecha_reserva->format('d/m/Y') }}</td>
                                            <td><span class="badge bg-light text-dark border">{{ $reserva->horario_formateado }}</span></td>
                                            <td>{!! $reserva->estado_badge !!}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center py-4 text-muted">
                                                El residente no ha realizado reservas de áreas comunes.
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
    </div>
</div>
@endsection
