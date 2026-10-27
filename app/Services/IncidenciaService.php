<?php

namespace App\Services;

use App\Exceptions\BusinessRuleException;
use App\Models\Equipo;
use App\Models\Incidencia;
use App\Models\Laboratorio;
use App\Models\ReservaLaboratorio;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class IncidenciaService
{
    public function __construct(
        protected BitacoraService $bitacoraService,
        protected EquipoService $equipoService
    ) {}

    /**
     * Reportar una nueva incidencia técnica en un equipo o laboratorio.
     */
    public function reportar(array $datos, User $reportante): Incidencia
    {
        $laboratorioId = $datos['laboratorio_id'] ?? null;
        $equipoId = $datos['equipo_id'] ?? null;

        $equipo = null;
        if ($equipoId) {
            $equipo = Equipo::with('estacion')->findOrFail($equipoId);
            if (!$laboratorioId && $equipo->estacion) {
                $laboratorioId = $equipo->estacion->laboratorio_id;
            }
        }

        if (!$laboratorioId) {
            $primerLab = Laboratorio::where('activo', true)->first();
            if (!$primerLab) {
                $primerLab = Laboratorio::create([
                    'nombre' => 'Ventanilla General de TI',
                    'ubicacion' => 'Edificio Central',
                    'capacidad' => 10,
                    'activo' => true,
                ]);
            }
            $laboratorioId = $primerLab->id;
        }

        $prioridad = strtolower($datos['prioridad'] ?? 'media');
        $prioridadesValidas = ['baja', 'media', 'alta', 'critica'];
        if (!in_array($prioridad, $prioridadesValidas, true)) {
            $prioridad = 'media';
        }

        $inutilizarEquipo = !empty($datos['marcar_fuera_de_servicio']) || in_array($prioridad, ['alta', 'critica'], true);

        $incidencia = Incidencia::create([
            'equipo_id' => $equipoId,
            'laboratorio_id' => $laboratorioId,
            'reportado_por' => $reportante->id,
            'asignado_a' => $datos['asignado_a'] ?? null,
            'titulo' => trim($datos['titulo']),
            'descripcion' => trim($datos['descripcion']),
            'prioridad' => $prioridad,
            'estado' => 'abierta',
        ]);

        // Si la falla es crítica o inutiliza el equipo, actualizar estado en inventario
        if ($equipo && $inutilizarEquipo) {
            $this->equipoService->cambiarEstado(
                $equipo,
                'fuera de servicio',
                "Incidencia ID {$incidencia->id} [Prioridad: {$prioridad}]: {$incidencia->titulo}",
                $reportante->id
            );
        }

        $this->bitacoraService->registrar(
            accion: 'Reportar Incidencia',
            modulo: 'Incidencias',
            detalle: "Incidencia reportada por {$reportante->name}: '{$incidencia->titulo}' [Prioridad: {$prioridad}]",
            userId: $reportante->id
        );

        return $incidencia;
    }

    /**
     * Asignar y poner en revisión/atención una incidencia.
     */
    public function asignar(Incidencia $incidencia, int $tecnicoId, ?int $asignadoPor = null): Incidencia
    {
        $tecnico = User::findOrFail($tecnicoId);

        $incidencia->asignado_a = $tecnico->id;
        $incidencia->estado = 'en revision';
        $incidencia->save();

        $this->bitacoraService->registrar(
            accion: 'Asignar Incidencia',
            modulo: 'Incidencias',
            detalle: "Incidencia ID {$incidencia->id} asignada a técnico {$tecnico->name}",
            userId: $asignadoPor
        );

        return $incidencia;
    }

    /**
     * Iniciar proceso de reparación o diagnóstico.
     */
    public function enProceso(Incidencia $incidencia, ?int $userId = null): Incidencia
    {
        $incidencia->estado = 'en proceso';
        $incidencia->save();

        if ($incidencia->equipo && $incidencia->equipo->estado !== 'fuera de servicio') {
            $this->equipoService->cambiarEstado(
                $incidencia->equipo,
                'en mantenimiento',
                "En atención por Incidencia ID {$incidencia->id}",
                $userId
            );
        }

        $this->bitacoraService->registrar(
            accion: 'Incidencia en Proceso',
            modulo: 'Incidencias',
            detalle: "Incidencia ID {$incidencia->id} pasó a estado 'en proceso'.",
            userId: $userId
        );

        return $incidencia;
    }

    /**
     * Resolver la incidencia y registrar el dictamen técnico.
     */
    public function resolver(
        Incidencia $incidencia,
        string $diagnostico,
        int $resueltoPor,
        bool $restablecerEquipoDisponible = true
    ): Incidencia {
        $incidencia->estado = 'resuelta';
        $incidencia->fecha_resolucion = Carbon::now();
        $incidencia->descripcion = trim($incidencia->descripcion . "\n\n[Dictamen de Resolución]: " . $diagnostico);
        $incidencia->save();

        // Si el equipo estaba fuera de servicio o en mantenimiento, verificar si se restablece
        if ($incidencia->equipo && $restablecerEquipoDisponible) {
            $this->equipoService->cambiarEstado(
                $incidencia->equipo,
                'disponible',
                "Incidencia ID {$incidencia->id} resuelta con éxito.",
                $resueltoPor
            );
        }

        $this->bitacoraService->registrar(
            accion: 'Resolver Incidencia',
            modulo: 'Incidencias',
            detalle: "Incidencia ID {$incidencia->id} resuelta. Diagnóstico: {$diagnostico}",
            userId: $resueltoPor
        );

        return $incidencia;
    }

    /**
     * Listar incidencias con filtros y paginación.
     */
    public function listar(array $filtros = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Incidencia::with(['laboratorio', 'equipo', 'reportante', 'encargado'])
            ->latest();

        if (!empty($filtros['estado'])) {
            $query->where('estado', $filtros['estado']);
        }

        if (!empty($filtros['prioridad'])) {
            $query->where('prioridad', $filtros['prioridad']);
        }

        if (!empty($filtros['laboratorio_id'])) {
            $query->where('laboratorio_id', $filtros['laboratorio_id']);
        }

        if (!empty($filtros['equipo_id'])) {
            $query->where('equipo_id', $filtros['equipo_id']);
        }

        if (!empty($filtros['reportado_por'])) {
            $query->where('reportado_por', $filtros['reportado_por']);
        }

        if (!empty($filtros['buscar'])) {
            $buscar = $filtros['buscar'];
            $query->where(function ($q) use ($buscar) {
                $q->where('titulo', 'like', "%{$buscar}%")
                  ->orWhere('descripcion', 'like', "%{$buscar}%");
            });
        }

        return $query->paginate($perPage);
    }
}
