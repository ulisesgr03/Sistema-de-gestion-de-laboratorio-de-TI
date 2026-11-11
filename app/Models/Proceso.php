<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Proceso extends Model
{
    protected $table = 'procesos';

    protected $guarded = [];

    public function setTituloAttribute($value): void
    {
        if (empty($this->attributes['tipo'])) {
            $this->attributes['tipo'] = (string) $value;
        } else {
            $this->attributes['tipo'] = (string) $value;
        }
    }

    public function setFolioAttribute($value): void
    {
        // El folio se calcula dinámicamente o se preserva en memoria
    }

    public function setFechaSolicitudAttribute($value): void
    {
        // Se preserva en created_at
    }

    public function getFolioAttribute(): string
    {
        return 'MN-SOL-' . sprintf('%04d', $this->id ?? 1);
    }

    public function getTituloAttribute(): string
    {
        return $this->tipo ?? 'Solicitud Técnica';
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function laboratorio(): BelongsTo
    {
        return $this->belongsTo(Laboratorio::class);
    }
}
