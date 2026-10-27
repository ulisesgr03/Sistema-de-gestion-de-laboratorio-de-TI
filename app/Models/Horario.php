<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Horario extends Model
{
    protected $fillable = [
        'laboratorio_id',
        'dia_semana',
        'hora_inicio',
        'hora_fin',
        'activo',
    ];

    public function laboratorio(): BelongsTo
    {
        return $this->belongsTo(Laboratorio::class);
    }
}
