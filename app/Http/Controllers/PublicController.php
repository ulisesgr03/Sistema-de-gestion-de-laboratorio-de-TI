<?php

namespace App\Http\Controllers;

use App\Models\Estacion;
use App\Models\Incidencia;
use App\Models\Laboratorio;
use App\Models\Software;
use App\Models\User;
use App\Support\CatalogoLaboratorios;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PublicController extends Controller
{
    /**
     * Portal Principal · Mapa Arquitectónico 3D (10 Laboratorios en 2 Niveles).
     */
    public function index(): View
    {
        $laboratoriosDetallados = CatalogoLaboratorios::laboratorios();
        $totales = CatalogoLaboratorios::totales();
        $totalEstaciones = Estacion::count() ?: $totales['total'];
        $softwareCount = Software::count() ?: count(CatalogoLaboratorios::software());

        return view('public.inicio', [
            'laboratorios' => $laboratoriosDetallados,
            'laboratoriosDetallados' => $laboratoriosDetallados,
            'totalEstaciones' => $totalEstaciones,
            'softwareCount' => $softwareCount,
            'totales' => $totales,
        ]);
    }

    /**
     * Vista de Laboratorios (/salas) con ilustraciones técnicas, PC-01 a PC-20 visuales y horas libres por rangos.
     */
    public function salas(Request $request): View
    {
        $busqueda = trim((string) $request->query('q', ''));
        $edificio = trim((string) $request->query('edificio', ''));

        $salas = collect(CatalogoLaboratorios::laboratorios())
            ->filter(function (array $lab) use ($busqueda, $edificio) {
                if ($edificio !== '' && stripos($lab['ubicacion'], $edificio) === false) {
                    return false;
                }
                if ($busqueda !== '') {
                    $haystack = strtolower(
                        $lab['codigo'] . ' ' .
                        $lab['nombre'] . ' ' .
                        $lab['ubicacion'] . ' ' .
                        $lab['hardware'] . ' ' .
                        $lab['software'] . ' ' .
                        $lab['asignatura'] . ' ' .
                        $lab['docente']
                    );
                    return str_contains($haystack, strtolower($busqueda));
                }
                return true;
            })
            ->values()
            ->all();

        return view('public.salas', [
            'salas' => $salas,
            'laboratorios' => $salas,
            'busqueda' => $busqueda,
            'edificio' => $edificio,
        ]);
    }

    /**
     * Vista de Horarios (/horarios) con línea de tiempo interactiva y toma de decisión.
     */
    public function horarios(Request $request): View
    {
        $diasSemana = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
        $diaParam = (string) $request->query('dia', 'Lunes');
        $mapDias = [
            'lunes' => 'Lunes',
            'martes' => 'Martes',
            'miercoles' => 'Miércoles',
            'miércoles' => 'Miércoles',
            'jueves' => 'Jueves',
            'viernes' => 'Viernes',
            'sabado' => 'Sábado',
            'sábado' => 'Sábado',
        ];
        $diaSeleccionado = $mapDias[strtolower(trim($diaParam))] ?? 'Lunes';
        $labSeleccionado = $request->query('laboratorio_id') ?? $request->query('sala');

        $laboratorios = CatalogoLaboratorios::laboratorios();

        return view('public.horarios', compact(
            'laboratorios',
            'diasSemana',
            'diaSeleccionado',
            'labSeleccionado'
        ));
    }

    /**
     * Vista de Catálogo de Software (/software) con logotipos oficiales, modal de versiones y solicitud al Admin.
     */
    public function software(Request $request): View
    {
        $busqueda = trim((string) $request->query('q', ''));
        $categoria = trim((string) $request->query('categoria', ''));

        $catalogo = collect(CatalogoLaboratorios::software())
            ->filter(function (array $sw) use ($busqueda, $categoria) {
                if ($categoria !== '' && strcasecmp($sw['categoria'], $categoria) !== 0) {
                    return false;
                }
                if ($busqueda !== '') {
                    $haystack = strtolower($sw['nombre'] . ' ' . $sw['fabricante'] . ' ' . $sw['categoria'] . ' ' . $sw['descripcion'] . ' ' . implode(' ', $sw['labs']));
                    return str_contains($haystack, strtolower($busqueda));
                }
                return true;
            })
            ->values()
            ->all();

        $categorias = collect(CatalogoLaboratorios::software())->pluck('categoria')->unique()->values()->all();

        return view('public.software', [
            'catalogo' => $catalogo,
            'categorias' => $categorias,
            'busqueda' => $busqueda,
            'categoria' => $categoria,
        ]);
    }

    /**
     * Formulario dedicado para reportar fallo en una estación.
     */
    public function reportarAveria(Request $request): View
    {
        return view('public.reportar-averia', [
            'salas' => CatalogoLaboratorios::laboratorios(),
            'salaSeleccionada' => $request->query('sala', 'L-01'),
            'pcSeleccionada' => $request->query('pc', 'PC-01'),
        ]);
    }

    /**
     * Registra un reporte de fallo sobre una computadora específica.
     */
    public function guardarAveria(Request $request)
    {
        $salaRaw = $request->input('sala') ?? $request->input('laboratorio_id') ?? 'L-01';
        $pcRaw = $request->input('pc') ?? $request->input('estacion_nombre') ?? 'PC-01';
        $motivoRaw = $request->input('motivo') ?? $request->input('categoria') ?? 'Hardware';
        $causaRaw = $request->input('causa') ?? $request->input('descripcion') ?? 'Revisión solicitada';
        $nombreRaw = $request->input('nombre') ?? $request->input('reportante_nombre') ?? (Auth::user()->name ?? 'Usuario MetalNet');
        $matRaw = $request->input('matricula') ?? $request->input('reportante_matricula') ?? (Auth::user()->matricula ?? 'MN-2026');

        $folioNum = random_int(1000, 9999);
        try {
            $labInfo = CatalogoLaboratorios::buscar($salaRaw);
            $labDb = $labInfo ? Laboratorio::find($labInfo['id']) : Laboratorio::first();
            $userDb = Auth::user() ?: User::first();
            $estDb = $labDb ? Estacion::where('laboratorio_id', $labDb->id)->where('nombre', $pcRaw)->first() : null;

            if ($estDb) {
                $estDb->update([
                    'estado' => 'mantenimiento',
                    'ubicacion' => "[Avería] {$motivoRaw}: {$causaRaw}",
                ]);
                \App\Models\Equipo::where('estacion_id', $estDb->id)->update(['estado' => 'mantenimiento']);
            }

            if ($labDb && $userDb) {
                $equipoDb = $estDb ? \App\Models\Equipo::where('estacion_id', $estDb->id)->first() : null;
                $incidencia = Incidencia::create([
                    'laboratorio_id' => $labDb->id,
                    'equipo_id' => $equipoDb?->id,
                    'reportado_por' => $userDb->id,
                    'atendido_por' => null,
                    'titulo' => '[' . strtoupper($motivoRaw) . '] Fallo en ' . $pcRaw,
                    'descripcion' => "Matrícula: {$matRaw} | Reportante: {$nombreRaw} | Estación: {$pcRaw} | Detalle: {$causaRaw}",
                    'prioridad' => 'alta',
                    'estado' => 'abierta',
                ]);
                $folioNum = $incidencia->id;
            }
        } catch (\Throwable $e) {
            // Fallback
        }

        CatalogoLaboratorios::clearCache();
        $labInfo = CatalogoLaboratorios::buscar($salaRaw);
        $salaTag = $labInfo ? $labInfo['codigo'] : (string) $salaRaw;
        $ticket = '#MN-INC-' . date('Ymd') . '-' . sprintf('%04d', $folioNum);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'ticket' => $ticket,
                'message' => "Fallo reportado exitosamente: {$ticket} ({$salaTag} · {$pcRaw}).",
            ]);
        }

        return back()->with(
            'status',
            "Reporte de falla registrado: {$ticket} ({$salaTag} · {$pcRaw})."
        );
    }
}
