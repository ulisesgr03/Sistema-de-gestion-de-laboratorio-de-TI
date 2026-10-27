<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReservaLaboratorio extends Model
{
    protected $table = 'reservas_laboratorio';

    protected $fillable = [
        'laboratorio_id',
        'user_id',
        'practica_id',
        'inicio',
        'fin',
        'estado',
        'motivo',
    ];

    protected function casts(): array
    {
        return [
            'inicio' => 'datetime',
            'fin' => 'datetime',
        ];
    }

    public function laboratorio(): BelongsTo
    {
        return $this->belongsTo(Laboratorio::class);
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function practica(): BelongsTo
    {
        return $this->belongsTo(Practica::class);
    }
}
