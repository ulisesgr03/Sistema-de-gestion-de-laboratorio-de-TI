<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Maneja la petición entrante y verifica si el usuario autenticado
     * cuenta con al menos uno de los roles autorizados.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = $request->user();

        // 1. Validar autenticación
        if (!$user) {
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'No autenticado',
                    'message' => 'Debes iniciar sesión para acceder a este módulo.',
                ], Response::HTTP_UNAUTHORIZED);
            }

            return redirect()->guest(route('login', [], false))
                ->with('error', 'Debes iniciar sesión con tu cuenta institucional para continuar.');
        }

        // Si no se especificaron roles, continuar
        if (empty($roles)) {
            return $next($request);
        }

        // 2. Normalizar roles (permitir tanto sintaxis variádica como roles separados por coma)
        $flattenedRoles = [];
        foreach ($roles as $role) {
            foreach (explode(',', $role) as $subRole) {
                $trimmed = trim($subRole);
                if ($trimmed !== '') {
                    $flattenedRoles[] = $trimmed;
                }
            }
        }

        // 3. Verificar si el usuario cuenta con alguno de los roles
        if (!$user->hasRole($flattenedRoles)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'Acceso Denegado',
                    'message' => 'No cuentas con el rol requerido (' . implode(', ', $flattenedRoles) . ') para esta operación.',
                ], Response::HTTP_FORBIDDEN);
            }

            abort(Response::HTTP_FORBIDDEN, 'Acceso Denegado: Esta área está restringida exclusivamente a usuarios con rol ' . implode(' o ', $flattenedRoles) . '.');
        }

        return $next($request);
    }
}
