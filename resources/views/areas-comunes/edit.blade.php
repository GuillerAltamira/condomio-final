@extends('layouts.app')

@section('title', 'Editar Área Común')
@section('page-title', 'Modificar Espacio Común')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-pencil-square text-primary me-2"></i>Editar Área: {{ $areaComun->nombre }}</h5>
                <a href="{{ route('areas-comunes.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left me-1"></i>Volver
                </a>
            </div>
            <div class="card-body p-4">
                <form method="POST" action="{{ route('areas-comunes.update', $areaComun) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="row mb-3">
                        <div class="col-md-8">
                            <label for="nombre" class="form-label fw-semibold small text-secondary">Nombre del Espacio <span class="text-danger">*</span></label>
                            <input type="text" name="nombre" id="nombre" class="form-control @error('nombre') is-invalid @enderror" value="{{ old('nombre', $areaComun->nombre) }}" required>
                            @error('nombre')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label for="capacidad" class="form-label fw-semibold small text-secondary">Capacidad Máxima <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" name="capacidad" id="capacidad" class="form-control @error('capacidad') is-invalid @enderror" value="{{ old('capacidad', $areaComun->capacidad) }}" min="1" max="500" required>
                                <span class="input-group-text bg-light text-muted small">pers.</span>
                            </div>
                            @error('capacidad')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="costo_reserva" class="form-label fw-semibold small text-secondary">Costo por Reserva (Bs.) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">Bs.</span>
                                <input type="number" step="0.01" name="costo_reserva" id="costo_reserva" class="form-control @error('costo_reserva') is-invalid @enderror" value="{{ old('costo_reserva', $areaComun->costo_reserva) }}" min="0" required>
                            </div>
                            @error('costo_reserva')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="imagen" class="form-label fw-semibold small text-secondary">Actualizar Fotografía</label>
                            <input type="file" name="imagen" id="imagen" class="form-control @error('imagen') is-invalid @enderror" accept="image/*">
                            <small class="text-muted">Dejar vacío para mantener la imagen actual.</small>
                            @error('imagen')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    @if($areaComun->imagen)
                        <div class="mb-3 p-2 bg-light rounded-3 border d-inline-block">
                            <span class="d-block small text-muted mb-1">Imagen actual:</span>
                            <img src="{{ $areaComun->imagen_url }}" alt="Vista previa" class="rounded" style="height: 90px; width: 140px; object-fit: cover;">
                        </div>
                    @endif

                    <div class="mb-3">
                        <label for="descripcion" class="form-label fw-semibold small text-secondary">Descripción General</label>
                        <textarea name="descripcion" id="descripcion" rows="3" class="form-control">{{ old('descripcion', $areaComun->descripcion) }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label for="normas" class="form-label fw-semibold small text-secondary">Reglamento y Normas de Uso</label>
                        <textarea name="normas" id="normas" rows="3" class="form-control">{{ old('normas', $areaComun->normas) }}</textarea>
                    </div>

                    <div class="form-check p-3 bg-light rounded-3 border mb-4">
                        <input class="form-check-input ms-0 me-2" type="checkbox" name="activo" id="activo" value="1" {{ old('activo', $areaComun->activo ? '1' : '0') == '1' ? 'checked' : '' }}>
                        <label class="form-check-label fw-semibold text-dark small" for="activo">
                            Espacio Habilitado para Reservas
                        </label>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('areas-comunes.index') }}" class="btn btn-light border">Cancelar</a>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-save me-1"></i>Guardar Cambios
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
