<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AsignacionEquipo extends Model
{
    protected $fillable = [
        'equipo_id',
        'user_id',
        'laboratorio_id',
        'asignado_por',
        'fecha_asignacion',
        'fecha_devolucion',
        'estado',
        'observaciones',
    ];

    protected function casts(): array
    {
        return [
            'fecha_asignacion' => 'datetime',
            'fecha_devolucion' => 'datetime',
        ];
    }

    public function equipo(): BelongsTo
    {
        return $this->belongsTo(Equipo::class);
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function laboratorio(): BelongsTo
    {
        return $this->belongsTo(Laboratorio::class);
    }

    public function encargado(): BelongsTo
    {
        return $this->belongsTo(User::class, 'asignado_por');
    }
}
