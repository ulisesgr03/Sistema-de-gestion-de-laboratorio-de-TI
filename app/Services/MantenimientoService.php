<?php

namespace App\Services;

use App\Exceptions\BusinessRuleException;
use App\Models\Equipo;
use App\Models\Laboratorio;
use App\Models\Mantenimiento;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class MantenimientoService
{
    public function __construct(
        protected BitacoraService $bitacoraService,
        protected EquipoService $equipoService
    ) {}

    /**
     * Programar un nuevo mantenimiento (preventivo o correctivo).
     */
    public function programar(array $datos, int $responsableId): Mantenimiento
    {
        $equipoId = $datos['equipo_id'] ?? null;
        $laboratorioId = $datos['laboratorio_id'] ?? null;

        $equipo = null;
        if ($equipoId) {
            $equipo = Equipo::with('estacion')->findOrFail($equipoId);
            if (!$laboratorioId && $equipo->estacion) {
                $laboratorioId = $equipo->estacion->laboratorio_id;
            }
        }

        $tipo = strtolower($datos['tipo'] ?? 'correctivo'); // preventivo, correctivo
        $fechaProgramada = Carbon::parse($datos['fecha_programada']);

        $mantenimiento = Mantenimiento::create([
            'equipo_id' => $equipoId,
            'laboratorio_id' => $laboratorioId,
            'user_id' => $responsableId,
            'tipo' => $tipo,
            'titulo' => trim($datos['titulo']),
            'actividades' => trim($datos['actividades']),
            'fecha_programada' => $fechaProgramada,
            'estado' => 'programado',
            'costo' => $datos['costo'] ?? null,
        ]);

        // Si el mantenimiento está programado para hoy mismo o de inmediato, cambiar equipo a 'en mantenimiento'
        if ($equipo && $fechaProgramada->isToday()) {
            $this->equipoService->cambiarEstado(
                $equipo,
                'en mantenimiento',
                "Mantenimiento programado ID {$mantenimiento->id} [{$tipo}]: {$mantenimiento->titulo}",
                $responsableId
            );
        }

        $this->bitacoraService->registrar(
            accion: 'Programar Mantenimiento',
            modulo: 'Mantenimientos',
            detalle: "Mantenimiento ID {$mantenimiento->id} programado para {$fechaProgramada->format('d/m/Y')}: '{$mantenimiento->titulo}'",
            userId: $responsableId
        );

        return $mantenimiento;
    }

    /**
     * Iniciar la ejecución de un mantenimiento programado.
     */
    public function iniciar(Mantenimiento $mantenimiento, ?int $userId = null): Mantenimiento
    {
        $mantenimiento->estado = 'en_proceso';
        $mantenimiento->fecha_inicio = Carbon::now();
        $mantenimiento->save();

        if ($mantenimiento->equipo) {
            $this->equipoService->cambiarEstado(
                $mantenimiento->equipo,
                'en mantenimiento',
                "En ejecución: Mantenimiento ID {$mantenimiento->id}",
                $userId
            );
        }

        $this->bitacoraService->registrar(
            accion: 'Iniciar Mantenimiento',
            modulo: 'Mantenimientos',
            detalle: "Mantenimiento ID {$mantenimiento->id} en ejecución.",
            userId: $userId
        );

        return $mantenimiento;
    }

    /**
     * Completar un mantenimiento, registrando el resultado y verificando funcionamiento.
     * Regla: "Cuando termine una reparación, el encargado verificará el funcionamiento antes de marcar el equipo como disponible."
     */
    public function completar(
        Mantenimiento $mantenimiento,
        array $datosCierre,
        int $completadoPor
    ): Mantenimiento {
        $resultado = trim($datosCierre['resultado'] ?? 'Mantenimiento finalizado con éxito.');
        $funcionamientoVerificado = !empty($datosCierre['verificacion_funcionamiento_exitosa']);

        $mantenimiento->estado = 'completado';
        $mantenimiento->fecha_fin = Carbon::now();
        $mantenimiento->resultado = $resultado;
        if (isset($datosCierre['costo'])) {
            $mantenimiento->costo = $datosCierre['costo'];
        }
        $mantenimiento->save();

        // Si el funcionamiento fue verificado con éxito, retornar el equipo a 'disponible'
        if ($mantenimiento->equipo) {
            if ($funcionamientoVerificado) {
                $this->equipoService->cambiarEstado(
                    $mantenimiento->equipo,
                    'disponible',
                    "Mantenimiento ID {$mantenimiento->id} completado y verificado.",
                    $completadoPor
                );
            } else {
                // Si la prueba de funcionamiento no fue satisfactoria, marcar fuera de servicio
                $this->equipoService->cambiarEstado(
                    $mantenimiento->equipo,
                    'fuera de servicio',
                    "Mantenimiento ID {$mantenimiento->id} finalizado pero requiere revisión adicional.",
                    $completadoPor
                );
            }
        }

        $this->bitacoraService->registrar(
            accion: 'Completar Mantenimiento',
            modulo: 'Mantenimientos',
            detalle: "Mantenimiento ID {$mantenimiento->id} completado. Resultado: {$resultado}",
            userId: $completadoPor
        );

        return $mantenimiento;
    }

    /**
     * Cancelar un mantenimiento programado con justificación.
     */
    public function cancelar(Mantenimiento $mantenimiento, string $motivo, int $canceladoPor): Mantenimiento
    {
        $mantenimiento->estado = 'cancelado';
        $mantenimiento->resultado = "[CANCELADO]: {$motivo}";
        $mantenimiento->save();

        if ($mantenimiento->equipo && $mantenimiento->equipo->estado === 'en mantenimiento') {
            $this->equipoService->cambiarEstado(
                $mantenimiento->equipo,
                'disponible',
                "Mantenimiento ID {$mantenimiento->id} cancelado.",
                $canceladoPor
            );
        }

        $this->bitacoraService->registrar(
            accion: 'Cancelar Mantenimiento',
            modulo: 'Mantenimientos',
            detalle: "Mantenimiento ID {$mantenimiento->id} cancelado. Motivo: {$motivo}",
            userId: $canceladoPor
        );

        return $mantenimiento;
    }

    /**
     * Obtener mantenimientos programados futuros o pendientes.
     */
    public function obtenerMantenimientosProgramados(): Collection
    {
        return Mantenimiento::with(['equipo', 'laboratorio', 'responsable'])
            ->whereIn('estado', ['programado', 'en_proceso'])
            ->orderBy('fecha_programada')
            ->get();
    }

    /**
     * Listar mantenimientos con filtros y paginación.
     */
    public function listar(array $filtros = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Mantenimiento::with(['equipo', 'laboratorio', 'responsable'])
            ->latest('fecha_programada');

        if (!empty($filtros['estado'])) {
            $query->where('estado', $filtros['estado']);
        }

        if (!empty($filtros['tipo'])) {
            $query->where('tipo', $filtros['tipo']);
        }

        if (!empty($filtros['laboratorio_id'])) {
            $query->where('laboratorio_id', $filtros['laboratorio_id']);
        }

        if (!empty($filtros['equipo_id'])) {
            $query->where('equipo_id', $filtros['equipo_id']);
        }

        if (!empty($filtros['buscar'])) {
            $buscar = $filtros['buscar'];
            $query->where(function ($q) use ($buscar) {
                $q->where('titulo', 'like', "%{$buscar}%")
                  ->orWhere('actividades', 'like', "%{$buscar}%")
                  ->orWhere('resultado', 'like', "%{$buscar}%");
            });
        }

        return $query->paginate($perPage);
    }
}
