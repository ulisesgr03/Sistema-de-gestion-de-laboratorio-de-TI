<?php

namespace App\Http\Controllers;

use App\Models\Equipo;
use App\Models\Incidencia;
use App\Models\Laboratorio;
use App\Models\MovimientoEquipo;
use App\Models\User;
use Illuminate\View\View;

class PanelController extends Controller
{
    /**
     * Panel de control principal para usuarios autenticados.
     */
    public function index(): View
    {
        $totalLaboratorios  = Laboratorio::activos()->count();
        $totalEquipos       = Equipo::count();
        $totalUsuarios      = User::count();
        $incidenciasPendientes = Incidencia::whereIn('estado', ['pendiente', 'en_proceso'])->count();

        $laboratorios = Laboratorio::with(['estaciones', 'horarios' => function ($q) {
            $q->where('activo', true);
        }])->activos()->get();

        $incidenciasRecientes = Incidencia::with(['laboratorio', 'reportante'])
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        return view('private.panel', compact(
            'totalLaboratorios',
            'totalEquipos',
            'totalUsuarios',
            'incidenciasPendientes',
            'laboratorios',
            'incidenciasRecientes'
        ));
    }
}
