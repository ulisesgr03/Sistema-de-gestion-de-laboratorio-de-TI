<?php

namespace App\Http\Middleware;

use App\Models\AsignacionEquipo;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckEquipmentLoanEligibility
{
    /**
     * Valida la elegibilidad del usuario para recibir préstamos de hardware didáctico.
     * Verifica que no cuente con adeudos de equipamiento con fecha de entrega vencida.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Identificar el usuario solicitante:
        // Puede ser el usuario autenticado (si solicita él mismo) o el alumno indicado en el formulario de ventanilla
        $solicitanteId = $request->input('user_id') 
                      ?? $request->input('alumno_id') 
                      ?? $request->route('user') 
                      ?? ($request->user() ? $request->user()->id : null);

        if (!$solicitanteId) {
            return $next($request);
        }

        // Si se envió un objeto User o una matrícula/email
        if (!is_numeric($solicitanteId)) {
            $userFound = User::where('email', $solicitanteId)->orWhere('name', $solicitanteId)->first();
            $solicitanteId = $userFound ? $userFound->id : null;
        }

        if (!$solicitanteId) {
            return $next($request);
        }

        // Buscar adeudos activos o asignaciones vencidas
        $adeudosPendientes = AsignacionEquipo::where('user_id', $solicitanteId)
            ->where(function ($query) {
                $query->where('estado', 'vencido')
                      ->orWhere(function ($q) {
                          $q->where('estado', 'asignado')
                            ->whereNotNull('fecha_devolucion')
                            ->where('fecha_devolucion', '<', now());
                      });
            })
            ->with('equipo')
            ->get();

        if ($adeudosPendientes->isNotEmpty()) {
            $totalAdeudos = $adeudosPendientes->count();
            $detallesEquipos = $adeudosPendientes->map(function ($item) {
                return $item->equipo ? $item->equipo->nombre : "Equipo #{$item->equipo_id}";
            })->implode(', ');

            $mensaje = "El solicitante cuenta con {$totalAdeudos} adeudo(s) de equipo vencido(s): [{$detallesEquipos}]. Debe regularizar su situación en ventanilla antes de recibir nuevo hardware.";

            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'Usuario No Elegible para Préstamo',
                    'adeudos_count' => $totalAdeudos,
                    'equipos_adeudados' => $detallesEquipos,
                    'message' => $mensaje,
                ], Response::HTTP_UNPROCESSABLE_ENTITY); // 422
            }

            return redirect()->back()
                ->withInput()
                ->with('error', $mensaje);
        }

        return $next($request);
    }
}
