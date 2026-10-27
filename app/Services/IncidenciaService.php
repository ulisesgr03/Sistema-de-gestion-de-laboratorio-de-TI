<?php

namespace App\Services;

use App\Models\Equipo;
use App\Models\Incidencia;
use DomainException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class IncidenciaService
{
    public function __construct(
        protected BitacoraService $bitacoraService
    ) {}

    /**
     * Registra un nuevo reporte de avería o incidencia técnica.
     */
    public function reportarIncidencia(array $datos, ?int $reportanteId = null): Incidencia
    {
        return DB::transaction(function () use ($datos, $reportanteId) {
            $incidencia = Incidencia::create([
                'equipo_id'       => $datos['equipo_id'] ?? null,
                'laboratorio_id'  => $datos['laboratorio_id'],
                'reportado_por'   => $reportanteId ?? Auth::id(),
                'asignado_a'      => $datos['asignado_a'] ?? null,
                'titulo'          => $datos['titulo'],
                'descripcion'     => $datos['descripcion'] ?? $datos['titulo'],
                'prioridad'       => strtolower($datos['prioridad'] ?? 'media'),
                'estado'          => strtolower($datos['estado'] ?? 'reportada'),
                'fecha_resolucion'=> null,
            ]);

            // Si la avería compromete un equipo específico, marcarlo en taller preventivo/mantenimiento
            if (!empty($datos['equipo_id'])) {
                $equipo = Equipo::find($datos['equipo_id']);
                if ($equipo && $equipo->estado !== 'mantenimiento') {
                    $equipo->update(['estado' => 'mantenimiento']);
                }
            }

            $this->bitacoraService->registrar(
                accion: "Nueva incidencia #{$incidencia->id}: {$incidencia->titulo}",
                modulo: 'Mesa de Incidencias',
                detalle: [
                    'incidencia_id'  => $incidencia->id,
                    'laboratorio_id' => $incidencia->laboratorio_id,
                    'prioridad'      => $incidencia->prioridad,
                    'equipo_id'      => $incidencia->equipo_id,
                ],
                userId: $reportanteId ?? Auth::id()
            );

            return $incidencia;
        });
    }

    /**
     * Asigna un técnico de soporte responsable para diagnosticar y reparar la incidencia.
     */
    public function asignarTecnico(
        int $incidenciaId,
        int $tecnicoId,
        ?string $nuevoEstado = 'en_diagnostico',
        ?int $operadorId = null
    ): Incidencia {
        $incidencia = Incidencia::findOrFail($incidenciaId);

        $incidencia->update([
            'asignado_a' => $tecnicoId,
            'estado'     => $nuevoEstado ?? $incidencia->estado,
        ]);

        $this->bitacoraService->registrar(
            accion: "Asignación técnica en ticket #{$incidencia->id}",
            modulo: 'Mesa de Incidencias',
            detalle: [
                'incidencia_id' => $incidencia->id,
                'tecnico_id'    => $tecnicoId,
                'estado'        => $incidencia->estado,
            ],
            userId: $operadorId ?? Auth::id()
        );

        return $incidencia;
    }

    /**
     * Actualiza el estado del ticket en el flujo Kanban.
     */
    public function cambiarEstado(
        int $incidenciaId,
        string $nuevoEstado,
        ?string $notas = null,
        bool $restaurarEquipo = true,
        ?int $operadorId = null
    ): Incidencia {
        return DB::transaction(function () use (
            $incidenciaId,
            $nuevoEstado,
            $notas,
            $restaurarEquipo,
            $operadorId
        ) {
            $incidencia = Incidencia::with('equipo')->lockForUpdate()->findOrFail($incidenciaId);
            $estadoAnterior = $incidencia->estado;
            $estadoNormalizado = strtolower($nuevoEstado);

            $updateData = [
                'estado' => $estadoNormalizado,
            ];

            if ($notas) {
                $updateData['descripcion'] = $incidencia->descripcion 
                    ? "{$incidencia->descripcion}\n[Actualización]: {$notas}" 
                    : $notas;
            }

            // Si se marca como resuelta, registrar fecha de cierre y liberar el hardware
            if ($estadoNormalizado === 'resuelta') {
                $updateData['fecha_resolucion'] = now();

                if ($restaurarEquipo && $incidencia->equipo && $incidencia->equipo->estado === 'mantenimiento') {
                    $incidencia->equipo->update(['estado' => 'disponible']);
                }
            }

            $incidencia->update($updateData);

            $this->bitacoraService->registrar(
                accion: "Avance ticket #{$incidencia->id} ({$estadoAnterior} -> {$estadoNormalizado})",
                modulo: 'Mesa de Incidencias',
                detalle: [
                    'incidencia_id'   => $incidencia->id,
                    'estado_anterior' => $estadoAnterior,
                    'nuevo_estado'    => $estadoNormalizado,
                    'notas'           => $notas,
                ],
                userId: $operadorId ?? Auth::id()
            );

            return $incidencia;
        });
    }

    /**
     * Obtiene los tickets organizados por las 4 columnas del tablero Kanban oficial UPTex TI.
     */
    public function obtenerTableroKanban(?int $laboratorioId = null): array
    {
        $query = Incidencia::with(['equipo', 'laboratorio', 'reportante', 'encargado'])
            ->latest();

        if ($laboratorioId !== null) {
            $query->where('laboratorio_id', $laboratorioId);
        }

        $tickets = $query->get();

        $reportadas = $tickets->where('estado', 'reportada')->values();
        $enDiagnostico = $tickets->where('estado', 'en_diagnostico')->values();
        $enReparacion = $tickets->where('estado', 'en_reparacion')->values();
        $resueltas = $tickets->where('estado', 'resuelta')->values();

        $criticas = $tickets->whereIn('estado', ['reportada', 'en_diagnostico', 'en_reparacion'])
            ->where('prioridad', 'alta')
            ->count();

        return [
            'columnas' => [
                'reportadas'     => $reportadas,
                'en_diagnostico' => $enDiagnostico,
                'en_reparacion'  => $enReparacion,
                'resueltas'      => $resueltas,
            ],
            'metricas' => [
                'total_activas'  => $reportadas->count() + $enDiagnostico->count() + $enReparacion->count(),
                'criticas_altas' => $criticas,
                'resueltas_hoy'  => $resueltas->where('fecha_resolucion', '>=', now()->startOfDay())->count(),
            ],
        ];
    }
}
