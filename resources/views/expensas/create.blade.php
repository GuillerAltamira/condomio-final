@extends('layouts.app')

@section('title', 'Nueva Expensa')
@section('page-title', 'Emitir Expensa Individual')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-file-earmark-plus text-primary me-2"></i>Emitir Cuota de Expensa Individual</h5>
                <a href="{{ route('expensas.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left me-1"></i>Volver
                </a>
            </div>
            <div class="card-body p-4">
                <form method="POST" action="{{ route('expensas.store') }}">
                    @csrf

                    <div class="mb-3">
                        <label for="departamento_id" class="form-label fw-semibold small text-secondary">Departamento Destino <span class="text-danger">*</span></label>
                        <select name="departamento_id" id="departamento_id" class="form-select @error('departamento_id') is-invalid @enderror" required>
                            <option value="">-- Seleccionar Departamento --</option>
                            @foreach($departamentos as $depto)
                                <option value="{{ $depto->id }}" {{ old('departamento_id') == $depto->id ? 'selected' : '' }}>
                                    {{ $depto->identificador_completo }} (Propietario: {{ $depto->propietario ? $depto->propietario->name : 'Sin asignar' }})
                                </option>
                            @endforeach
                        </select>
                        @error('departamento_id')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="mes" class="form-label fw-semibold small text-secondary">Mes <span class="text-danger">*</span></label>
                            @php
                                $mesesEnEspanol = [
                                    1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
                                    5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
                                    9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
                                ];
                            @endphp
                            <select name="mes" id="mes" class="form-select @error('mes') is-invalid @enderror" required>
                                @for($m = 1; $m <= 12; $m++)
                                    <option value="{{ $m }}" {{ old('mes', $mesActual) == $m ? 'selected' : '' }}>
                                        {{ $mesesEnEspanol[$m] }}
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

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="monto" class="form-label fw-semibold small text-secondary">Monto (Bs.) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">Bs.</span>
                                <input type="number" step="0.01" name="monto" id="monto" class="form-control @error('monto') is-invalid @enderror" value="{{ old('monto', '150.00') }}" min="0.01" required>
                            </div>
                            @error('monto')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="estado" class="form-label fw-semibold small text-secondary">Estado Inicial <span class="text-danger">*</span></label>
                            <select name="estado" id="estado" class="form-select @error('estado') is-invalid @enderror" required>
                                <option value="pendiente" {{ old('estado') == 'pendiente' ? 'selected' : '' }}>Pendiente de Pago</option>
                                <option value="pagado" {{ old('estado') == 'pagado' ? 'selected' : '' }}>Pagado</option>
                                <option value="atrasado" {{ old('estado') == 'atrasado' ? 'selected' : '' }}>Atrasado / En Mora</option>
                            </select>
                            @error('estado')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="notas" class="form-label fw-semibold small text-secondary">Concepto / Notas Adicionales</label>
                        <textarea name="notas" id="notas" rows="2" class="form-control @error('notas') is-invalid @enderror" placeholder="Ej: Expensa ordinaria correspondiente al mantenimiento y áreas comunes.">{{ old('notas') }}</textarea>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ route('expensas.index') }}" class="btn btn-light border">Cancelar</a>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-save me-1"></i>Registrar Expensa
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection