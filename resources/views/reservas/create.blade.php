@extends('layouts.app')

@section('title', 'Nueva Reserva')
@section('page-title', 'Reservar Área Común')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-calendar-plus text-primary me-2"></i>Nueva Reserva de Espacio</h5>
                <a href="{{ route('reservas.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left me-1"></i>Volver
                </a>
            </div>
            <div class="card-body p-4">
                <div class="alert alert-info border-info-subtle d-flex align-items-center mb-4">
                    <i class="bi bi-info-circle-fill fs-4 me-3 text-info"></i>
                    <div>
                        <span class="fw-bold d-block">Control Automático de Solapamiento</span>
                        <small>El sistema valida de forma estricta que no exista ninguna otra reserva activa en el mismo rango de horario para el área seleccionada.</small>
                    </div>
                </div>

                <form method="POST" action="{{ route('reservas.store') }}">
                    @csrf

                    <div class="mb-3">
                        <label for="area_comun_id" class="form-label fw-semibold small text-secondary">Área Común a Reservar <span class="text-danger">*</span></label>
                        <select name="area_comun_id" id="area_comun_id" class="form-select @error('area_comun_id') is-invalid @enderror" required autofocus>
                            <option value="">-- Seleccionar Espacio Común --</option>
                            @foreach($areas as $area)
                                <option value="{{ $area->id }}" {{ (old('area_comun_id', $areaSeleccionadaId) == $area->id) ? 'selected' : '' }}>
                                    {{ $area->nombre }} (Capacidad: {{ $area->capacidad }} pers. &bull; {{ $area->costo_formateado }})
                                </option>
                            @endforeach
                        </select>
                        @error('area_comun_id')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    @if(Auth::user()->isAdmin())
                        <div class="mb-3">
                            <label for="residente_id" class="form-label fw-semibold small text-secondary">Residente Solicitante <span class="text-danger">*</span></label>
                            <select name="residente_id" id="residente_id" class="form-select @error('residente_id') is-invalid @enderror" required>
                                <option value="">-- Seleccionar Residente --</option>
                                @foreach($residentes as $res)
                                    <option value="{{ $res->id }}" {{ old('residente_id') == $res->id ? 'selected' : '' }}>
                                        {{ $res->name }} (CI: {{ $res->ci ?? 'S/N' }} - Depto: {{ $res->departamento ? $res->departamento->identificador_completo : 'Sin depto' }})
                                    </option>
                                @endforeach
                            </select>
                            @error('residente_id')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    @endif

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="fecha_reserva" class="form-label fw-semibold small text-secondary">Fecha de Reserva <span class="text-danger">*</span></label>
                            <input type="date" name="fecha_reserva" id="fecha_reserva" class="form-control @error('fecha_reserva') is-invalid @enderror" value="{{ old('fecha_reserva', date('Y-m-d')) }}" min="{{ date('Y-m-d') }}" required>
                            @error('fecha_reserva')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label for="hora_inicio" class="form-label fw-semibold small text-secondary">Hora de Inicio <span class="text-danger">*</span></label>
                            <input type="time" name="hora_inicio" id="hora_inicio" class="form-control @error('hora_inicio') is-invalid @enderror" value="{{ old('hora_inicio', '14:00') }}" required>
                            @error('hora_inicio')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label for="hora_fin" class="form-label fw-semibold small text-secondary">Hora de Finalización <span class="text-danger">*</span></label>
                            <input type="time" name="hora_fin" id="hora_fin" class="form-control @error('hora_fin') is-invalid @enderror" value="{{ old('hora_fin', '18:00') }}" required>
                            @error('hora_fin')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="motivo" class="form-label fw-semibold small text-secondary">Motivo o Descripción del Evento (Opcional)</label>
                        <textarea name="motivo" id="motivo" rows="2" class="form-control @error('motivo') is-invalid @enderror" placeholder="Ej: Cumpleaños familiar, reunión con amigos...">{{ old('motivo') }}</textarea>
                        @error('motivo')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('reservas.index') }}" class="btn btn-light border">Cancelar</a>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-check-circle me-1"></i>Confirmar Reserva
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
