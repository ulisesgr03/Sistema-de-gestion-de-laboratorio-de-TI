<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UsuarioLaboratorio extends Model
{
    protected $table = 'usuario_laboratorio';

    protected $fillable = [
        'user_id',
        'laboratorio_id',
        'tipo_acceso',
    ];

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function laboratorio(): BelongsTo
    {
        return $this->belongsTo(Laboratorio::class);
    }
}
