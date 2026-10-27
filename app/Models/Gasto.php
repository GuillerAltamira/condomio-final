<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Gasto extends Model
{
    use HasFactory;

    protected $table = 'gastos';

    protected $fillable = [
        'descripcion',
        'monto',
        'categoria',
        'fecha',
        'comprobante',
        'registrado_por',
    ];

    protected function casts(): array
    {
        return [
            'monto' => 'decimal:2',
            'fecha' => 'date',
        ];
    }

    /**
     * User who registered the expense.
     */
    public function registrador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'registrado_por');
    }

    /**
     * Color-coded badge for expense categories.
     */
    public function getCategoriaBadgeAttribute(): string
    {
        return match (strtolower($this->categoria)) {
            'mantenimiento' => '<span class="badge bg-primary-subtle text-primary border border-primary-subtle"><i class="bi bi-tools me-1"></i>Mantenimiento</span>',
            'servicios' => '<span class="badge bg-info-subtle text-info border border-info-subtle"><i class="bi bi-lightning-charge me-1"></i>Servicios Básicos</span>',
            'reparaciones' => '<span class="badge bg-warning-subtle text-warning border border-warning-subtle"><i class="bi bi-wrench me-1"></i>Reparaciones</span>',
            'seguridad' => '<span class="badge bg-danger-subtle text-danger border border-danger-subtle"><i class="bi bi-shield-lock me-1"></i>Seguridad</span>',
            'limpieza' => '<span class="badge bg-success-subtle text-success border border-success-subtle"><i class="bi bi-stars me-1"></i>Limpieza</span>',
            default => '<span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle"><i class="bi bi-tag me-1"></i>' . ucfirst($this->categoria) . '</span>',
        };
    }

    /**
     * Storage URL for expense receipt if available.
     */
    public function getComprobanteUrlAttribute(): ?string
    {
        if ($this->comprobante && file_exists(public_path('storage/' . $this->comprobante))) {
            return asset('storage/' . $this->comprobante);
        }
        return null;
    }
}
