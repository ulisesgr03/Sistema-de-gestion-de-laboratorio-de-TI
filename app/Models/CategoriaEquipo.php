<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CategoriaEquipo extends Model
{
    protected $table = 'categoria_equipos';

    protected $fillable = [
        'nombre',
        'descripcion',
    ];

    public function equipos(): HasMany
    {
        return $this->hasMany(Equipo::class);
    }
}
