<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Expensa extends Model
{
    use HasFactory;

    protected $table = 'expensas';

    protected $fillable = [
        'departamento_id',
        'mes',
        'anio',
        'monto',
        'estado',
        'fecha_pago',
        'metodo_pago',
        'comprobante_imagen',
        'notas',
    ];

    protected function casts(): array
    {
        return [
            'mes' => 'integer',
            'anio' => 'integer',
            'monto' => 'decimal:2',
            'fecha_pago' => 'date',
        ];
    }

    /**
     * Associated apartment.
     */
    public function departamento(): BelongsTo
    {
        return $this->belongsTo(Departamento::class, 'departamento_id');
    }

    /**
     * Human readable month in Spanish.
     */
    public function getNombreMesAttribute(): string
    {
        $meses = [
            1 => 'Enero',
            2 => 'Febrero',
            3 => 'Marzo',
            4 => 'Abril',
            5 => 'Mayo',
            6 => 'Junio',
            7 => 'Julio',
            8 => 'Agosto',
            9 => 'Septiembre',
            10 => 'Octubre',
            11 => 'Noviembre',
            12 => 'Diciembre',
        ];

        return $meses[$this->mes] ?? "Mes {$this->mes}";
    }

    /**
     * Period string: e.g. "Octubre 2026"
     */
    public function getPeriodoAttribute(): string
    {
        return "{$this->nombre_mes} {$this->anio}";
    }

    /**
     * Color-coded badge for financial statuses.
     */
    public function getEstadoBadgeAttribute(): string
    {
        return match ($this->estado) {
            'pagado' => '<span class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1.5"><i class="bi bi-check-circle-fill me-1"></i>Pagado</span>',
            'pendiente' => '<span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2.5 py-1.5"><i class="bi bi-hourglass-split me-1"></i>Pendiente</span>',
            'atrasado' => '<span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2.5 py-1.5"><i class="bi bi-exclamation-triangle-fill me-1"></i>Atrasado</span>',
            default => '<span class="badge bg-secondary-subtle text-secondary px-2.5 py-1.5">' . ucfirst($this->estado) . '</span>',
        };
    }

    /**
     * URL of the payment receipt.
     */
    public function getComprobanteUrlAttribute(): ?string
    {
        if ($this->comprobante_imagen) {
            if (filter_var($this->comprobante_imagen, FILTER_VALIDATE_URL)) {
                return $this->comprobante_imagen;
            }

            // Limpiamos cualquier redundancia de carpetas
            $path = str_replace(['public/', 'storage/'], '', $this->comprobante_imagen);
            $path = ltrim($path, '/');

            // Apuntamos directamente a 'public/storage/' porque tus archivos están en htdocs/public/storage/
            return asset('public/storage/' . $path);
        }
        return null;
    }

    // Scopes
    public function scopePendientes(Builder $query): Builder
    {
        return $query->where('estado', 'pendiente');
    }

    public function scopePagadas(Builder $query): Builder
    {
        return $query->where('estado', 'pagado');
    }

    public function scopeAtrasadas(Builder $query): Builder
    {
        return $query->where('estado', 'atrasado');
    }
}
