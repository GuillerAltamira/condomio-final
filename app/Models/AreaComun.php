<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AreaComun extends Model
{
    use HasFactory;

    protected $table = 'areas_comunes';

    protected $fillable = [
        'nombre',
        'descripcion',
        'imagen',
        'capacidad',
        'costo_reserva',
        'normas',
        'activo',
    ];

    protected function casts(): array
    {
        return [
            'capacidad' => 'integer',
            'costo_reserva' => 'decimal:2',
            'activo' => 'boolean',
        ];
    }

    /**
     * Bookings made for this common area.
     */
    public function reservas(): HasMany
    {
        return $this->hasMany(Reserva::class, 'area_comun_id');
    }

    /**
     * Confirmed reservations.
     */
    public function reservasConfirmadas(): HasMany
    {
        return $this->hasMany(Reserva::class, 'area_comun_id')->where('estado', 'confirmada');
    }

    
  public function getImagenUrlAttribute(): string
    {
        if ($this->imagen) {
            // Si ya es un enlace web externo completo, déjalo tal cual
            if (filter_var($this->imagen, FILTER_VALIDATE_URL)) {
                return $this->imagen;
            }

            // Limpiamos cualquier prefijo duplicado o incorrecto guardado antes
            $path = str_replace('public/', '', $this->imagen);
            $path = str_replace('storage/', '', $path);
            $path = ltrim($path, '/');

            // Apuntamos a 'public/storage/' porque tus fotos están físicamente en htdocs/public/storage/
            return asset('public/storage/' . $path);
        }

        // Imagen por defecto si no tiene ninguna
        return "https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?auto=format&fit=crop&w=800&q=80";
    }

    /**
     * Formatted cost label.
     */
    public function getCostoFormateadoAttribute(): string
    {
        return $this->costo_reserva > 0
            ? 'Bs. ' . number_format($this->costo_reserva, 2)
            : 'Sin costo adicional';
    }
}
