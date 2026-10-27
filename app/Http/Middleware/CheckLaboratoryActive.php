<?php

namespace App\Http\Middleware;

use App\Models\Laboratorio;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckLaboratoryActive
{
    /**
     * Verifica que el laboratorio objetivo se encuentre activo en el sistema.
     * Previene operaciones de reservas y prácticas en salas inactivas o bajo mantenimiento.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $laboratorio = $this->resolveLaboratorio($request);

        // Si no hay un laboratorio específico en la ruta, continuar
        if (!$laboratorio) {
            return $next($request);
        }

        // Si el laboratorio está activo, permitir acceso sin restricciones
        if ($laboratorio->activo) {
            return $next($request);
        }

        $user = $request->user();

        // El personal técnico y administradores pueden ingresar a salas inactivas para labores de mantenimiento
        if ($user && ($user->isAdmin() || $user->isTechnician())) {
            // Se puede adjuntar un aviso informativo en la sesión web
            if (!$request->expectsJson() && !session()->has('lab_warning_shown')) {
                session()->flash('warning', "Aviso de Mantenimiento: {$laboratorio->nombre} se encuentra marcado como inactivo para el público.");
            }
            return $next($request);
        }

        // Para alumnos, docentes y público, denegar el acceso a laboratorios inactivos
        $mensaje = "El {$laboratorio->nombre} se encuentra temporalmente fuera de servicio o en mantenimiento institucional programado.";

        if ($request->expectsJson()) {
            return response()->json([
                'error' => 'Laboratorio Fuera de Servicio',
                'laboratorio_id' => $laboratorio->id,
                'nombre' => $laboratorio->nombre,
                'activo' => false,
                'message' => $mensaje,
            ], Response::HTTP_LOCKED); // 423 Locked
        }

        abort(Response::HTTP_LOCKED, $mensaje);
    }

    /**
     * Resuelve el modelo de Laboratorio a partir de la petición.
     */
    protected function resolveLaboratorio(Request $request): ?Laboratorio
    {
        $routeParam = $request->route('laboratorio') 
                   ?? $request->route('laboratorio_id') 
                   ?? $request->route('lab');

        if ($routeParam instanceof Laboratorio) {
            return $routeParam;
        }

        if (is_numeric($routeParam)) {
            return Laboratorio::find((int) $routeParam);
        }

        $inputParam = $request->input('laboratorio_id') ?? $request->header('X-Laboratorio-ID');
        if (!empty($inputParam) && is_numeric($inputParam)) {
            return Laboratorio::find((int) $inputParam);
        }

        return null;
    }
}
