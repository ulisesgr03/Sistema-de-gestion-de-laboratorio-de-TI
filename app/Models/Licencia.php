<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Licencia extends Model
{
    protected $fillable = [
        'software_id',
        'tipo',
        'clave_licencia',
        'cantidad_licencias',
        'fecha_inicio',
        'fecha_vencimiento',
        'estado',
    ];

    protected function casts(): array
    {
        return [
            'fecha_inicio' => 'date',
            'fecha_vencimiento' => 'date',
        ];
    }

    public function software(): BelongsTo
    {
        return $this->belongsTo(Software::class);
    }

    public function instalaciones(): HasMany
    {
        return $this->hasMany(InstalacionSoftware::class);
    }
}
