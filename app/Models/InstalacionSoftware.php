<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InstalacionSoftware extends Model
{
    protected $table = 'instalacion_software';

    protected $fillable = [
        'equipo_id',
        'software_id',
        'licencia_id',
        'instalado_por',
        'fecha_instalacion',
        'version_instalada',
        'estado',
    ];

    protected function casts(): array
    {
        return [
            'fecha_instalacion' => 'datetime',
        ];
    }

    public function equipo(): BelongsTo
    {
        return $this->belongsTo(Equipo::class);
    }

    public function software(): BelongsTo
    {
        return $this->belongsTo(Software::class);
    }

    public function licencia(): BelongsTo
    {
        return $this->belongsTo(Licencia::class);
    }

    public function responsable(): BelongsTo
    {
        return $this->belongsTo(User::class, 'instalado_por');
    }
}
