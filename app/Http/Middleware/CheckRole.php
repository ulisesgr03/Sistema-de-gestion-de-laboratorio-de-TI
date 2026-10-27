<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!Auth::check()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No autenticado. Por favor inicie sesión.',
                ], Response::HTTP_UNAUTHORIZED);
            }

            return redirect()->guest('/login');
        }

        $user = Auth::user();

        // Verificar si la cuenta de usuario se encuentra activa
        if (isset($user->activo) && !$user->activo) {
            Auth::logout();
            if ($request->hasSession()) {
                $request->session()->invalidate();
                $request->session()->regenerateToken();
            }

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Su cuenta se encuentra desactivada. Contacte al administrador.',
                ], Response::HTTP_FORBIDDEN);
            }

            return redirect('/login')->withErrors([
                'email' => 'Su cuenta se encuentra desactivada. Contacte al administrador.',
            ]);
        }

        // Si no se pasaron roles específicos, permitir acceso a usuarios autenticados
        if (empty($roles)) {
            return $next($request);
        }

        // Verificar si el usuario tiene alguno de los roles solicitados
        if (!$user->hasRole($roles)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Acceso denegado: no cuenta con los permisos o rol requeridos para esta acción.',
                    'required_roles' => $roles,
                ], Response::HTTP_FORBIDDEN);
            }

            abort(Response::HTTP_FORBIDDEN, 'Acceso denegado: no cuenta con el rol requerido.');
        }

        return $next($request);
    }
}
