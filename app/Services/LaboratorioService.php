<?php

namespace App\Services;

use App\Models\Estacion;
use App\Models\Laboratorio;
use App\Models\UsuarioLaboratorio;
use DomainException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LaboratorioService
{
    public function __construct(
        protected BitacoraService $bitacoraService
    ) {}

    /**
     * Obtiene el estado consolidado de la infraestructura de laboratorios para el Dashboard / Portal.
     */
    public function obtenerEstadoGlobal(): array
    {
        $laboratorios = Laboratorio::withCount(['estaciones'])->get();

        $totalSalas = $laboratorios->count();
        $salasActivas = $laboratorios->where('activo', true)->count();
        $capacidadTotal = $laboratorios->sum('capacidad');

        $estaciones = Estacion::all();
        $totalEstaciones = $estaciones->count();
        $ocupadas = $estaciones->where('estado', 'ocupada')->count();
        $libres = $estaciones->whereIn('estado', ['libre', 'disponible'])->count();
        $enMantenimiento = $estaciones->whereIn('estado', ['mantenimiento', 'averiada'])->count();
        $bloqueadas = $estaciones->where('estado', 'bloqueada')->count();

        $porcentajeOcupacion = $totalEstaciones > 0
            ? round(($ocupadas / $totalEstaciones) * 100, 1)
            : 0.0;

        return [
            'total_salas'           => $totalSalas,
            'salas_activas'         => $salasActivas,
            'capacidad_total'       => $capacidadTotal,
            'total_estaciones'      => $totalEstaciones,
            'estaciones_libres'     => $libres,
            'estaciones_ocupadas'   => $ocupadas,
            'estaciones_taller'     => $enMantenimiento,
            'estaciones_bloqueadas' => $bloqueadas,
            'porcentaje_ocupacion'  => $porcentajeOcupacion,
        ];
    }

    /**
     * Obtiene la distribución física de estaciones y telemetría de una sala para la Malla Topológica.
     */
    public function obtenerMallaTopologica(int $laboratorioId): array
    {
        $laboratorio = Laboratorio::with(['estaciones.equipos.categoria'])->findOrFail($laboratorioId);

        $estaciones = $laboratorio->estaciones->sortBy('nombre')->values();

        $ocupadas = $estaciones->where('estado', 'ocupada')->count();
        $libres = $estaciones->whereIn('estado', ['libre', 'disponible'])->count();
        $mantenimiento = $estaciones->whereIn('estado', ['mantenimiento', 'averiada'])->count();
        $bloqueadas = $estaciones->where('estado', 'bloqueada')->count();

        return [
            'laboratorio' => $laboratorio,
            'conteo' => [
                'total'         => $estaciones->count(),
                'ocupadas'      => $ocupadas,
                'libres'        => $libres,
                'mantenimiento' => $mantenimiento,
                'bloqueadas'    => $bloqueadas,
            ],
            'estaciones' => $estaciones,
        ];
    }

    /**
     * Ejecuta el bloqueo masivo o selectivo de terminales dentro de un laboratorio.
     */
    public function bloquearTerminales(
        int $laboratorioId,
        array $estacionesIds = [],
        ?string $motivo = null,
        ?int $operadorId = null
    ): int {
        return DB::transaction(function () use ($laboratorioId, $estacionesIds, $motivo, $operadorId) {
            $query = Estacion::where('laboratorio_id', $laboratorioId);

            if (!empty($estacionesIds)) {
                $query->whereIn('id', $estacionesIds);
            }

            $afectadas = $query->update(['estado' => 'bloqueada']);

            $laboratorio = Laboratorio::find($laboratorioId);

            $this->bitacoraService->registrar(
                accion: "Bloqueo masivo en {$laboratorio?->nombre}",
                modulo: 'Malla Topológica',
                detalle: [
                    'laboratorio_id'       => $laboratorioId,
                    'estaciones_afectadas' => $afectadas,
                    'motivo'               => $motivo ?? 'Bloqueo administrativo por seguridad o fin de sesión',
                ],
                userId: $operadorId ?? Auth::id()
            );

            return $afectadas;
        });
    }

    /**
     * Desbloquea terminales de un laboratorio, regresándolas a estado libre/disponible.
     */
    public function desbloquearTerminales(
        int $laboratorioId,
        array $estacionesIds = [],
        ?int $operadorId = null
    ): int {
        return DB::transaction(function () use ($laboratorioId, $estacionesIds, $operadorId) {
            $query = Estacion::where('laboratorio_id', $laboratorioId)->where('estado', 'bloqueada');

            if (!empty($estacionesIds)) {
                $query->whereIn('id', $estacionesIds);
            }

            $afectadas = $query->update(['estado' => 'libre']);

            $laboratorio = Laboratorio::find($laboratorioId);

            $this->bitacoraService->registrar(
                accion: "Desbloqueo de estaciones en {$laboratorio?->nombre}",
                modulo: 'Malla Topológica',
                detalle: [
                    'laboratorio_id'       => $laboratorioId,
                    'estaciones_liberadas' => $afectadas,
                ],
                userId: $operadorId ?? Auth::id()
            );

            return $afectadas;
        });
    }

    /**
     * Cambia el estado operativo de una estación individual.
     */
    public function cambiarEstadoEstacion(int $estacionId, string $nuevoEstado, ?int $operadorId = null): Estacion
    {
        $estacion = Estacion::with('laboratorio')->findOrFail($estacionId);
        $anterior = $estacion->estado;

        $estacion->update(['estado' => strtolower($nuevoEstado)]);

        $this->bitacoraService->registrar(
            accion: "Cambio de estado: {$estacion->nombre} ({$anterior} -> {$nuevoEstado})",
            modulo: 'Malla Topológica',
            detalle: [
                'estacion_id'    => $estacion->id,
                'laboratorio_id' => $estacion->laboratorio_id,
                'nuevo_estado'   => $nuevoEstado,
            ],
            userId: $operadorId ?? Auth::id()
        );

        return $estacion;
    }

    /**
     * Asigna un usuario como encargado o técnico de un laboratorio.
     */
    public function asignarEncargado(
        int $laboratorioId,
        int $userId,
        string $tipoAcceso = 'responsable'
    ): UsuarioLaboratorio {
        return UsuarioLaboratorio::updateOrCreate(
            [
                'user_id'        => $userId,
                'laboratorio_id' => $laboratorioId,
            ],
            [
                'tipo_acceso'    => strtolower($tipoAcceso),
            ]
        );
    }

    /**
     * Habilita o deshabilita operativamente un laboratorio (mantenimiento / contingencia).
     */
    public function actualizarEstadoOperativo(
        int $laboratorioId,
        bool $activo,
        ?string $motivo = null,
        ?int $operadorId = null
    ): Laboratorio {
        $laboratorio = Laboratorio::findOrFail($laboratorioId);
        $laboratorio->update(['activo' => $activo]);

        $accion = $activo ? 'Apertura de laboratorio' : 'Cierre preventivo de laboratorio';

        $this->bitacoraService->registrar(
            accion: "{$accion}: {$laboratorio->nombre}",
            modulo: 'Gestión de Laboratorios',
            detalle: [
                'laboratorio_id' => $laboratorio->id,
                'activo'         => $activo,
                'motivo'         => $motivo,
            ],
            userId: $operadorId ?? Auth::id()
        );

        return $laboratorio;
    }
}
