<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Incidencia extends Model
{
    protected $table = 'incidencias';

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'fecha_resolucion' => 'datetime',
        ];
    }

    public function setResueltoEnAttribute($value): void
    {
        $this->attributes['fecha_resolucion'] = $value;
    }

    public function setAtendidoPorAttribute($value): void
    {
        $this->attributes['asignado_a'] = $value;
    }

    public function setSeveridadAttribute($value): void
    {
        $this->attributes['prioridad'] = $value;
    }

    public function setReportadoEnAttribute($value): void
    {
        // mapped to created_at if needed
    }

    public function setEstacionIdAttribute($value): void
    {
        if ($value && empty($this->attributes['equipo_id'])) {
            $eq = Equipo::where('estacion_id', $value)->first();
            if ($eq) {
                $this->attributes['equipo_id'] = $eq->id;
            }
        }
    }

    public function getSeveridadAttribute(): string
    {
        return $this->prioridad ?? 'alta';
    }

    public function equipo(): BelongsTo
    {
        return $this->belongsTo(Equipo::class);
    }

    public function estacion()
    {
        return $this->hasOneThrough(Estacion::class, Equipo::class, 'id', 'id', 'equipo_id', 'estacion_id');
    }

    public function laboratorio(): BelongsTo
    {
        return $this->belongsTo(Laboratorio::class);
    }

    public function reportante(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reportado_por');
    }

    public function encargado(): BelongsTo
    {
        return $this->belongsTo(User::class, 'asignado_a');
    }
}
