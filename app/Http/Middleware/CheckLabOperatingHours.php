<?php

namespace App\Http\Middleware;

use App\Models\Horario;
use App\Models\Laboratorio;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckLabOperatingHours
{
    /**
     * Valida que la solicitud de práctica o reserva concurrente ocurra
     * dentro del horario operativo activo del laboratorio.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Administradores y técnicos pueden operar y registrar prácticas en cualquier horario
        if ($user && ($user->isAdmin() || $user->isTechnician())) {
            return $next($request);
        }

        $laboratorio = $this->resolveLaboratorio($request);

        if (!$laboratorio) {
            return $next($request);
        }

        $ahora = now();
        $diaSemana = (int) $ahora->dayOfWeekIso; // 1 (Lunes) a 7 (Domingo)
        $horaActual = $ahora->format('H:i:s');

        // Buscar si existe un horario operativo activo para este día
        $horarioValido = Horario::where('laboratorio_id', $laboratorio->id)
            ->where('activo', true)
            ->where(function ($q) use ($diaSemana) {
                // Acomodar tanto ISO (1-7) como estándar 0-6
                $q->where('dia_semana', $diaSemana)
                  ->orWhere('dia_semana', $diaSemana % 7);
            })
            ->where('hora_inicio', '<=', $horaActual)
            ->where('hora_fin', '>=', $horaActual)
            ->exists();

        // Si existen horarios configurados para la sala y la hora actual está fuera de ellos
        $tieneHorariosConfigurados = Horario::where('laboratorio_id', $laboratorio->id)
            ->where('activo', true)
            ->exists();

        if ($tieneHorariosConfigurados && !$horarioValido) {
            $mensaje = "El {$laboratorio->nombre} se encuentra fuera de su horario de atención operativa para prácticas abiertas en este momento.";

            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'Fuera de Horario Operativo',
                    'laboratorio_id' => $laboratorio->id,
                    'hora_actual' => $horaActual,
                    'dia_semana' => $diaSemana,
                    'message' => $mensaje,
                ], Response::HTTP_FORBIDDEN);
            }

            return redirect()->back()
                ->with('error', $mensaje);
        }

        return $next($request);
    }

    /**
     * Resuelve el modelo de Laboratorio de la petición.
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
