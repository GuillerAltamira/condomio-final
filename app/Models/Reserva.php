<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reserva extends Model
{
    use HasFactory;

    protected $table = 'reservas';

    protected $fillable = [
        'area_comun_id',
        'residente_id',
        'fecha_reserva',
        'hora_inicio',
        'hora_fin',
        'estado',
        'motivo',
    ];

    protected function casts(): array
    {
        return [
            'fecha_reserva' => 'date',
        ];
    }

    /**
     * Reserved common area.
     */
    public function areaComun(): BelongsTo
    {
        return $this->belongsTo(AreaComun::class, 'area_comun_id');
    }

    /**
     * Resident who made the reservation.
     */
    public function residente(): BelongsTo
    {
        return $this->belongsTo(User::class, 'residente_id');
    }

    /**
     * Formatted time range e.g. "14:00 - 18:00".
     */
    public function getHorarioFormateadoAttribute(): string
    {
        $inicio = substr($this->hora_inicio, 0, 5);
        $fin = substr($this->hora_fin, 0, 5);
        return "{$inicio} a {$fin}";
    }

    /**
     * Status badge for reservations.
     */
    public function getEstadoBadgeAttribute(): string
    {
        return match ($this->estado) {
            'confirmada' => '<span class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1.5"><i class="bi bi-calendar-check me-1"></i>Confirmada</span>',
            'cancelada' => '<span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2.5 py-1.5"><i class="bi bi-calendar-x me-1"></i>Cancelada</span>',
            'pendiente' => '<span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2.5 py-1.5"><i class="bi bi-hourglass me-1"></i>Pendiente</span>',
            default => '<span class="badge bg-secondary-subtle text-secondary px-2.5 py-1.5">' . ucfirst($this->estado) . '</span>',
        };
    }

    /**
     * Check if a proposed time slot conflicts with an existing reservation.
     */
    public static function existeSolapamiento($areaComunId, $fecha, $horaInicio, $horaFin, $ignorarId = null): bool
    {
        $query = self::where('area_comun_id', $areaComunId)
            ->whereDate('fecha_reserva', $fecha)
            ->where('estado', '!=', 'cancelada')
            ->where(function (Builder $q) use ($horaInicio, $horaFin) {
                // An overlap occurs whenever (startA < endB) and (endA > startB)
                $q->where('hora_inicio', '<', $horaFin)
                  ->where('hora_fin', '>', $horaInicio);
            });

        if ($ignorarId) {
            $query->where('id', '!=', $ignorarId);
        }

        return $query->exists();
    }
}
