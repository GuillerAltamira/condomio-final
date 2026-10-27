@extends('layouts.app')

@section('title', 'Editar Gasto')
@section('page-title', 'Modificar Gasto de Administración')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-pencil-square text-primary me-2"></i>Editar Registro de Gasto</h5>
                <a href="{{ route('gastos.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left me-1"></i>Volver
                </a>
            </div>
            <div class="card-body p-4">
                <form method="POST" action="{{ route('gastos.update', $gasto) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="descripcion" class="form-label fw-semibold small text-secondary">Descripción o Concepto <span class="text-danger">*</span></label>
                        <input type="text" name="descripcion" id="descripcion" class="form-control @error('descripcion') is-invalid @enderror" value="{{ old('descripcion', $gasto->descripcion) }}" required>
                        @error('descripcion')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="categoria" class="form-label fw-semibold small text-secondary">Categoría <span class="text-danger">*</span></label>
                            <select name="categoria" id="categoria" class="form-select @error('categoria') is-invalid @enderror" required>
                                @foreach($categorias as $key => $label)
                                    <option value="{{ $key }}" {{ old('categoria', $gasto->categoria) == $key ? 'selected' : '' }}>{{ $label }}</option>
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
                                <input type="number" step="0.01" name="monto" id="monto" class="form-control @error('monto') is-invalid @enderror" value="{{ old('monto', $gasto->monto) }}" min="0.01" required>
                            </div>
                            @error('monto')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="fecha" class="form-label fw-semibold small text-secondary">Fecha del Gasto <span class="text-danger">*</span></label>
                            <input type="date" name="fecha" id="fecha" class="form-control @error('fecha') is-invalid @enderror" value="{{ old('fecha', $gasto->fecha->format('Y-m-d')) }}" required>
                            @error('fecha')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="comprobante" class="form-label fw-semibold small text-secondary">Actualizar Factura (Opcional)</label>
                            <input type="file" name="comprobante" id="comprobante" class="form-control @error('comprobante') is-invalid @enderror" accept="image/*,application/pdf">
                            @error('comprobante')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    @if($gasto->comprobante_url)
                        <div class="mb-4 p-2.5 bg-light rounded-3 border d-flex align-items-center justify-content-between">
                            <span class="small text-muted"><i class="bi bi-file-earmark-check text-success me-1"></i>Factura actual registrada</span>
                            <a href="{{ $gasto->comprobante_url }}" target="_blank" class="btn btn-sm btn-outline-info py-0">Ver Documento</a>
                        </div>
                    @endif

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('gastos.index') }}" class="btn btn-light border">Cancelar</a>
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
