<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Estacion extends Model
{
    protected $fillable = [
        'laboratorio_id',
        'nombre',
        'ubicacion',
        'estado',
    ];

    public function laboratorio(): BelongsTo
    {
        return $this->belongsTo(Laboratorio::class);
    }

    public function equipos(): HasMany
    {
        return $this->hasMany(Equipo::class);
    }
}
