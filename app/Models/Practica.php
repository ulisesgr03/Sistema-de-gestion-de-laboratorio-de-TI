<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Practica extends Model
{
    protected $fillable = [
        'laboratorio_id',
        'user_id',
        'nombre',
        'descripcion',
        'fecha_inicio',
        'fecha_fin',
        'estado',
    ];

    public function laboratorio(): BelongsTo
    {
        return $this->belongsTo(Laboratorio::class);
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function reservas(): HasMany
    {
        return $this->hasMany(ReservaLaboratorio::class);
    }
}
