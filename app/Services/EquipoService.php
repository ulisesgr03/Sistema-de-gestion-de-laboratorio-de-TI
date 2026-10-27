<?php

namespace App\Services;

use App\Exceptions\BusinessRuleException;
use App\Models\Equipo;
use App\Models\Estacion;
use App\Models\MovimientoEquipo;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class EquipoService
{
    public function __construct(
        protected BitacoraService $bitacoraService
    ) {}

    /**
     * Registrar un nuevo equipo en el inventario.
     */
    public function crear(array $datos, ?int $userId = null): Equipo
    {
        $codigo = trim($datos['codigo_inventario']);

        if (Equipo::where('codigo_inventario', $codigo)->exists()) {
            throw new BusinessRuleException(
                "El código de inventario '{$codigo}' ya está asignado a otro equipo.",
                'EQUIPO_CODIGO_DUPLICADO'
            );
        }

        if (!empty($datos['numero_serie'])) {
            $serie = trim($datos['numero_serie']);
            if (Equipo::where('numero_serie', $serie)->exists()) {
                throw new BusinessRuleException(
                    "El número de serie '{$serie}' ya está registrado en el inventario.",
                    'EQUIPO_SERIE_DUPLICADA'
                );
            }
        }

        $equipo = Equipo::create([
            'categoria_equipo_id' => $datos['categoria_equipo_id'],
            'estacion_id' => $datos['estacion_id'] ?? null,
            'codigo_inventario' => $codigo,
            'nombre' => trim($datos['nombre']),
            'marca' => $datos['marca'] ?? null,
            'modelo' => $datos['modelo'] ?? null,
            'numero_serie' => $datos['numero_serie'] ?? null,
            'fecha_adquisicion' => $datos['fecha_adquisicion'] ?? null,
            'estado' => $datos['estado'] ?? 'disponible',
            'observaciones' => $datos['observaciones'] ?? null,
        ]);

        $this->bitacoraService->registrar(
            accion: 'Registrar Equipo',
            modulo: 'Inventario',
            detalle: "Equipo registrado: {$equipo->nombre} [{$equipo->codigo_inventario}]",
            userId: $userId
        );

        return $equipo;
    }

    /**
     * Actualizar los datos de un equipo.
     */
    public function actualizar(Equipo $equipo, array $datos, ?int $userId = null): Equipo
    {
        if (isset($datos['codigo_inventario']) && $datos['codigo_inventario'] !== $equipo->codigo_inventario) {
            $duplicado = Equipo::where('codigo_inventario', trim($datos['codigo_inventario']))
                ->where('id', '!=', $equipo->id)
                ->exists();

            if ($duplicado) {
                throw new BusinessRuleException(
                    "El código de inventario '{$datos['codigo_inventario']}' ya pertenece a otro equipo.",
                    'EQUIPO_CODIGO_DUPLICADO'
                );
            }
        }

        if (isset($datos['numero_serie']) && $datos['numero_serie'] !== $equipo->numero_serie) {
            $duplicado = Equipo::where('numero_serie', trim($datos['numero_serie']))
                ->where('id', '!=', $equipo->id)
                ->exists();

            if ($duplicado) {
                throw new BusinessRuleException(
                    "El número de serie '{$datos['numero_serie']}' ya pertenece a otro equipo.",
                    'EQUIPO_SERIE_DUPLICADA'
                );
            }
        }

        $equipo->update($datos);

        $this->bitacoraService->registrar(
            accion: 'Actualizar Equipo',
            modulo: 'Inventario',
            detalle: "Equipo ID {$equipo->id} [{$equipo->codigo_inventario}] actualizado.",
            userId: $userId
        );

        return $equipo;
    }

    /**
     * Cambiar el estado del equipo registrando el motivo.
     */
    public function cambiarEstado(Equipo $equipo, string $nuevoEstado, ?string $motivo = null, ?int $userId = null): Equipo
    {
        $estadosPermitidos = ['disponible', 'asignado', 'prestado', 'en mantenimiento', 'fuera de servicio', 'baja'];

        if (!in_array(strtolower($nuevoEstado), $estadosPermitidos, true)) {
            throw new BusinessRuleException(
                "Estado '{$nuevoEstado}' no es válido para equipos.",
                'ESTADO_EQUIPO_INVALIDO'
            );
        }

        $estadoAnterior = $equipo->estado;
        $equipo->estado = $nuevoEstado;
        if ($motivo) {
            $equipo->observaciones = trim(($equipo->observaciones ? $equipo->observaciones . " | " : "") . "[{$nuevoEstado}] " . $motivo);
        }
        $equipo->save();

        $this->bitacoraService->registrar(
            accion: 'Cambio de Estado Equipo',
            modulo: 'Inventario',
            detalle: "Equipo ID {$equipo->id} [{$equipo->codigo_inventario}] cambió de '{$estadoAnterior}' a '{$nuevoEstado}'. Motivo: " . ($motivo ?? 'No especificado'),
            userId: $userId
        );

        return $equipo;
    }

    /**
     * Mover un equipo a otro laboratorio o estación de trabajo.
     * Registra el movimiento histórico en la tabla 'movimiento_equipos'.
     */
    public function mover(
        Equipo $equipo,
        ?int $laboratorioDestinoId,
        ?int $estacionDestinoId,
        string $motivo,
        int $realizadoPor
    ): MovimientoEquipo {
        $estacionOrigen = $equipo->estacion;
        $labOrigenId = $estacionOrigen ? $estacionOrigen->laboratorio_id : null;

        // Validar que la estación destino exista y coincida con el laboratorio de destino
        if ($estacionDestinoId) {
            $estacionDestino = Estacion::findOrFail($estacionDestinoId);
            if ($laboratorioDestinoId && $estacionDestino->laboratorio_id !== $laboratorioDestinoId) {
                throw new BusinessRuleException(
                    "La estación seleccionada no pertenece al laboratorio destino especificado.",
                    'ESTACION_LABORATORIO_INCONSISTENTE'
                );
            }
            $laboratorioDestinoId = $estacionDestino->laboratorio_id;
        }

        // Registrar en movimiento_equipos
        $movimiento = MovimientoEquipo::create([
            'equipo_id' => $equipo->id,
            'laboratorio_origen_id' => $labOrigenId,
            'laboratorio_destino_id' => $laboratorioDestinoId,
            'realizado_por' => $realizadoPor,
            'fecha_movimiento' => Carbon::now(),
            'motivo' => $motivo,
        ]);

        // Actualizar la estación del equipo
        $equipo->estacion_id = $estacionDestinoId;
        $equipo->save();

        $this->bitacoraService->registrar(
            accion: 'Mover Equipo',
            modulo: 'Inventario',
            detalle: "Equipo ID {$equipo->id} transferido de Lab " . ($labOrigenId ?? 'Sin asignar') . " a Lab " . ($laboratorioDestinoId ?? 'Sin asignar') . ". Motivo: {$motivo}",
            userId: $realizadoPor
        );

        return $movimiento;
    }

    /**
     * Desactivar lógicamente un equipo protegiendo el historial.
     * Regla: "No se permitirá eliminar un usuario o equipo cuando existan registros históricos que deban conservarse; en su lugar, podrá desactivarse."
     */
    public function desactivar(Equipo $equipo, string $motivo, ?int $userId = null): bool
    {
        // No se puede dar de baja si se encuentra prestado actualmente
        if (strtolower($equipo->estado) === 'prestado' || strtolower($equipo->estado) === 'asignado') {
            throw new BusinessRuleException(
                "No se puede dar de baja el equipo '{$equipo->codigo_inventario}' porque está actualmente prestado o asignado.",
                'EQUIPO_EN_PRESTAMO'
            );
        }

        $equipo->estado = 'baja';
        $equipo->estacion_id = null; // Desvincular de estación activa
        $equipo->observaciones = trim(($equipo->observaciones ? $equipo->observaciones . " | " : "") . "[BAJA] " . $motivo);
        $equipo->save();

        $this->bitacoraService->registrar(
            accion: 'Dar de Baja Equipo',
            modulo: 'Inventario',
            detalle: "Equipo ID {$equipo->id} [{$equipo->codigo_inventario}] desactivado lógicamente (baja). Motivo: {$motivo}",
            userId: $userId
        );

        return true;
    }

    /**
     * Listar equipos con filtros por laboratorio, estado, categoría, búsqueda y paginación.
     */
    public function listar(array $filtros = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Equipo::with(['categoria', 'estacion.laboratorio', 'instalacionesSoftware.software']);

        if (!empty($filtros['estado'])) {
            $query->where('estado', $filtros['estado']);
        }

        if (!empty($filtros['categoria_id'])) {
            $query->where('categoria_equipo_id', $filtros['categoria_id']);
        }

        if (!empty($filtros['laboratorio_id'])) {
            $labId = $filtros['laboratorio_id'];
            $query->whereHas('estacion', function ($q) use ($labId) {
                $q->where('laboratorio_id', $labId);
            });
        }

        if (!empty($filtros['estacion_id'])) {
            $query->where('estacion_id', $filtros['estacion_id']);
        }

        if (!empty($filtros['buscar'])) {
            $buscar = $filtros['buscar'];
            $query->where(function ($q) use ($buscar) {
                $q->where('codigo_inventario', 'like', "%{$buscar}%")
                  ->orWhere('nombre', 'like', "%{$buscar}%")
                  ->orWhere('numero_serie', 'like', "%{$buscar}%")
                  ->orWhere('marca', 'like', "%{$buscar}%")
                  ->orWhere('modelo', 'like', "%{$buscar}%");
            });
        }

        return $query->orderBy('codigo_inventario')->paginate($perPage);
    }

    /**
     * Buscar un equipo por su código de inventario o escaneo de código de barras.
     */
    public function buscarPorCodigo(string $codigo): ?Equipo
    {
        return Equipo::with(['categoria', 'estacion.laboratorio', 'instalacionesSoftware.software'])
            ->where('codigo_inventario', trim($codigo))
            ->orWhere('numero_serie', trim($codigo))
            ->first();
    }

    /**
     * Verificar si un equipo se encuentra operativo y elegible para préstamo o uso.
     */
    public function verificarOperativo(Equipo $equipo): bool
    {
        return $equipo->esOperativo();
    }
}
