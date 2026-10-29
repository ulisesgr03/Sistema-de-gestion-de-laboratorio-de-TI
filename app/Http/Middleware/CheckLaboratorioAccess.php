<?php

namespace App\Http\Middleware;

use App\Models\Laboratorio;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckLaboratorioAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $requiredPermission ('consulta' o 'administracion')
     */
    public function handle(Request $request, Closure $next, string $requiredPermission = 'consulta'): Response
    {
        if (!Auth::check()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No autenticado.',
                ], Response::HTTP_UNAUTHORIZED);
            }
            return redirect()->guest('/login');
        }

        $user = Auth::user();

        // El Administrador global tiene acceso total a todos los laboratorios
        if ($user->isAdmin()) {
            return $next($request);
        }

        // Obtener el ID del laboratorio desde ruta, consulta o cuerpo
        $laboratorioParam = $request->route('laboratorio')
            ?? $request->route('laboratorio_id')
            ?? $request->input('laboratorio_id');

        $laboratorioId = null;
        if ($laboratorioParam instanceof Laboratorio) {
            $laboratorioId = $laboratorioParam->id;
        } elseif (is_numeric($laboratorioParam)) {
            $laboratorioId = (int) $laboratorioParam;
        }

        if (!$laboratorioId) {
            // Si la ruta no especifica un laboratorio particular, continuar
            return $next($request);
        }

        // Si es Encargado de laboratorio, verificar si tiene acceso asignado
        $acceso = $user->laboratorios()
            ->where('laboratorio_id', $laboratorioId)
            ->first();

        // Si no tiene acceso asignado explícito, verificar reglas de rol
        if (!$acceso) {
            // Si el usuario es Docente o Alumno y solo requiere consulta, verificar si el laboratorio está activo
            if ($requiredPermission === 'consulta' && ($user->isDocente() || $user->isAlumno())) {
                $lab = Laboratorio::find($laboratorioId);
                if ($lab && $lab->activo) {
                    return $next($request);
                }
            }

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No tiene permisos asignados para gestionar o acceder a este laboratorio.',
                ], Response::HTTP_FORBIDDEN);
            }

            abort(Response::HTTP_FORBIDDEN, 'No tiene permisos asignados para este laboratorio.');
        }

        // Si se requiere permiso de administración pero solo tiene consulta
        if ($requiredPermission === 'administracion' && $acceso->pivot->tipo_acceso === 'consulta') {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Requiere permisos administrativos sobre este laboratorio.',
                ], Response::HTTP_FORBIDDEN);
            }

            abort(Response::HTTP_FORBIDDEN, 'Permiso insuficiente sobre este laboratorio.');
        }

        return $next($request);
    }
}
