<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Departamento extends Model
{
    use HasFactory;

    protected $table = 'departamentos';

    protected $fillable = [
        'numero_departamento',
        'piso',
        'bloque',
        'propietario_id',
    ];

    protected function casts(): array
    {
        return [
            'piso' => 'integer',
        ];
    }

    /**
     * Owner of the apartment.
     */
    public function propietario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'propietario_id');
    }

    /**
     * Residents living in this apartment.
     */
    public function residentes(): HasMany
    {
        return $this->hasMany(User::class, 'departamento_id');
    }

    /**
     * Expensas issued to this apartment.
     */
    public function expensas(): HasMany
    {
        return $this->hasMany(Expensa::class, 'departamento_id');
    }

    /**
     * Full label representation of the unit.
     */
    public function getIdentificadorCompletoAttribute(): string
    {
        $bloqueStr = $this->bloque ? "Bloque {$this->bloque} - " : "";
        return "{$bloqueStr}Piso {$this->piso} - Unidad {$this->numero_departamento}";
    }

    /**
     * Has pending or overdue payments?
     */
    public function tieneDeuda(): bool
    {
        return $this->expensas()->whereIn('estado', ['pendiente', 'atrasado'])->exists();
    }
}
