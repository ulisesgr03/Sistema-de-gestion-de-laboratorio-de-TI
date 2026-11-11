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
        'matricula',
        'password',
        'activo',
        'telefono',
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

    public function mantenimientos(): HasMany
    {
        return $this->hasMany(Mantenimiento::class);
    }

    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    public function hasRole(string|array $roles): bool
    {
        $roleList = is_array($roles) ? $roles : func_get_args();

        $aliases = [
            'admin' => ['admin', 'administrador', 'encargado', 'encargado de laboratorio', 'lab_manager'],
            'administrador' => ['admin', 'administrador', 'encargado', 'encargado de laboratorio', 'lab_manager'],
            'encargado' => ['admin', 'administrador', 'encargado', 'encargado de laboratorio', 'lab_manager'],
            'docente' => ['docente', 'maestro', 'profesor', 'teacher'],
            'maestro' => ['docente', 'maestro', 'profesor', 'teacher'],
            'alumno' => ['alumno', 'estudiante', 'student'],
            'estudiante' => ['alumno', 'estudiante', 'student'],
        ];

        $userRoles = $this->roles->map(function ($r) {
            return strtolower(trim($r->nombre));
        })->toArray();

        foreach ($roleList as $role) {
            foreach (explode(',', (string) $role) as $part) {
                $normalized = strtolower(trim($part));
                $acceptable = $aliases[$normalized] ?? [$normalized];
                foreach ($acceptable as $candidate) {
                    if (in_array($candidate, $userRoles, true)) {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    public function assignRole(string|Rol $role): void
    {
        if (is_string($role)) {
            $normalized = strtolower(trim($role));
            if ($normalized === 'encargado' || $normalized === 'encargado de laboratorio') {
                $normalized = 'admin';
            }
            $map = [
                'admin' => ['admin', 'administrador'],
                'administrador' => ['admin', 'administrador'],
                'docente' => ['docente'],
                'alumno' => ['alumno'],
            ];
            $candidates = $map[$normalized] ?? [$normalized];
            $roleModel = Rol::all()->first(fn (Rol $r) => in_array(strtolower(trim($r->nombre)), $candidates, true));
            if ($roleModel) {
                $this->roles()->syncWithoutDetaching([$roleModel->id]);
            }
        } elseif ($role instanceof Rol) {
            $this->roles()->syncWithoutDetaching([$role->id]);
        }
    }

    public function removeRole(string|Rol $role): void
    {
        if (is_string($role)) {
            $roleModel = Rol::whereRaw('LOWER(nombre) = ?', [strtolower(trim($role))])->first();
            if ($roleModel) {
                $this->roles()->detach($roleModel->id);
            }
        } elseif ($role instanceof Rol) {
            $this->roles()->detach($role->id);
        }
    }

    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    public function isEncargado(): bool
    {
        return $this->isAdmin();
    }

    public function isDocente(): bool
    {
        return $this->hasRole('docente');
    }

    public function isAlumno(): bool
    {
        return $this->hasRole('alumno');
    }

    public function primaryRoleSlug(): string
    {
        if ($this->isAdmin()) {
            return 'admin';
        }
        if ($this->isDocente()) {
            return 'docente';
        }
        return 'alumno';
    }

    public function primaryRoleLabel(): string
    {
        return match ($this->primaryRoleSlug()) {
            'admin' => 'Administrador de Laboratorios',
            'docente' => 'Docente',
            default => 'Alumno',
        };
    }

    public function dashboardRoute(): string
    {
        return match ($this->primaryRoleSlug()) {
            'admin' => route('panel.admin'),
            'docente' => route('panel.docente'),
            default => route('panel.alumno'),
        };
    }

    public function isActive(): bool
    {
        return (bool) $this->activo;
    }
}
