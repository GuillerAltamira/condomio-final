@extends('layouts.app')

@section('title', 'Generación Masiva de Expensas')
@section('page-title', 'Emisión Masiva de Cuotas Mensuales')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-magic text-primary me-2"></i>Generación Masiva de Expensas</h5>
                <a href="{{ route('expensas.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left me-1"></i>Volver
                </a>
            </div>
            <div class="card-body p-4">
                <div class="alert alert-primary border-primary-subtle d-flex align-items-center mb-4">
                    <i class="bi bi-info-circle-fill fs-4 me-3 text-primary"></i>
                    <div>
                        <span class="fw-bold d-block">Emisión Automática de Cuotas</span>
                        <small>Esta herramienta generará la expensa en estado <strong>Pendiente</strong> para todos los departamentos. Si una unidad ya tiene la expensa emitida para este período, el sistema la omitirá automáticamente para evitar duplicaciones.</small>
                    </div>
                </div>

                <form method="POST" action="{{ route('expensas.generar-masivo.post') }}">
                    @csrf

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="mes" class="form-label fw-semibold small text-secondary">Mes de Facturación <span class="text-danger">*</span></label>
                            <select name="mes" id="mes" class="form-select @error('mes') is-invalid @enderror" required>
                                @for($m = 1; $m <= 12; $m++)
                                    <option value="{{ $m }}" {{ old('mes', $mesActual) == $m ? 'selected' : '' }}>
                                        {{ DateTime::createFromFormat('!m', $m)->format('F') }}
                                    </option>
                                @endfor
                            </select>
                            @error('mes')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="anio" class="form-label fw-semibold small text-secondary">Año <span class="text-danger">*</span></label>
                            <input type="number" name="anio" id="anio" class="form-control @error('anio') is-invalid @enderror" value="{{ old('anio', $anioActual) }}" min="2020" max="2035" required>
                            @error('anio')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="monto" class="form-label fw-semibold small text-secondary">Monto Base por Departamento ($) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light">$</span>
                            <input type="number" step="0.01" name="monto" id="monto" class="form-control @error('monto') is-invalid @enderror" value="{{ old('monto', '150.00') }}" min="0.01" required>
                        </div>
                        <small class="text-muted">Cuota estándar aplicable a las unidades seleccionadas.</small>
                        @error('monto')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-check p-3 bg-light rounded-3 border mb-4">
                        <input class="form-check-input ms-0 me-2" type="checkbox" name="solo_ocupados" id="solo_ocupados" value="1" {{ old('solo_ocupados', '1') ? 'checked' : '' }}>
                        <label class="form-check-label fw-semibold text-dark small" for="solo_ocupados">
                            Generar solo para departamentos ocupados (con residente o propietario asignado)
                        </label>
                        <small class="text-muted d-block ms-4">Si se desmarca, se emitirán cuotas para todos los {{ $totalDeptos }} departamentos registrados.</small>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('expensas.index') }}" class="btn btn-light border">Cancelar</a>
                        <button type="submit" class="btn btn-primary px-4" onclick="return confirm('¿Confirmas la generación masiva de cuotas para el período seleccionado?');">
                            <i class="bi bi-play-circle me-1"></i>Generar Cuotas
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
