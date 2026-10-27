<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Laboratorio extends Model
{
    protected $fillable = [
        'nombre',
        'ubicacion',
        'descripcion',
        'capacidad',
        'activo',
    ];

    public function horarios(): HasMany
    {
        return $this->hasMany(Horario::class);
    }

    public function practicas(): HasMany
    {
        return $this->hasMany(Practica::class);
    }

    public function estaciones(): HasMany
    {
        return $this->hasMany(Estacion::class);
    }

    public function incidencias(): HasMany
    {
        return $this->hasMany(Incidencia::class);
    }

    public function reservas(): HasMany
    {
        return $this->hasMany(ReservaLaboratorio::class);
    }

    public function usuarios(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            'usuario_laboratorio',
            'laboratorio_id',
            'user_id'
        )->withPivot('tipo_acceso')->withTimestamps();
    }

    public function mantenimientos(): HasMany
    {
        return $this->hasMany(Mantenimiento::class);
    }

    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }
}
