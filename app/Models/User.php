<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
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
        ];
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(
            Rol::class,
            'role_user',
            'user_id',
            'role_id'
        )->withTimestamps();
    }

    public function laboratorios(): BelongsToMany
    {
        return $this->belongsToMany(
            Laboratorio::class,
            'usuario_laboratorio',
            'user_id',
            'laboratorio_id'
        )->withPivot('tipo_acceso')->withTimestamps();
    }

    public function practicas(): HasMany
    {
        return $this->hasMany(Practica::class);
    }

    public function asignacionesEquipos(): HasMany
    {
        return $this->hasMany(AsignacionEquipo::class);
    }

    public function equiposAsignados(): HasMany
    {
        return $this->hasMany(
            AsignacionEquipo::class,
            'asignado_por'
        );
    }

    public function procesos(): HasMany
    {
        return $this->hasMany(Proceso::class);
    }

    public function incidenciasReportadas(): HasMany
    {
        return $this->hasMany(Incidencia::class, 'reportado_por');
    }

    public function incidenciasAsignadas(): HasMany
    {
        return $this->hasMany(Incidencia::class, 'asignado_a');
    }

    public function movimientosEquipos(): HasMany
    {
        return $this->hasMany(MovimientoEquipo::class, 'realizado_por');
    }

    public function movimientos(): HasMany
    {
        return $this->hasMany(Movimiento::class);
    }

    public function bitacoras(): HasMany
    {
        return $this->hasMany(Bitacora::class);
    }

    public function instalacionesSoftware(): HasMany
    {
        return $this->hasMany(
            InstalacionSoftware::class,
            'instalado_por'
        );
    }

    public function reservas(): HasMany
    {
        return $this->hasMany(ReservaLaboratorio::class);
    }
}
