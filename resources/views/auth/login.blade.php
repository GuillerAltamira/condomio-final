@extends('layouts.guest')

@section('content')
<div class="auth-header">
    <div class="d-inline-flex align-items-center justify-content-center bg-white bg-opacity-20 rounded-circle mb-3" style="width: 64px; height: 64px;">
        <i class="bi bi-buildings-fill fs-2"></i>
    </div>
    <h3 class="fw-bold mb-1">CondominioApp</h3>
    <p class="mb-0 text-white-50 small">Sistema Integral de Gestión Residencial</p>
</div>

<div class="auth-body">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show d-flex align-items-center mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2 fs-5"></i>
            <div>{{ session('success') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center mb-4" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
            <div>{{ session('error') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('info'))
        <div class="alert alert-info alert-dismissible fade show d-flex align-items-center mb-4" role="alert">
            <i class="bi bi-info-circle-fill me-2 fs-5"></i>
            <div>{{ session('info') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <form method="POST" action="{{ route('login.post') }}">
        @csrf

        <div class="mb-3">
            <label for="email" class="form-label fw-semibold text-secondary small">Correo Electrónico</label>
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-envelope"></i></span>
                <input type="email" id="email" name="email" class="form-control border-start-0 @error('email') is-invalid @enderror" 
                       value="{{ old('email', 'admin@condominio.com') }}" placeholder="ejemplo@condominio.com" required autofocus>
            </div>
            @error('email')
                <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="password" class="form-label fw-semibold text-secondary small">Contraseña</label>
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-lock"></i></span>
                <input type="password" id="password" name="password" class="form-control border-start-0 @error('password') is-invalid @enderror" 
                       placeholder="••••••••" value="admin123" required>
            </div>
            @error('password')
                <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror
        </div>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="form-check">
                <input type="checkbox" class="form-check-input" id="remember" name="remember" {{ old('remember') ? 'checked' : '' }}>
                <label class="form-check-label small text-muted" for="remember">Recordarme</label>
            </div>
            <a href="{{ route('register') }}" class="small text-primary text-decoration-none fw-semibold">¿Nuevo residente? Regístrate</a>
        </div>

        <button type="submit" class="btn btn-primary w-100 mb-3 shadow-sm">
            <i class="bi bi-box-arrow-in-right me-2"></i>Iniciar Sesión
        </button>
    </form>

    <div class="border-top pt-3 mt-3">
        <p class="text-center text-muted small mb-2 fw-semibold">Acceso Rápido para Demostración:</p>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-outline-primary btn-sm w-50 py-2 rounded-3" onclick="fillCredentials('admin@condominio.com', 'admin123')">
                <i class="bi bi-shield-lock me-1"></i> Admin
            </button>
            <button type="button" class="btn btn-outline-success btn-sm w-50 py-2 rounded-3" onclick="fillCredentials('carlos@condominio.com', 'residente123')">
                <i class="bi bi-person me-1"></i> Residente
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function fillCredentials(email, pass) {
        document.getElementById('email').value = email;
        document.getElementById('password').value = pass;
    }
</script>
@endpush
@endsection
