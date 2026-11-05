<?php

namespace App\Http\Controllers;

use App\Models\Equipo;
use App\Models\Estacion;
use App\Models\Incidencia;
use App\Models\Laboratorio;
use App\Models\AsignacionEquipo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PanelOperativoController extends Controller
{
    /**
     * Muestra la consola del Panel Operativo de Laboratorios.
     */
    public function index(Request $request): View
    {
        $laboratorios = Laboratorio::where('activo', true)
            ->withCount(['estaciones'])
            ->orderBy('nombre')
            ->get();

        $totalEstaciones = Estacion::count();
        $estacionesActivas = Estacion::where('estado', 'activa')->count();
        $estacionesMantenimiento = Estacion::where('estado', 'mantenimiento')->count();

        $valesPrestamoActivos = AsignacionEquipo::where('estado', 'activa')->count();
        $ticketsPendientes = Incidencia::where('estado', 'abierta')->count();

        return view('private.panel', compact(
            'laboratorios',
            'totalEstaciones',
            'estacionesActivas',
            'estacionesMantenimiento',
            'valesPrestamoActivos',
            'ticketsPendientes'
        ));
    }
}
