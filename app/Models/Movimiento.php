<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Movimiento extends Model
{
    protected $fillable = [
        'user_id',
        'entidad',
        'entidad_id',
        'tipo_movimiento',
        'descripcion',
        'fecha_movimiento',
    ];

    protected function casts(): array
    {
        return [
            'fecha_movimiento' => 'datetime',
        ];
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
