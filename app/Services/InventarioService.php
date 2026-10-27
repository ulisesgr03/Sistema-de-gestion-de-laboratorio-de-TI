<?php

namespace App\Services;

use App\Models\AsignacionEquipo;
use App\Models\Equipo;
use App\Models\Estacion;
use App\Models\Laboratorio;
use App\Models\MovimientoEquipo;
use App\Models\User;
use DomainException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InventarioService
{
    public function __construct(
        protected BitacoraService $bitacoraService
    ) {}

    /**
     * Registra un nuevo equipo en el inventario institucional.
     */
    public function crearEquipo(array $datos, ?int $operadorId = null): Equipo
    {
        return DB::transaction(function () use ($datos, $operadorId) {
            $categoriaId = $datos['categoria_equipo_id'] ?? null;
            if (!$categoriaId) {
                $categoriaId = \App\Models\CategoriaEquipo::firstOrCreate(
                    ['nombre' => 'Cómputo General'],
                    ['descripcion' => 'Categoría estándar para equipos del laboratorio']
                )->id;
            }

            $equipo = Equipo::create([
                'categoria_equipo_id' => $categoriaId,
                'estacion_id'         => $datos['estacion_id'] ?? null,
                'codigo_inventario'   => $datos['codigo_inventario'],
                'nombre'              => $datos['nombre'],
                'marca'               => $datos['marca'] ?? null,
                'modelo'              => $datos['modelo'] ?? null,
                'numero_serie'        => $datos['numero_serie'] ?? null,
                'fecha_adquisicion'   => $datos['fecha_adquisicion'] ?? now(),
                'estado'              => $datos['estado'] ?? 'disponible',
                'observaciones'       => $datos['observaciones'] ?? null,
            ]);

            $this->bitacoraService->registrar(
                accion: "Alta de equipo {$equipo->codigo_inventario}",
                modulo: 'Inventario y Auditoría',
                detalle: [
                    'equipo_id' => $equipo->id,
                    'nombre'    => $equipo->nombre,
                    'serie'     => $equipo->numero_serie,
                ],
                userId: $operadorId
            );

            return $equipo;
        });
    }

    /**
     * Registra un préstamo en ventanilla validando disponibilidad y adeudos previos.
     */
    public function registrarPrestamo(
        int $equipoId,
        int $solicitanteId,
        int $laboratorioId,
        ?int $asignadoPorId = null,
        ?string $fechaLimite = null,
        ?string $observaciones = null
    ): AsignacionEquipo {
        return DB::transaction(function () use (
            $equipoId,
            $solicitanteId,
            $laboratorioId,
            $asignadoPorId,
            $fechaLimite,
            $observaciones
        ) {
            // 1. Bloquear registro del equipo para prevenir condiciones de carrera (Race Conditions)
            $equipo = Equipo::where('id', $equipoId)->lockForUpdate()->firstOrFail();

            if ($equipo->estado !== 'disponible') {
                throw new DomainException("El equipo '{$equipo->nombre}' ({$equipo->codigo_inventario}) no está disponible. Estado actual: {$equipo->estado}.");
            }

            // 2. Verificar que el solicitante no tenga adeudos vencidos
            $adeudos = $this->verificarAdeudos($solicitanteId);
            if ($adeudos->isNotEmpty()) {
                throw new DomainException("El solicitante tiene {$adeudos->count()} préstamo(s) con fecha de entrega vencida.");
            }

            // 3. Establecer fecha límite de devolución (por defecto 2 horas reglamentarias si no se especifica)
            $fechaRetorno = $fechaLimite ? now()->parse($fechaLimite) : now()->addHours(2);

            // 4. Crear la asignación
            $asignacion = AsignacionEquipo::create([
                'equipo_id'        => $equipo->id,
                'user_id'          => $solicitanteId,
                'laboratorio_id'   => $laboratorioId,
                'asignado_por'     => $asignadoPorId ?? Auth::id(),
                'fecha_asignacion' => now(),
                'fecha_devolucion' => $fechaRetorno,
                'estado'           => 'asignado',
                'observaciones'    => $observaciones,
            ]);

            // 5. Actualizar estado del equipo
            $equipo->update(['estado' => 'prestado']);

            // 6. Registrar en bitácora
            $this->bitacoraService->registrar(
                accion: "Préstamo en ventanilla: {$equipo->codigo_inventario}",
                modulo: 'Inventario y Auditoría',
                detalle: [
                    'asignacion_id'  => $asignacion->id,
                    'solicitante_id' => $solicitanteId,
                    'laboratorio_id' => $laboratorioId,
                    'hora_limite'    => $fechaRetorno->toDateTimeString(),
                ],
                userId: $asignadoPorId
            );

            return $asignacion;
        });
    }

    /**
     * Registra la devolución de un equipo en ventanilla.
     */
    public function registrarDevolucion(
        int $asignacionId,
        string $estadoFisico = 'bueno',
        ?string $observaciones = null,
        ?int $operadorId = null
    ): AsignacionEquipo {
        return DB::transaction(function () use ($asignacionId, $estadoFisico, $observaciones, $operadorId) {
            $asignacion = AsignacionEquipo::with('equipo')->where('id', $asignacionId)->lockForUpdate()->firstOrFail();

            if ($asignacion->estado === 'devuelto') {
                throw new DomainException("La asignación #{$asignacion->id} ya fue liquidada con anterioridad.");
            }

            // Actualizar asignación
            $asignacion->update([
                'estado'           => 'devuelto',
                'fecha_devolucion' => now(),
                'observaciones'    => $observaciones ? "{$asignacion->observaciones} | {$observaciones}" : $asignacion->observaciones,
            ]);

            // Determinar estado final del equipo
            $equipo = $asignacion->equipo;
            if ($equipo) {
                $nuevoEstado = in_array(strtolower($estadoFisico), ['danado', 'averiado', 'falla'])
                    ? 'mantenimiento'
                    : 'disponible';

                $equipo->update(['estado' => $nuevoEstado]);
            }

            // Registrar en bitácora
            $this->bitacoraService->registrar(
                accion: "Devolución de equipo: {$equipo?->codigo_inventario}",
                modulo: 'Inventario y Auditoría',
                detalle: [
                    'asignacion_id' => $asignacion->id,
                    'estado_fisico' => $estadoFisico,
                    'equipo_estado' => $equipo?->estado,
                ],
                userId: $operadorId
            );

            return $asignacion;
        });
    }

    /**
     * Registra el traslado físico de un equipo hacia otro laboratorio o estación.
     */
    public function trasladarEquipo(
        int $equipoId,
        int $laboratorioDestinoId,
        ?int $estacionDestinoId = null,
        ?string $motivo = null,
        ?int $realizadoPorId = null
    ): MovimientoEquipo {
        return DB::transaction(function () use (
            $equipoId,
            $laboratorioDestinoId,
            $estacionDestinoId,
            $motivo,
            $realizadoPorId
        ) {
            $equipo = Equipo::with('estacion.laboratorio')->where('id', $equipoId)->lockForUpdate()->firstOrFail();
            $origenId = $equipo->estacion?->laboratorio_id;

            $movimiento = MovimientoEquipo::create([
                'equipo_id'              => $equipo->id,
                'laboratorio_origen_id'  => $origenId,
                'laboratorio_destino_id' => $laboratorioDestinoId,
                'realizado_por'          => $realizadoPorId ?? Auth::id(),
                'fecha_movimiento'       => now(),
                'motivo'                 => $motivo ?? 'Reubicación de parque computacional',
            ]);

            $equipo->update([
                'estacion_id' => $estacionDestinoId,
            ]);

            $this->bitacoraService->registrar(
                accion: "Traslado de equipo {$equipo->codigo_inventario}",
                modulo: 'Inventario y Auditoría',
                detalle: [
                    'origen_id'  => $origenId,
                    'destino_id' => $laboratorioDestinoId,
                    'estacion_id'=> $estacionDestinoId,
                    'motivo'     => $motivo,
                ],
                userId: $realizadoPorId
            );

            return $movimiento;
        });
    }

    /**
     * Verifica si un alumno o usuario tiene asignaciones con fecha límite vencida.
     */
    public function verificarAdeudos(int $userId): Collection
    {
        return AsignacionEquipo::where('user_id', $userId)
            ->where(function ($query) {
                $query->where('estado', 'vencido')
                      ->orWhere(function ($q) {
                          $q->where('estado', 'asignado')
                            ->whereNotNull('fecha_devolucion')
                            ->where('fecha_devolucion', '<', now());
                      });
            })
            ->with('equipo')
            ->get();
    }

    /**
     * Calcula métricas clave para el parque computacional e indicadores de telemetría.
     */
    public function obtenerEstadisticasParqueComputacional(?int $laboratorioId = null): array
    {
        $query = Equipo::query();

        if ($laboratorioId !== null) {
            $query->whereHas('estacion', function ($q) use ($laboratorioId) {
                $q->where('laboratorio_id', $laboratorioId);
            });
        }

        $total = $query->count();
        $disponibles = (clone $query)->where('estado', 'disponible')->count();
        $prestados = (clone $query)->where('estado', 'prestado')->count();
        $enMantenimiento = (clone $query)->where('estado', 'mantenimiento')->count();

        $operativos = $disponibles + $prestados;
        $porcentajeDisp = $total > 0 ? round(($operativos / $total) * 100, 1) : 100.0;

        return [
            'total_equipos'            => $total,
            'disponibles_inmediatos'   => $disponibles,
            'en_prestamo'              => $prestados,
            'en_taller_mantenimiento'  => $enMantenimiento,
            'operativos_totales'       => $operativos,
            'porcentaje_disponibilidad'=> $porcentajeDisp,
        ];
    }
}
