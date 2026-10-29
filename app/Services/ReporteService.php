<?php

namespace App\Services;

use App\Models\AsignacionEquipo;
use App\Models\Bitacora;
use App\Models\Equipo;
use App\Models\Incidencia;
use App\Models\Laboratorio;
use App\Models\Licencia;
use App\Models\Mantenimiento;
use App\Models\ReservaLaboratorio;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReporteService
{
    /**
     * Obtener el conjunto completo de métricas e indicadores para el Dashboard.
     */
    public function obtenerIndicadoresDashboard(): array
    {
        $hoyInicio = Carbon::today()->startOfDay();
        $hoyFin = Carbon::today()->endOfDay();
        $ahora = Carbon::now();

        // 1. Laboratorios
        $totalLaboratorios = Laboratorio::where('activo', true)->count();
        $laboratoriosOcupadosIds = ReservaLaboratorio::whereIn('estado', ['aprobada', 'en_curso'])
            ->where('inicio', '<=', $ahora)
            ->where('fin', '>=', $ahora)
            ->pluck('laboratorio_id')
            ->unique();
        $laboratoriosOcupados = $laboratoriosOcupadosIds->count();
        $laboratoriosDisponibles = max(0, $totalLaboratorios - $laboratoriosOcupados);

        // 2. Equipos
        $equiposDisponibles = Equipo::where('estado', 'disponible')->count();
        $equiposPrestados = Equipo::whereIn('estado', ['prestado', 'asignado'])->count();
        $equiposMantenimiento = Equipo::where('estado', 'en mantenimiento')->count();
        $equiposFueraServicio = Equipo::where('estado', 'fuera de servicio')->count();
        $totalEquipos = Equipo::where('estado', '!=', 'baja')->count();

        // 3. Reservas del día
        $reservasHoy = ReservaLaboratorio::whereBetween('inicio', [$hoyInicio, $hoyFin])->count();
        $reservasPendientes = ReservaLaboratorio::where('estado', 'pendiente')->count();

        // 4. Préstamos pendientes y atrasados
        $prestamosPendientes = AsignacionEquipo::whereIn('estado', ['prestado', 'asignado', 'activo'])->count();
        $prestamosAtrasados = AsignacionEquipo::whereIn('estado', ['prestado', 'asignado', 'activo'])
            ->where('fecha_limite', '<', $ahora)
            ->count();

        // 5. Incidencias abiertas
        $incidenciasAbiertas = Incidencia::whereIn('estado', ['abierta', 'en revision', 'en proceso'])->count();
        $incidenciasCriticas = Incidencia::whereIn('estado', ['abierta', 'en revision', 'en proceso'])
            ->whereIn('prioridad', ['alta', 'critica'])
            ->count();

        // 6. Mantenimientos programados
        $mantenimientosProgramados = Mantenimiento::whereIn('estado', ['programado', 'en_proceso'])->count();

        // 7. Licencias próximas a vencer (siguientes 30 días)
        $licenciasPorVencer = Licencia::where('estado', 'activa')
            ->whereNotNull('fecha_vencimiento')
            ->whereBetween('fecha_vencimiento', [$hoyInicio, $ahora->copy()->addDays(30)])
            ->count();

        // 8. Actividad reciente de bitácora
        $actividadReciente = Bitacora::with('usuario')
            ->latest('fecha')
            ->limit(8)
            ->get();

        // 9. Porcentaje global de ocupación de laboratorios
        $porcentajeOcupacion = $totalLaboratorios > 0
            ? round(($laboratoriosOcupados / $totalLaboratorios) * 100, 1)
            : 0;

        return [
            'kpis' => [
                'laboratorios_disponibles' => $laboratoriosDisponibles,
                'laboratorios_ocupados' => $laboratoriosOcupados,
                'total_laboratorios' => $totalLaboratorios,
                'equipos_disponibles' => $equiposDisponibles,
                'equipos_prestados' => $equiposPrestados,
                'equipos_en_mantenimiento' => $equiposMantenimiento,
                'equipos_fuera_servicio' => $equiposFueraServicio,
                'total_equipos' => $totalEquipos,
                'reservas_hoy' => $reservasHoy,
                'reservas_pendientes' => $reservasPendientes,
                'prestamos_pendientes' => $prestamosPendientes,
                'prestamos_atrasados' => $prestamosAtrasados,
                'incidencias_abiertas' => $incidenciasAbiertas,
                'incidencias_criticas' => $incidenciasCriticas,
                'mantenimientos_programados' => $mantenimientosProgramados,
                'licencias_por_vencer' => $licenciasPorVencer,
                'porcentaje_ocupacion' => $porcentajeOcupacion,
            ],
            'distribucion_equipos' => [
                'disponibles' => $equiposDisponibles,
                'prestados' => $equiposPrestados,
                'mantenimiento' => $equiposMantenimiento,
                'fuera_servicio' => $equiposFueraServicio,
            ],
            'actividad_reciente' => $actividadReciente,
        ];
    }

    /**
     * Generar reporte detallado de ocupación de laboratorios en un periodo.
     */
    public function generarReporteOcupacion(Carbon $desde, Carbon $hasta, ?int $laboratorioId = null): array
    {
        $query = ReservaLaboratorio::with(['laboratorio', 'usuario'])
            ->whereIn('estado', ['aprobada', 'completada'])
            ->whereBetween('inicio', [$desde->startOfDay(), $hasta->endOfDay()]);

        if ($laboratorioId) {
            $query->where('laboratorio_id', $laboratorioId);
        }

        $reservas = $query->orderBy('inicio')->get();

        $totalMinutosReservados = 0;
        $porLaboratorio = [];

        foreach ($reservas as $reserva) {
            $duracion = $reserva->inicio->diffInMinutes($reserva->fin);
            $totalMinutosReservados += $duracion;

            $labNombre = $reserva->laboratorio->nombre ?? 'Desconocido';
            if (!isset($porLaboratorio[$labNombre])) {
                $porLaboratorio[$labNombre] = [
                    'total_reservas' => 0,
                    'total_horas' => 0,
                ];
            }

            $porLaboratorio[$labNombre]['total_reservas']++;
            $porLaboratorio[$labNombre]['total_horas'] += round($duracion / 60, 2);
        }

        return [
            'periodo' => [
                'desde' => $desde->toDateString(),
                'hasta' => $hasta->toDateString(),
            ],
            'total_reservas' => $reservas->count(),
            'total_horas_ocupacion' => round($totalMinutosReservados / 60, 2),
            'desglose_por_laboratorio' => $porLaboratorio,
            'reservas' => $reservas,
        ];
    }

    /**
     * Generar reporte detallado de préstamos y devoluciones en un periodo.
     */
    public function generarReportePrestamos(Carbon $desde, Carbon $hasta): array
    {
        $prestamos = AsignacionEquipo::with(['equipo.categoria', 'usuario', 'laboratorio', 'encargado'])
            ->whereBetween('fecha_asignacion', [$desde->startOfDay(), $hasta->endOfDay()])
            ->latest('fecha_asignacion')
            ->get();

        $devueltos = $prestamos->where('estado', 'devuelto')->count();
        $pendientes = $prestamos->whereIn('estado', ['prestado', 'asignado'])->count();

        return [
            'periodo' => [
                'desde' => $desde->toDateString(),
                'hasta' => $hasta->toDateString(),
            ],
            'total_prestamos' => $prestamos->count(),
            'total_devueltos' => $devueltos,
            'total_pendientes' => $pendientes,
            'prestamos' => $prestamos,
        ];
    }

    /**
     * Generar reporte de incidencias técnicas en un periodo.
     */
    public function generarReporteIncidencias(Carbon $desde, Carbon $hasta): array
    {
        $incidencias = Incidencia::with(['equipo', 'laboratorio', 'reportante', 'encargado'])
            ->whereBetween('created_at', [$desde->startOfDay(), $hasta->endOfDay()])
            ->latest()
            ->get();

        $porPrioridad = [
            'critica' => $incidencias->where('prioridad', 'critica')->count(),
            'alta' => $incidencias->where('prioridad', 'alta')->count(),
            'media' => $incidencias->where('prioridad', 'media')->count(),
            'baja' => $incidencias->where('prioridad', 'baja')->count(),
        ];

        $porEstado = [
            'abierta' => $incidencias->where('estado', 'abierta')->count(),
            'en_proceso' => $incidencias->whereIn('estado', ['en revision', 'en proceso'])->count(),
            'resuelta' => $incidencias->where('estado', 'resuelta')->count(),
        ];

        return [
            'periodo' => [
                'desde' => $desde->toDateString(),
                'hasta' => $hasta->toDateString(),
            ],
            'total_incidencias' => $incidencias->count(),
            'desglose_prioridad' => $porPrioridad,
            'desglose_estado' => $porEstado,
            'incidencias' => $incidencias,
        ];
    }

    /**
     * Generar reporte de mantenimientos preventivos y correctivos en un periodo.
     */
    public function generarReporteMantenimientos(Carbon $desde, Carbon $hasta): array
    {
        $mantenimientos = Mantenimiento::with(['equipo', 'laboratorio', 'responsable'])
            ->whereBetween('fecha_programada', [$desde->startOfDay(), $hasta->endOfDay()])
            ->latest('fecha_programada')
            ->get();

        $costoTotal = $mantenimientos->sum('costo');

        return [
            'periodo' => [
                'desde' => $desde->toDateString(),
                'hasta' => $hasta->toDateString(),
            ],
            'total_mantenimientos' => $mantenimientos->count(),
            'preventivos' => $mantenimientos->where('tipo', 'preventivo')->count(),
            'correctivos' => $mantenimientos->where('tipo', 'correctivo')->count(),
            'costo_total' => $costoTotal,
            'mantenimientos' => $mantenimientos,
        ];
    }

    /**
     * Exportar conjunto de datos tabular a formato CSV descargable mediante streaming.
     */
    public function exportarCsv(array $encabezados, array $filas, string $nombreArchivo = 'reporte.csv'): StreamedResponse
    {
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$nombreArchivo}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($encabezados, $filas) {
            $stream = fopen('php://output', 'w');
            // Incluir BOM para apertura limpia en Microsoft Excel (caracteres en español)
            fprintf($stream, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($stream, $encabezados);

            foreach ($filas as $fila) {
                fputcsv($stream, $fila);
            }

            fclose($stream);
        };

        return response()->stream($callback, 200, $headers);
    }
}
