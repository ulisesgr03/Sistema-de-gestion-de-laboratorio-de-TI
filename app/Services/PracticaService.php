<?php

namespace App\Services;

use App\Exceptions\BusinessRuleException;
use App\Models\Laboratorio;
use App\Models\Practica;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class PracticaService
{
    public function __construct(
        protected BitacoraService $bitacoraService
    ) {}

    /**
     * Crear una nueva práctica académica.
     */
    public function crear(array $datos, User $docente): Practica
    {
        $laboratorioId = (int) $datos['laboratorio_id'];
        $laboratorio = Laboratorio::findOrFail($laboratorioId);

        if (!$laboratorio->activo) {
            throw new BusinessRuleException(
                "El laboratorio '{$laboratorio->nombre}' se encuentra inactivo.",
                'LABORATORIO_INACTIVO'
            );
        }

        $practica = Practica::create([
            'laboratorio_id' => $laboratorioId,
            'user_id' => $docente->id,
            'nombre' => trim($datos['nombre']),
            'descripcion' => $datos['descripcion'] ?? null,
            'fecha_inicio' => isset($datos['fecha_inicio']) ? Carbon::parse($datos['fecha_inicio']) : null,
            'fecha_fin' => isset($datos['fecha_fin']) ? Carbon::parse($datos['fecha_fin']) : null,
            'estado' => $datos['estado'] ?? 'programada',
        ]);

        $this->bitacoraService->registrar(
            accion: 'Crear Práctica Académica',
            modulo: 'Prácticas Académicas',
            detalle: "Práctica '{$practica->nombre}' creada por docente {$docente->name} para laboratorio '{$laboratorio->nombre}'",
            userId: $docente->id
        );

        return $practica;
    }

    /**
     * Actualizar los datos de una práctica académica.
     */
    public function actualizar(Practica $practica, array $datos, ?int $userId = null): Practica
    {
        $practica->update($datos);

        $this->bitacoraService->registrar(
            accion: 'Actualizar Práctica Académica',
            modulo: 'Prácticas Académicas',
            detalle: "Práctica ID {$practica->id} actualizada.",
            userId: $userId
        );

        return $practica;
    }

    /**
     * Marcar una práctica como completada.
     */
    public function completar(Practica $practica, ?int $userId = null): Practica
    {
        $practica->estado = 'completada';
        $practica->save();

        $this->bitacoraService->registrar(
            accion: 'Completar Práctica Académica',
            modulo: 'Prácticas Académicas',
            detalle: "Práctica ID {$practica->id} ('{$practica->nombre}') completada.",
            userId: $userId
        );

        return $practica;
    }

    /**
     * Listar prácticas académicas con filtros y paginación.
     */
    public function listar(array $filtros = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Practica::with(['laboratorio', 'usuario', 'reservas'])
            ->latest('created_at');

        if (!empty($filtros['estado'])) {
            $query->where('estado', $filtros['estado']);
        }

        if (!empty($filtros['laboratorio_id'])) {
            $query->where('laboratorio_id', $filtros['laboratorio_id']);
        }

        if (!empty($filtros['user_id'])) {
            $query->where('user_id', $filtros['user_id']);
        }

        if (!empty($filtros['buscar'])) {
            $buscar = $filtros['buscar'];
            $query->where(function ($q) use ($buscar) {
                $q->where('nombre', 'like', "%{$buscar}%")
                  ->orWhere('descripcion', 'like', "%{$buscar}%");
            });
        }

        return $query->paginate($perPage);
    }
}
