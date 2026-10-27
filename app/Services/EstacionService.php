<?php

namespace App\Services;

use App\Exceptions\BusinessRuleException;
use App\Models\Estacion;
use App\Models\Laboratorio;
use Illuminate\Database\Eloquent\Collection;

class EstacionService
{
    public function __construct(
        protected BitacoraService $bitacoraService
    ) {}

    /**
     * Crear una estación de trabajo dentro de un laboratorio.
     */
    public function crear(array $datos): Estacion
    {
        $laboratorio = Laboratorio::findOrFail($datos['laboratorio_id']);

        // Verificar que no se duplique el nombre dentro del mismo laboratorio
        $existe = Estacion::where('laboratorio_id', $laboratorio->id)
            ->where('nombre', trim($datos['nombre']))
            ->exists();

        if ($existe) {
            throw new BusinessRuleException(
                "Ya existe una estación con el nombre '{$datos['nombre']}' en el laboratorio '{$laboratorio->nombre}'.",
                'ESTACION_DUPLICADA'
            );
        }

        $estacion = Estacion::create([
            'laboratorio_id' => $laboratorio->id,
            'nombre' => trim($datos['nombre']),
            'ubicacion' => $datos['ubicacion'] ?? null,
            'estado' => $datos['estado'] ?? 'disponible',
        ]);

        $this->bitacoraService->registrar(
            accion: 'Crear Estación',
            modulo: 'Estaciones',
            detalle: "Estación '{$estacion->nombre}' creada en laboratorio '{$laboratorio->nombre}' (ID: {$estacion->id})"
        );

        return $estacion;
    }

    /**
     * Actualizar una estación de trabajo.
     */
    public function actualizar(Estacion $estacion, array $datos): Estacion
    {
        if (isset($datos['nombre']) && $datos['nombre'] !== $estacion->nombre) {
            $duplicado = Estacion::where('laboratorio_id', $estacion->laboratorio_id)
                ->where('nombre', trim($datos['nombre']))
                ->where('id', '!=', $estacion->id)
                ->exists();

            if ($duplicado) {
                throw new BusinessRuleException(
                    "Ya existe otra estación con el nombre '{$datos['nombre']}' en este laboratorio.",
                    'ESTACION_DUPLICADA'
                );
            }
        }

        $estacion->update($datos);

        $this->bitacoraService->registrar(
            accion: 'Actualizar Estación',
            modulo: 'Estaciones',
            detalle: "Estación ID {$estacion->id} actualizada."
        );

        return $estacion;
    }

    /**
     * Cambiar el estado operativo de una estación.
     */
    public function cambiarEstado(Estacion $estacion, string $nuevoEstado): Estacion
    {
        $estadosValidos = ['disponible', 'ocupada', 'mantenimiento', 'fuera de servicio'];
        if (!in_array($nuevoEstado, $estadosValidos, true)) {
            throw new BusinessRuleException(
                "Estado '{$nuevoEstado}' no válido para la estación.",
                'ESTADO_ESTACION_INVALIDO'
            );
        }

        $estadoAnterior = $estacion->estado;
        $estacion->estado = $nuevoEstado;
        $estacion->save();

        $this->bitacoraService->registrar(
            accion: 'Cambiar Estado Estación',
            modulo: 'Estaciones',
            detalle: "Estación ID {$estacion->id} ({$estacion->nombre}) cambió de '{$estadoAnterior}' a '{$nuevoEstado}'."
        );

        return $estacion;
    }

    /**
     * Listar estaciones de un laboratorio con sus equipos asociados.
     */
    public function listarPorLaboratorio(int $laboratorioId, ?string $estado = null): Collection
    {
        $query = Estacion::where('laboratorio_id', $laboratorioId)
            ->with(['equipos.categoria', 'equipos.instalacionesSoftware.software'])
            ->orderBy('nombre');

        if ($estado) {
            $query->where('estado', $estado);
        }

        return $query->get();
    }
}
