<?php

namespace App\Http\Controllers;

use App\Models\Incidencia;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class IncidenciaController extends Controller
{
    /**
     * Muestra las incidencias reportadas (acceso técnico).
     */
    public function index(Request $request): View|JsonResponse
    {
        $incidencias = Incidencia::with(['laboratorio', 'equipo', 'reportante', 'encargado'])
            ->latest()
            ->paginate(15);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'data' => $incidencias,
            ]);
        }

        return redirect()->route('averia.create');
    }

    /**
     * Almacena una incidencia a través de la API o controlador de recursos.
     */
    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'laboratorio_id' => ['required', 'exists:laboratorios,id'],
            'equipo_id' => ['nullable', 'exists:equipos,id'],
            'titulo' => ['required', 'string', 'max:150'],
            'descripcion' => ['required', 'string'],
            'prioridad' => ['required', 'in:baja,media,alta,critica'],
        ]);

        $incidencia = Incidencia::create([
            'laboratorio_id' => $validated['laboratorio_id'],
            'equipo_id' => $validated['equipo_id'] ?? null,
            'reportado_por' => Auth::id() ?? 1,
            'titulo' => $validated['titulo'],
            'descripcion' => $validated['descripcion'],
            'prioridad' => $validated['prioridad'],
            'estado' => 'abierta',
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Incidencia registrada exitosamente.',
                'data' => $incidencia,
            ], 201);
        }

        return redirect()->back()->with('success', 'Incidencia registrada con folio #' . $incidencia->id);
    }
}
