<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MovimientoEquipo extends Model
{
    protected $table = 'movimiento_equipos';

    protected $fillable = [
        'equipo_id',
        'laboratorio_origen_id',
        'laboratorio_destino_id',
        'realizado_por',
        'fecha_movimiento',
        'motivo',
    ];

    protected function casts(): array
    {
        return [
            'fecha_movimiento' => 'datetime',
        ];
    }

    public function equipo(): BelongsTo
    {
        return $this->belongsTo(Equipo::class);
    }

    public function laboratorioOrigen(): BelongsTo
    {
        return $this->belongsTo(
            Laboratorio::class,
            'laboratorio_origen_id'
        );
    }

    public function laboratorioDestino(): BelongsTo
    {
        return $this->belongsTo(
            Laboratorio::class,
            'laboratorio_destino_id'
        );
    }

    public function responsable(): BelongsTo
    {
        return $this->belongsTo(User::class, 'realizado_por');
    }
}
