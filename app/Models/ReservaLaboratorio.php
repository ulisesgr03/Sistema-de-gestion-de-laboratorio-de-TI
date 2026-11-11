<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReservaLaboratorio extends Model
{
    protected $table = 'reservas_laboratorio';

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'inicio' => 'datetime',
            'fin' => 'datetime',
        ];
    }

    public function setInicioEnAttribute($value): void
    {
        $this->attributes['inicio'] = $value;
    }

    public function setFinEnAttribute($value): void
    {
        $this->attributes['fin'] = $value;
    }

    public function setDocenteNombreAttribute($value): void {}
    public function setMateriaAttribute($value): void {}
    public function setGrupoAttribute($value): void {}
    public function setModalidadAttribute($value): void {}
    public function setNotasAttribute($value): void {}

    public function getModalidadAttribute(): string
    {
        $m = (string) ($this->attributes['modalidad'] ?? $this->attributes['motivo'] ?? '');
        if (stripos($m, 'EXAMEN') !== false) {
            return 'Examen';
        }
        if (stripos($m, 'CLASE') !== false) {
            return 'Clase';
        }
        return 'Práctica';
    }

    public function getMateriaAttribute(): string
    {
        if (!empty($this->attributes['materia'])) {
            return $this->attributes['materia'];
        }
        if (preg_match('/\]\s*(.*?)\s*·/i', (string) ($this->attributes['motivo'] ?? ''), $matches)) {
            return trim($matches[1]);
        }
        return $this->attributes['motivo'] ?: 'Sesión de Ingeniería';
    }

    public function getGrupoAttribute(): string
    {
        if (!empty($this->attributes['grupo'])) {
            return $this->attributes['grupo'];
        }
        if (preg_match('/Grupo\s+([A-Z0-9\-]+)/i', (string) ($this->attributes['motivo'] ?? ''), $matches)) {
            return $matches[1];
        }
        return 'MN-TI-801';
    }

    public function getInicioEnAttribute()
    {
        return $this->inicio;
    }

    public function getFinEnAttribute()
    {
        return $this->fin;
    }

    public function laboratorio(): BelongsTo
    {
        return $this->belongsTo(Laboratorio::class);
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function practica(): BelongsTo
    {
        return $this->belongsTo(Practica::class);
    }
}
