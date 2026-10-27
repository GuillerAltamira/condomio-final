@extends('layouts.app')

@section('title', 'Nuevo Gasto')
@section('page-title', 'Registrar Gasto de Administración')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-dash-circle text-danger me-2"></i>Registrar Egreso / Desembolso</h5>
                <a href="{{ route('gastos.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left me-1"></i>Volver
                </a>
            </div>
            <div class="card-body p-4">
                <form method="POST" action="{{ route('gastos.store') }}" enctype="multipart/form-data">
                    @csrf

                    <div class="mb-3">
                        <label for="descripcion" class="form-label fw-semibold small text-secondary">Descripción o Concepto del Gasto <span class="text-danger">*</span></label>
                        <input type="text" name="descripcion" id="descripcion" class="form-control @error('descripcion') is-invalid @enderror" value="{{ old('descripcion') }}" placeholder="Ej: Pago de factura de luz áreas comunes - Empresa Eléctrica" required autofocus>
                        @error('descripcion')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="categoria" class="form-label fw-semibold small text-secondary">Categoría <span class="text-danger">*</span></label>
                            <select name="categoria" id="categoria" class="form-select @error('categoria') is-invalid @enderror" required>
                                <option value="">-- Seleccionar Categoría --</option>
                                @foreach($categorias as $key => $label)
                                    <option value="{{ $key }}" {{ old('categoria') == $key ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('categoria')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="monto" class="form-label fw-semibold small text-secondary">Monto (Bs.) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">Bs.</span>
                                <input type="number" step="0.01" name="monto" id="monto" class="form-control @error('monto') is-invalid @enderror" value="{{ old('monto') }}" min="0.01" placeholder="0.00" required>
                            </div>
                            @error('monto')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label for="fecha" class="form-label fw-semibold small text-secondary">Fecha del Gasto <span class="text-danger">*</span></label>
                            <input type="date" name="fecha" id="fecha" class="form-control @error('fecha') is-invalid @enderror" value="{{ old('fecha', date('Y-m-d')) }}" required>
                            @error('fecha')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="comprobante" class="form-label fw-semibold small text-secondary">Comprobante o Factura (PDF/Imagen)</label>
                            <input type="file" name="comprobante" id="comprobante" class="form-control @error('comprobante') is-invalid @enderror" accept="image/*,application/pdf">
                            <small class="text-muted">JPG, PNG, PDF (Máx. 4MB)</small>
                            @error('comprobante')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('gastos.index') }}" class="btn btn-light border">Cancelar</a>
                        <button type="submit" class="btn btn-danger px-4">
                            <i class="bi bi-save me-1"></i>Guardar Gasto
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
