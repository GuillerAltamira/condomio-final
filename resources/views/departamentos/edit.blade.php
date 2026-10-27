@extends('layouts.app')

@section('title', 'Editar Departamento')
@section('page-title', 'Modificar Unidad Habitacional')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-pencil-square text-primary me-2"></i>Editar Departamento {{ $departamento->identificador_completo }}</h5>
                <a href="{{ route('departamentos.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left me-1"></i>Volver
                </a>
            </div>
            <div class="card-body p-4">
                <form method="POST" action="{{ route('departamentos.update', $departamento) }}">
                    @csrf
                    @method('PUT')

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="numero_departamento" class="form-label fw-semibold small text-secondary">Número de Departamento <span class="text-danger">*</span></label>
                            <input type="text" name="numero_departamento" id="numero_departamento" class="form-control @error('numero_departamento') is-invalid @enderror" value="{{ old('numero_departamento', $departamento->numero_departamento) }}" required>
                            @error('numero_departamento')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="bloque" class="form-label fw-semibold small text-secondary">Bloque / Torre (Opcional)</label>
                            <input type="text" name="bloque" id="bloque" class="form-control @error('bloque') is-invalid @enderror" value="{{ old('bloque', $departamento->bloque) }}">
                            @error('bloque')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="piso" class="form-label fw-semibold small text-secondary">Piso <span class="text-danger">*</span></label>
                            <input type="number" name="piso" id="piso" class="form-control @error('piso') is-invalid @enderror" value="{{ old('piso', $departamento->piso) }}" min="0" max="100" required>
                            @error('piso')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="propietario_id" class="form-label fw-semibold small text-secondary">Propietario Asignado</label>
                            <select name="propietario_id" id="propietario_id" class="form-select @error('propietario_id') is-invalid @enderror">
                                <option value="">-- Sin Propietario (Desocupado) --</option>
                                @foreach($propietarios as $prop)
                                    <option value="{{ $prop->id }}" {{ old('propietario_id', $departamento->propietario_id) == $prop->id ? 'selected' : '' }}>
                                        {{ $prop->name }} (CI: {{ $prop->ci }})
                                    </option>
                                @endforeach
                            </select>
                            @error('propietario_id')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    @if($departamento->residentes->isNotEmpty())
                        <div class="p-3 bg-light rounded-3 mb-3 border">
                            <h6 class="fw-bold text-secondary small mb-2"><i class="bi bi-people me-1"></i>Residentes actualmente viviendo en esta unidad:</h6>
                            <ul class="list-unstyled mb-0 small">
                                @foreach($departamento->residentes as $res)
                                    <li>&bull; {{ $res->name }} (CI: {{ $res->ci }})</li>
                                endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ route('departamentos.index') }}" class="btn btn-light border">Cancelar</a>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-save me-1"></i>Actualizar Departamento
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
