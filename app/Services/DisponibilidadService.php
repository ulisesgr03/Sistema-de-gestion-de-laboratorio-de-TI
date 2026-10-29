<?php

namespace App\Services;

use App\Models\AsignacionEquipo;
use App\Models\Equipo;
use App\Models\Estacion;
use App\Models\Horario;
use App\Models\Laboratorio;
use App\Models\Mantenimiento;
use App\Models\ReservaLaboratorio;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

class DisponibilidadService
{
    /**
     * Consultar laboratorios disponibles en un rango de fecha y hora.
     */
    public function consultarLaboratorios(Carbon $inicio, Carbon $fin, ?int $capacidadMinima = null): Collection
    {
        $diaSemana = $inicio->dayOfWeekIso; // 1 = Lunes, 7 = Domingo
        $horaInicio = $inicio->format('H:i:s');
        $horaFin = $fin->format('H:i:s');

        // IDs de laboratorios que tienen reservas superpuestas
        $laboratoriosOcupadosIds = ReservaLaboratorio::whereIn('estado', ['aprobada', 'en_curso', 'pendiente'])
            ->where(function ($query) use ($inicio, $fin) {
                $query->where('inicio', '<', $fin)
                      ->where('fin', '>', $inicio);
            })
            ->pluck('laboratorio_id')
            ->toArray();

        // IDs de laboratorios con mantenimiento programado en ese horario
        $laboratoriosMantenimientoIds = Mantenimiento::whereIn('estado', ['programado', 'en_proceso'])
            ->whereNotNull('laboratorio_id')
            ->where(function ($query) use ($inicio, $fin) {
                $query->where('fecha_programada', '<', $fin)
                      ->where(function ($sub) use ($inicio) {
                          $sub->whereNull('fecha_fin')
                              ->orWhere('fecha_fin', '>', $inicio);
                      });
            })
            ->pluck('laboratorio_id')
            ->toArray();

        $excluirIds = array_unique(array_merge($laboratoriosOcupadosIds, $laboratoriosMantenimientoIds));

        $query = Laboratorio::where('activo', true)
            ->whereNotIn('id', $excluirIds)
            ->withCount(['estaciones', 'equipos']);

        if ($capacidadMinima) {
            $query->where('capacidad', '>=', $capacidadMinima);
        }

        return $query->orderBy('nombre')->get();
    }

    /**
     * Verificar si un laboratorio específico está disponible en un horario dado.
     */
    public function estaLaboratorioDisponible(int $laboratorioId, Carbon $inicio, Carbon $fin): bool
    {
        $laboratorio = Laboratorio::find($laboratorioId);
        if (!$laboratorio || !$laboratorio->activo) {
            return false;
        }

        $tieneReserva = ReservaLaboratorio::where('laboratorio_id', $laboratorioId)
            ->whereIn('estado', ['aprobada', 'en_curso', 'pendiente'])
            ->where(function ($query) use ($inicio, $fin) {
                $query->where('inicio', '<', $fin)
                      ->where('fin', '>', $inicio);
            })
            ->exists();

        if ($tieneReserva) {
            return false;
        }

        $tieneMantenimiento = Mantenimiento::where('laboratorio_id', $laboratorioId)
            ->whereIn('estado', ['programado', 'en_proceso'])
            ->where(function ($query) use ($inicio, $fin) {
                $query->where('fecha_programada', '<', $fin)
                      ->where(function ($sub) use ($inicio) {
                          $sub->whereNull('fecha_fin')
                              ->orWhere('fecha_fin', '>', $inicio);
                      });
            })
            ->exists();

        return !$tieneMantenimiento;
    }

    /**
     * Consultar estaciones disponibles en un laboratorio.
     */
    public function consultarEstaciones(int $laboratorioId, ?string $estado = 'disponible'): Collection
    {
        return Estacion::where('laboratorio_id', $laboratorioId)
            ->when($estado, fn($q) => $q->where('estado', $estado))
            ->with(['equipos' => fn($q) => $q->where('estado', 'disponible')])
            ->orderBy('nombre')
            ->get();
    }

    /**
     * Consultar equipos elegibles para préstamo inmediato.
     */
    public function consultarEquiposDisponibles(?int $categoriaId = null, ?int $laboratorioId = null): Collection
    {
        $query = Equipo::with(['categoria', 'estacion.laboratorio'])
            ->where('estado', 'disponible');

        if ($categoriaId) {
            $query->where('categoria_equipo_id', $categoriaId);
        }

        if ($laboratorioId) {
            $query->whereHas('estacion', function ($q) use ($laboratorioId) {
                $q->where('laboratorio_id', $laboratorioId);
            });
        }

        return $query->orderBy('nombre')->get();
    }

    /**
     * Verificar si un equipo específico está disponible para préstamo o reserva.
     */
    public function estaEquipoDisponible(int $equipoId): bool
    {
        $equipo = Equipo::find($equipoId);
        if (!$equipo || !$equipo->estaDisponible()) {
            return false;
        }

        // Verificar que no tenga un préstamo activo sin devolución
        $tienePrestamoActivo = AsignacionEquipo::where('equipo_id', $equipoId)
            ->whereIn('estado', ['prestado', 'asignado', 'activo'])
            ->exists();

        if ($tienePrestamoActivo) {
            return false;
        }

        // Verificar que no tenga mantenimiento activo
        $tieneMantenimientoActivo = Mantenimiento::where('equipo_id', $equipoId)
            ->whereIn('estado', ['programado', 'en_proceso'])
            ->exists();

        return !$tieneMantenimientoActivo;
    }
}
