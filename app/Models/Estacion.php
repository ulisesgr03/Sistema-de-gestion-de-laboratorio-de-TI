<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Estacion extends Model
{
    protected $table = 'estaciones';

    protected $guarded = [];

    public function setCodigoAttribute($value): void
    {
        $this->attributes['nombre'] = $value;
    }

    public function setFilaAttribute($value): void {}
    public function setColumnaAttribute($value): void {}
    public function setEspecificacionesAttribute($value): void {}

    public function getCodigoAttribute(): string
    {
        return $this->attributes['nombre'] ?? 'PC-01';
    }

    public function laboratorio(): BelongsTo
    {
        return $this->belongsTo(Laboratorio::class);
    }

    public function equipos(): HasMany
    {
        return $this->hasMany(Equipo::class);
    }
}
