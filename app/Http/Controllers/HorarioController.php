<?php

namespace App\Http\Controllers;

use App\Models\Horario;
use App\Models\Laboratorio;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HorarioController extends Controller
{
    /**
     * Lista los horarios de operación y prácticas.
     */
    public function index(Request $request): View|JsonResponse
    {
        $dia = $request->query('dia');
        $labId = $request->query('laboratorio_id');

        $query = Horario::with('laboratorio')->where('activo', true);

        if ($dia) {
            $query->where('dia_semana', $dia);
        }

        if ($labId) {
            $query->where('laboratorio_id', $labId);
        }

        $horarios = $query->orderBy('hora_inicio')->get();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'data' => $horarios,
            ]);
        }

        return redirect()->route('horarios.index');
    }
}
