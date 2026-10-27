<?php

namespace App\Http\Middleware;

use App\Models\Laboratorio;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckLaboratoryAccess
{
    /**
     * Valida que el usuario autenticado tenga permiso de acceso sobre
     * el laboratorio en contexto (vía ruta, parámetro o payload).
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string|null  $tipoAcceso  Tipo de acceso mínimo requerido (ej. 'responsable', 'tecnico', 'consulta')
     */
    public function handle(Request $request, Closure $next, ?string $tipoAcceso = null): Response
    {
        $user = $request->user();

        if (!$user) {
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'No autenticado',
                    'message' => 'Debes iniciar sesión para acceder a este laboratorio.',
                ], Response::HTTP_UNAUTHORIZED);
            }

            return redirect()->guest(route('login', [], false))
                ->with('error', 'Inicia sesión para acceder a la gestión de laboratorios.');
        }

        // Los administradores generales tienen acceso total a todos los laboratorios
        if ($user->isAdmin()) {
            return $next($request);
        }

        // Obtener la entidad o ID del laboratorio desde la ruta o la petición
        $laboratorio = $this->resolveLaboratorio($request);

        if (!$laboratorio) {
            // Si la ruta no especifica un laboratorio particular, verificar que el usuario tenga acceso al menos a uno
            if (!$user->laboratorios()->exists()) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'error' => 'Sin laboratorios asignados',
                        'message' => 'Tu usuario no tiene ningún laboratorio asignado actualmente.',
                    ], Response::HTTP_FORBIDDEN);
                }

                abort(Response::HTTP_FORBIDDEN, 'Acceso Denegado: No cuentas con asignación en ningún laboratorio.');
            }

            return $next($request);
        }

        // Validar acceso del usuario al laboratorio específico y opcionalmente el tipo_acceso
        if (!$user->hasLaboratoryAccess($laboratorio, $tipoAcceso)) {
            $nombreLab = $laboratorio->nombre ?? 'el laboratorio solicitado';
            $mensaje = "No tienes permisos de acceso para operar en {$nombreLab}";

            if ($tipoAcceso !== null) {
                $mensaje .= " con nivel de acceso '{$tipoAcceso}'.";
            } else {
                $mensaje .= '.';
            }

            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'Acceso Denegado a Laboratorio',
                    'message' => $mensaje,
                ], Response::HTTP_FORBIDDEN);
            }

            abort(Response::HTTP_FORBIDDEN, $mensaje);
        }

        return $next($request);
    }

    /**
     * Resuelve el modelo o ID de Laboratorio a partir del contexto HTTP.
     */
    protected function resolveLaboratorio(Request $request): ?Laboratorio
    {
        // 1. Desde el parámetro de ruta de enlace de modelos (Route Model Binding)
        $routeParam = $request->route('laboratorio') 
                   ?? $request->route('laboratorio_id') 
                   ?? $request->route('lab');

        if ($routeParam instanceof Laboratorio) {
            return $routeParam;
        }

        if (is_numeric($routeParam)) {
            return Laboratorio::find((int) $routeParam);
        }

        // 2. Desde el cuerpo de la petición o query string
        $inputParam = $request->input('laboratorio_id') ?? $request->header('X-Laboratorio-ID');
        if (!empty($inputParam) && is_numeric($inputParam)) {
            return Laboratorio::find((int) $inputParam);
        }

        return null;
    }
}
