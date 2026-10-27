<?php

namespace App\Http\Middleware;

use App\Models\Bitacora;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class AuditLog
{
    /**
     * Campos sensibles que nunca deben quedar registrados en el detalle de la bitácora.
     */
    protected array $hiddenFields = [
        'password',
        'password_confirmation',
        '_token',
        'token',
        'secret',
        'key',
        'credencial',
    ];

    /**
     * Registra automáticamente en la tabla `bitacoras` las operaciones relevantes
     * ejecutadas por los usuarios (especialmente peticiones de modificación POST, PUT, DELETE).
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string|null  $modulo  Nombre explícito del módulo (ej. 'Inventario', 'Topología')
     */
    public function handle(Request $request, Closure $next, ?string $modulo = null): Response
    {
        // Ejecutar primero la petición para capturar la respuesta y su código HTTP
        $response = $next($request);

        // Registrar en bitácora si es mutación de datos (POST, PUT, PATCH, DELETE)
        // o si se configuró un módulo explícito en la ruta
        if ($this->shouldLog($request, $modulo)) {
            $this->logAction($request, $response, $modulo);
        }

        return $response;
    }

    /**
     * Determina si la petición debe registrarse en la bitácora institucional.
     */
    protected function shouldLog(Request $request, ?string $modulo): bool
    {
        // Si se indicó un módulo explícito en el middleware, siempre registrar
        if ($modulo !== null) {
            return true;
        }

        // De forma predeterminada registrar operaciones de creación, actualización o eliminación
        return in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'], true);
    }

    /**
     * Inserta el registro en el modelo Bitacora.
     */
    protected function logAction(Request $request, Response $response, ?string $modulo): void
    {
        try {
            $user = $request->user();
            $detectedModulo = $modulo ?? $this->detectModulo($request);
            $action = $request->method() . ' ' . $request->path();

            // Filtrar parámetros sensibles
            $safePayload = $request->except($this->hiddenFields);

            $detalle = json_encode([
                'status_code' => $response->getStatusCode(),
                'route_name'  => $request->route() ? $request->route()->getName() : null,
                'user_agent'  => substr($request->userAgent() ?? '', 0, 150),
                'payload'     => !empty($safePayload) ? $safePayload : null,
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

            Bitacora::create([
                'user_id'      => $user ? $user->id : null,
                'accion'       => substr($action, 0, 150),
                'modulo'       => substr($detectedModulo, 0, 100),
                'detalle'      => $detalle,
                'direccion_ip' => $request->ip(),
                'fecha'        => now(),
            ]);
        } catch (\Throwable $e) {
            // Prevenir que una falla en el logging interrumpa la transacción del usuario
            Log::error('Error registrando auditoría en Bitacora: ' . $e->getMessage(), [
                'exception' => $e,
            ]);
        }
    }

    /**
     * Infiera un nombre legible de módulo a partir de la ruta URL.
     */
    protected function detectModulo(Request $request): string
    {
        $firstSegment = strtolower($request->segment(1) ?? 'general');

        return match ($firstSegment) {
            'panel-operativo', 'admin'      => 'Panel Operativo',
            'malla-topologica', 'estaciones' => 'Malla Topológica',
            'mesa-incidencias', 'incidencias'=> 'Mesa de Incidencias',
            'inventario', 'prestamos'       => 'Inventario y Auditoría',
            'software', 'licencias'         => 'Catálogo de Software',
            'horarios', 'practicas', 'reservas' => 'Horarios y Prácticas',
            'laboratorios'                  => 'Gestión de Laboratorios',
            'auth', 'login', 'logout'       => 'Autenticación',
            default                         => ucfirst($firstSegment),
        };
    }
}
