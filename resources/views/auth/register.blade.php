@extends('layouts.guest')

@section('content')
<div class="auth-header">
    <div class="d-inline-flex align-items-center justify-content-center bg-white bg-opacity-20 rounded-circle mb-3" style="width: 56px; height: 56px;">
        <i class="bi bi-person-plus-fill fs-3"></i>
    </div>
    <h4 class="fw-bold mb-1">Registro de Residente</h4>
    <p class="mb-0 text-white-50 small">Crea tu cuenta en CondominioApp</p>
</div>

<div class="auth-body">
    <form method="POST" action="{{ route('register.post') }}">
        @csrf

        <div class="mb-3">
            <label for="name" class="form-label fw-semibold text-secondary small">Nombre Completo</label>
            <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror" 
                   value="{{ old('name') }}" placeholder="Ej: Carlos Mendoza" required autofocus>
            @error('name')
                <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="ci" class="form-label fw-semibold text-secondary small">Carnet de Identidad (CI)</label>
                <input type="text" id="ci" name="ci" class="form-control @error('ci') is-invalid @enderror" 
                       value="{{ old('ci') }}" placeholder="Ej: 8492019" required>
                @error('ci')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-6 mb-3">
                <label for="telefono" class="form-label fw-semibold text-secondary small">Teléfono / Celular</label>
                <input type="text" id="telefono" name="telefono" class="form-control @error('telefono') is-invalid @enderror" 
                       value="{{ old('telefono') }}" placeholder="Ej: 71234567">
                @error('telefono')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="mb-3">
            <label for="email" class="form-label fw-semibold text-secondary small">Correo Electrónico</label>
            <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror" 
                   value="{{ old('email') }}" placeholder="carlos@ejemplo.com" required>
            @error('email')
                <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="departamento_id" class="form-label fw-semibold text-secondary small">Unidad Habitacional (Departamento)</label>
            <select name="departamento_id" id="departamento_id" class="form-select @error('departamento_id') is-invalid @enderror">
                <option value="">-- Seleccionar mi departamento (opcional) --</option>
                @foreach($departamentos as $depto)
                    <option value="{{ $depto->id }}" {{ old('departamento_id') == $depto->id ? 'selected' : '' }}>
                        {{ $depto->identificador_completo }}
                    </option>
                @endforeach
            </select>
            @error('departamento_id')
                <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="password" class="form-label fw-semibold text-secondary small">Contraseña</label>
                <input type="password" id="password" name="password" class="form-control @error('password') is-invalid @enderror" 
                       placeholder="Mínimo 8 caracteres" required>
                @error('password')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-6 mb-3">
                <label for="password_confirmation" class="form-label fw-semibold text-secondary small">Confirmar Contraseña</label>
                <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" 
                       placeholder="Repetir contraseña" required>
            </div>
        </div>

        <button type="submit" class="btn btn-primary w-100 mb-3 shadow-sm py-2">
            <i class="bi bi-check-lg me-2"></i>Completar Registro
        </button>

        <div class="text-center">
            <a href="{{ route('login') }}" class="small text-muted text-decoration-none">
                ¿Ya tienes una cuenta? <span class="text-primary fw-semibold">Inicia Sesión</span>
            </a>
        </div>
    </form>
</div>
@endsection
