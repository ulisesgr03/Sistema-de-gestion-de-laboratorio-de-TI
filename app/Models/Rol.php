<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Rol extends Model
{
    public const ROLE_ADMIN = 'administrador';
    public const ROLE_ENCARGADO = 'encargado';
    public const ROLE_DOCENTE = 'docente';
    public const ROLE_ALUMNO = 'alumno';

    protected $table = 'roles';

    protected $fillable = [
        'nombre',
        'descripcion',
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            'role_user',
            'role_id',
            'user_id'
        )->withTimestamps();
    }
}
