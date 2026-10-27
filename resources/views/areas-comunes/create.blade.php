@extends('layouts.app')

@section('title', 'Nueva Área Común')
@section('page-title', 'Crear Espacio Común')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-tree text-primary me-2"></i>Registrar Nueva Área Común</h5>
                <a href="{{ route('areas-comunes.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left me-1"></i>Volver
                </a>
            </div>
            <div class="card-body p-4">
                <form method="POST" action="{{ route('areas-comunes.store') }}" enctype="multipart/form-data">
                    @csrf

                    <div class="row mb-3">
                        <div class="col-md-8">
                            <label for="nombre" class="form-label fw-semibold small text-secondary">Nombre del Espacio <span class="text-danger">*</span></label>
                            <input type="text" name="nombre" id="nombre" class="form-control @error('nombre') is-invalid @enderror" value="{{ old('nombre') }}" placeholder="Ej: Quincho Parrillero Principal" required autofocus>
                            @error('nombre')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label for="capacidad" class="form-label fw-semibold small text-secondary">Capacidad Máxima <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" name="capacidad" id="capacidad" class="form-control @error('capacidad') is-invalid @enderror" value="{{ old('capacidad', 20) }}" min="1" max="500" required>
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
                                <input type="number" step="0.01" name="costo_reserva" id="costo_reserva" class="form-control @error('costo_reserva') is-invalid @enderror" value="{{ old('costo_reserva', '0.00') }}" min="0" required>
                            </div>
                            <small class="text-muted">Ingresa 0 para uso gratuito.</small>
                            @error('costo_reserva')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="imagen" class="form-label fw-semibold small text-secondary">Fotografía del Área</label>
                            <input type="file" name="imagen" id="imagen" class="form-control @error('imagen') is-invalid @enderror" accept="image/*">
                            <small class="text-muted">JPG, PNG, WEBP (Máx. 3MB)</small>
                            @error('imagen')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="descripcion" class="form-label fw-semibold small text-secondary">Descripción General</label>
                        <textarea name="descripcion" id="descripcion" rows="3" class="form-control @error('descripcion') is-invalid @enderror" placeholder="Describe los atractivos, comodidades y equipamiento del espacio...">{{ old('descripcion') }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label for="normas" class="form-label fw-semibold small text-secondary">Reglamento y Normas de Uso</label>
                        <textarea name="normas" id="normas" rows="3" class="form-control @error('normas') is-invalid @enderror" placeholder="Ej: Respetar volumen de música tras las 23:00, dejar el área limpia y libre de basura...">{{ old('normas') }}</textarea>
                    </div>

                    <div class="form-check p-3 bg-light rounded-3 border mb-4">
                        <input class="form-check-input ms-0 me-2" type="checkbox" name="activo" id="activo" value="1" {{ old('activo', '1') ? 'checked' : '' }}>
                        <label class="form-check-label fw-semibold text-dark small" for="activo">
                            Espacio Habilitado (Permite reservas de los residentes)
                        </label>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('areas-comunes.index') }}" class="btn btn-light border">Cancelar</a>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-save me-1"></i>Crear Área Común
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
