<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Incidencia extends Model
{
    protected $fillable = [
        'equipo_id',
        'laboratorio_id',
        'reportado_por',
        'asignado_a',
        'titulo',
        'descripcion',
        'prioridad',
        'estado',
        'fecha_resolucion',
    ];

    protected function casts(): array
    {
        return [
            'fecha_resolucion' => 'datetime',
        ];
    }

    public function equipo(): BelongsTo
    {
        return $this->belongsTo(Equipo::class);
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
