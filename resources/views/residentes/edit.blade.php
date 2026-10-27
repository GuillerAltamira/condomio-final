@extends('layouts.app')

@section('title', 'Editar Residente')
@section('page-title', 'Modificar Residente')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-pencil-square text-primary me-2"></i>Editar Datos de {{ $residente->name }}</h5>
                <a href="{{ route('residentes.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left me-1"></i>Volver
                </a>
            </div>
            <div class="card-body p-4">
                <form method="POST" action="{{ route('residentes.update', $residente) }}">
                    @csrf
                    @method('PUT')

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="name" class="form-label fw-semibold small text-secondary">Nombre Completo <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $residente->name) }}" required>
                            @error('name')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="email" class="form-label fw-semibold small text-secondary">Correo Electrónico (Único) <span class="text-danger">*</span></label>
                            <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $residente->email) }}" required>
                            @error('email')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="ci" class="form-label fw-semibold small text-secondary">Carnet de Identidad - CI (Único) <span class="text-danger">*</span></label>
                            <input type="text" name="ci" id="ci" class="form-control @error('ci') is-invalid @enderror" value="{{ old('ci', $residente->ci) }}" required>
                            @error('ci')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="telefono" class="form-label fw-semibold small text-secondary">Teléfono / Celular</label>
                            <input type="text" name="telefono" id="telefono" class="form-control @error('telefono') is-invalid @enderror" value="{{ old('telefono', $residente->telefono) }}">
                            @error('telefono')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="departamento_id" class="form-label fw-semibold small text-secondary">Departamento Asignado</label>
                            <select name="departamento_id" id="departamento_id" class="form-select @error('departamento_id') is-invalid @enderror">
                                <option value="">-- Sin Departamento --</option>
                                @foreach($departamentos as $depto)
                                    <option value="{{ $depto->id }}" {{ old('departamento_id', $residente->departamento_id) == $depto->id ? 'selected' : '' }}>
                                        {{ $depto->identificador_completo }}
                                    </option>
                                @endforeach
                            </select>
                            @error('departamento_id')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="activo" class="form-label fw-semibold small text-secondary">Estado de Cuenta <span class="text-danger">*</span></label>
                            <select name="activo" id="activo" class="form-select @error('activo') is-invalid @enderror" required>
                                <option value="1" {{ old('activo', $residente->activo ? '1' : '0') == '1' ? 'selected' : '' }}>Activo (Permite acceso al sistema)</option>
                                <option value="0" {{ old('activo', $residente->activo ? '1' : '0') == '0' ? 'selected' : '' }}>Inactivo (Acceso bloqueado)</option>
                            </select>
                            @error('activo')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label for="password" class="form-label fw-semibold small text-secondary">Restablecer Contraseña (Opcional)</label>
                            <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" placeholder="Dejar vacío si no deseas cambiar la contraseña actual">
                            @error('password')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-check mb-4 p-3 bg-light rounded-3 border">
                        <input class="form-check-input ms-0 me-2" type="checkbox" name="es_propietario" id="es_propietario" value="1">
                        <label class="form-check-label fw-semibold text-dark small" for="es_propietario">
                            Asignar como Propietario del departamento seleccionado
                        </label>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('residentes.index') }}" class="btn btn-light border">Cancelar</a>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-save me-1"></i>Actualizar Residente
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
