@extends('layouts.app')

@section('title', 'Mi Perfil')
@section('page-title', 'Mi Perfil de Usuario')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-bottom py-3">
                <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-person-gear me-2 text-primary"></i>Configuración de Cuenta</h5>
            </div>
            <div class="card-body p-4">
                <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                    @csrf

                    <div class="d-flex align-items-center gap-4 mb-4 pb-3 border-bottom">
                        <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="rounded-circle shadow-sm" style="width: 80px; height: 80px; object-fit: cover;">
                        <div>
                            <label for="avatar" class="form-label fw-semibold mb-1">Cambiar Foto de Perfil</label>
                            <input type="file" name="avatar" id="avatar" class="form-control form-control-sm @error('avatar') is-invalid @enderror" accept="image/*">
                            <small class="text-muted d-block mt-1">Formatos: JPG, PNG, WEBP. Máx: 2MB.</small>
                            @error('avatar')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="name" class="form-label fw-semibold small text-secondary">Nombre Completo</label>
                            <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required>
                            @error('name')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="email" class="form-label fw-semibold small text-secondary">Correo Electrónico</label>
                            <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required>
                            @error('email')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="ci" class="form-label fw-semibold small text-secondary">Carnet de Identidad (CI)</label>
                            <input type="text" name="ci" id="ci" class="form-control @error('ci') is-invalid @enderror" value="{{ old('ci', $user->ci) }}" required>
                            @error('ci')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="telefono" class="form-label fw-semibold small text-secondary">Teléfono de Contacto</label>
                            <input type="text" name="telefono" id="telefono" class="form-control @error('telefono') is-invalid @enderror" value="{{ old('telefono', $user->telefono) }}">
                            @error('telefono')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    @if($user->isResidente() && $user->departamento)
                        <div class="alert alert-light border d-flex align-items-center mb-4">
                            <i class="bi bi-door-open-fill text-primary fs-3 me-3"></i>
                            <div>
                                <span class="d-block fw-bold text-dark">Departamento Asignado</span>
                                <span class="text-muted">{{ $user->departamento->identificador_completo }}</span>
                            </div>
                        </div>
                    @endif

                    <hr class="my-4">
                    <h6 class="fw-bold mb-3 text-secondary"><i class="bi bi-key me-2"></i>Actualizar Contraseña (Opcional)</h6>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label for="password" class="form-label fw-semibold small text-secondary">Nueva Contraseña</label>
                            <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" placeholder="Dejar en blanco para no cambiar">
                            @error('password')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="password_confirmation" class="form-label fw-semibold small text-secondary">Confirmar Nueva Contraseña</label>
                            <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" placeholder="Repite la contraseña">
                        </div>
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-save me-2"></i>Guardar Cambios
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
