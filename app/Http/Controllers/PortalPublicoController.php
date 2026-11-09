<?php

namespace App\Http\Controllers;

use App\Models\Horario;
use App\Models\Laboratorio;
use App\Models\Software;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PortalPublicoController extends Controller
{
    /**
     * Página principal / inicio del portal público.
     */
    public function inicio(): View
    {
        $laboratoriosActivos = Laboratorio::activos()->count();
        $totalEstaciones = \App\Models\Estacion::count();
        $totalSoftware = Software::count();

        return view('public.inicio', compact(
            'laboratoriosActivos',
            'totalEstaciones',
            'totalSoftware'
        ));
    }

    /**
     * Página de salas y disponibilidad.
     */
    public function salas(): View
    {
        $laboratorios = Laboratorio::with(['estaciones', 'horarios' => function ($q) {
            $q->where('activo', true);
        }])->activos()->get();

        return view('public.salas', compact('laboratorios'));
    }

    /**
     * Página de horarios y prácticas académicas.
     */
    public function horarios(): View
    {
        $dias = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];

        $horarios = Horario::with('laboratorio')
            ->where('activo', true)
            ->orderByRaw("FIELD(dia_semana, 'Lunes','Martes','Miércoles','Jueves','Viernes','Sábado')")
            ->orderBy('hora_inicio')
            ->get()
            ->groupBy('dia_semana');

        return view('public.horarios', compact('horarios', 'dias'));
    }

    /**
     * Catálogo de software disponible.
     */
    public function software(): View
    {
        $softwares = Software::with('instalaciones')->orderBy('nombre')->get();

        return view('public.software', compact('softwares'));
    }

    /**
     * Información de préstamos en ventanilla (pañol).
     */
    public function prestamos(): View
    {
        return view('public.prestamos');
    }

    /**
     * Reglamento oficial del laboratorio.
     */
    public function reglamento(): View
    {
        return view('public.reglamento');
    }

    /**
     * Formulario para reportar averías desde el portal público.
     */
    public function reportarAveria(): View
    {
        $laboratorios = Laboratorio::activos()->orderBy('nombre')->get();
        return view('public.reportar-averia', compact('laboratorios'));
    }

    /**
     * Procesar el reporte de avería enviado por un usuario externo.
     */
    public function storeAveria(Request $request)
    {
        $request->validate([
            'nombre_reportante' => ['required', 'string', 'max:150'],
            'matricula'         => ['nullable', 'string', 'max:20'],
            'laboratorio_id'    => ['required', 'exists:laboratorios,id'],
            'titulo'            => ['required', 'string', 'max:200'],
            'descripcion'       => ['required', 'string', 'max:1000'],
        ], [
            'nombre_reportante.required' => 'Ingresa tu nombre completo.',
            'laboratorio_id.required'    => 'Selecciona el laboratorio donde ocurrió la avería.',
            'titulo.required'            => 'Describe brevemente el problema.',
            'descripcion.required'       => 'Proporciona más detalles sobre la avería.',
        ]);

        // Crear incidencia pública (sin usuario autenticado)
        \App\Models\Incidencia::create([
            'laboratorio_id' => $request->laboratorio_id,
            'titulo'         => $request->titulo,
            'descripcion'    => $request->descripcion . "\n\n[Reportante: {$request->nombre_reportante}" . ($request->matricula ? " / Matrícula: {$request->matricula}" : '') . "]",
            'prioridad'      => 'media',
            'estado'         => 'pendiente',
            'reportado_por'  => null,
        ]);

        return redirect('/reportar-averia')->with('success', 'Tu reporte fue enviado correctamente. El equipo técnico lo revisará a la brevedad. ¡Gracias por ayudarnos a mantener los laboratorios en óptimas condiciones!');
    }
}
