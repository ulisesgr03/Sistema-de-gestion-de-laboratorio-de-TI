<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Equipo extends Model
{
    protected $fillable = [
        'categoria_equipo_id',
        'estacion_id',
        'codigo_inventario',
        'nombre',
        'marca',
        'modelo',
        'numero_serie',
        'fecha_adquisicion',
        'estado',
        'observaciones',
    ];

    protected function casts(): array
    {
        return [
            'fecha_adquisicion' => 'date',
        ];
    }

    public function categoria(): BelongsTo
    {
        return $this->belongsTo(
            CategoriaEquipo::class,
            'categoria_equipo_id'
        );
    }

    public function estacion(): BelongsTo
    {
        return $this->belongsTo(Estacion::class);
    }

    public function asignaciones(): HasMany
    {
        return $this->hasMany(AsignacionEquipo::class);
    }

    public function incidencias(): HasMany
    {
        return $this->hasMany(Incidencia::class);
    }

    public function movimientos(): HasMany
    {
        return $this->hasMany(MovimientoEquipo::class);
    }

    public function disponibilidades(): HasMany
    {
        return $this->hasMany(Disponibilidad::class);
    }

    public function instalacionesSoftware(): HasMany
    {
        return $this->hasMany(InstalacionSoftware::class);
    }
}
