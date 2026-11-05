<?php

namespace App\Http\Controllers;

use App\Models\Laboratorio;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LaboratorioController extends Controller
{
    /**
     * Muestra el catálogo de laboratorios activos.
     */
    public function index(Request $request): View|JsonResponse
    {
        $laboratorios = Laboratorio::where('activo', true)
            ->with(['estaciones'])
            ->orderBy('nombre')
            ->get();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'data' => $laboratorios,
            ]);
        }

        return view('public.salas', compact('laboratorios'));
    }

    /**
     * Muestra el detalle específico de un laboratorio.
     */
    public function show(string|int $id): View|JsonResponse
    {
        $laboratorio = Laboratorio::with(['estaciones', 'horarios'])->findOrFail($id);

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'data' => $laboratorio,
            ]);
        }

        return view('public.salas', ['laboratorios' => collect([$laboratorio])]);
    }
}
