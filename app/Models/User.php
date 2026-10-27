<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'ci',
        'telefono',
        'rol',
        'activo',
        'avatar',
        'departamento_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'activo' => 'boolean',
        ];
    }

    /**
     * Departamento where the resident lives.
     */
    public function departamento(): BelongsTo
    {
        return $this->belongsTo(Departamento::class, 'departamento_id');
    }

    /**
     * Departamentos owned by this user (if propietario).
     */
    public function departamentosPropiedad(): HasMany
    {
        return $this->hasMany(Departamento::class, 'propietario_id');
    }

    /**
     * Reservations made by this resident.
     */
    public function reservas(): HasMany
    {
        return $this->hasMany(Reserva::class, 'residente_id');
    }

    /**
     * Gastos registered by this user.
     */
    public function gastosRegistrados(): HasMany
    {
        return $this->hasMany(Gasto::class, 'registrado_por');
    }

    /**
     * Check if user has administrator privileges.
     */
    public function isAdmin(): bool
    {
        return $this->rol === 'admin';
    }

    /**
     * Check if user is resident / propietario.
     */
    public function isResidente(): bool
    {
        return $this->rol === 'residente';
    }

    /**
     * Avatar image helper.
     */
    public function getAvatarUrlAttribute(): string
    {
        if ($this->avatar && file_exists(public_path('storage/' . $this->avatar))) {
            return asset('storage/' . $this->avatar);
        }

        // Clean SVG placeholder based on initials
        $initials = strtoupper(substr($this->name, 0, 2));
        return "https://ui-avatars.com/api/?name=" . urlencode($this->name) . "&background=0D8ABC&color=fff&size=128";
    }

    /**
     * Badge visual representation for role.
     */
    public function getRolBadgeAttribute(): string
    {
        return $this->isAdmin()
            ? '<span class="badge bg-primary-subtle text-primary border border-primary-subtle"><i class="bi bi-shield-check me-1"></i>Administrador</span>'
            : '<span class="badge bg-success-subtle text-success border border-success-subtle"><i class="bi bi-person me-1"></i>Residente</span>';
    }
}
