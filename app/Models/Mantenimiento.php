<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Mantenimiento extends Model
{
    protected $table = 'mantenimientos';

    protected $fillable = [
        'equipo_id',
        'laboratorio_id',
        'user_id',
        'tipo',
        'titulo',
        'actividades',
        'resultado',
        'fecha_programada',
        'fecha_inicio',
        'fecha_fin',
        'estado',
        'costo',
    ];

    protected function casts(): array
    {
        return [
            'fecha_programada' => 'datetime',
            'fecha_inicio' => 'datetime',
            'fecha_fin' => 'datetime',
            'costo' => 'decimal:2',
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

    public function responsable(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
