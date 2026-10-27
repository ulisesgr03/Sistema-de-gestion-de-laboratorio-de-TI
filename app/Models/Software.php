<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Software extends Model
{
    protected $table = 'software';

    protected $fillable = [
        'nombre',
        'version',
        'fabricante',
        'descripcion',
    ];

    public function licencias(): HasMany
    {
        return $this->hasMany(Licencia::class);
    }

    public function instalaciones(): HasMany
    {
        return $this->hasMany(InstalacionSoftware::class);
    }
}
