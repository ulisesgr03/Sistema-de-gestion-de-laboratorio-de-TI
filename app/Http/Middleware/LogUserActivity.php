<?php

namespace App\Http\Middleware;

use App\Models\Bitacora;
use Closure;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class LogUserActivity
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string|null  $modulo
     */
    public function handle(Request $request, Closure $next, ?string $modulo = null): Response
    {
        $response = $next($request);

        // Registrar operaciones importantes de modificación o si se especificó el módulo
        $method = strtoupper($request->method());
        $isMutation = in_array($method, ['POST', 'PUT', 'PATCH', 'DELETE'], true);

        if ($isMutation && Auth::check()) {
            try {
                $user = Auth::user();
                $path = $request->path();
                $resolvedModulo = $modulo ?: $this->resolveModuleFromPath($path);

                // Ocultar campos sensibles de los parámetros registrados
                $safePayload = $request->except(['password', 'password_confirmation', '_token', 'credencial']);

                Bitacora::create([
                    'user_id' => $user->id,
                    'accion' => $method . ' ' . $path,
                    'modulo' => $resolvedModulo,
                    'detalle' => json_encode([
                        'status' => $response->getStatusCode(),
                        'params' => $safePayload,
                    ], JSON_UNESCAPED_UNICODE),
                    'direccion_ip' => $request->ip(),
                    'fecha' => now(),
                ]);
            } catch (Exception $e) {
                // Prevenir que una falla en la bitácora bloquee la respuesta principal
                report($e);
            }
        }

        return $response;
    }

    protected function resolveModuleFromPath(string $path): string
    {
        $segments = explode('/', trim($path, '/'));
        $first = $segments[0] ?? 'general';

        $moduleMap = [
            'laboratorios' => 'Laboratorios',
            'estaciones' => 'Estaciones',
            'equipos' => 'Inventario',
            'reservas' => 'Reservas',
            'prestamos' => 'Préstamos',
            'asignaciones' => 'Préstamos',
            'software' => 'Software y Licencias',
            'licencias' => 'Software y Licencias',
            'incidencias' => 'Incidencias',
            'mantenimientos' => 'Mantenimientos',
            'usuarios' => 'Usuarios y Roles',
            'horarios' => 'Horarios',
            'practicas' => 'Prácticas Académicas',
            'auth' => 'Autenticación',
            'login' => 'Autenticación',
        ];

        return $moduleMap[strtolower($first)] ?? ucfirst($first);
    }
}
