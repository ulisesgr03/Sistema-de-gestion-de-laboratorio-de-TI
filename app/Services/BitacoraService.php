<?php

namespace App\Services;

use App\Models\Bitacora;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;

class BitacoraService
{
    /**
     * Registra un evento de auditoría en la bitácora institucional.
     */
    public function registrar(
        string $accion,
        string $modulo,
        string|array|null $detalle = null,
        ?int $userId = null,
        ?string $ip = null
    ): Bitacora {
        try {
            $detalleJson = is_array($detalle)
                ? json_encode($detalle, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
                : $detalle;

            return Bitacora::create([
                'user_id'      => $userId ?? Auth::id(),
                'accion'       => substr($accion, 0, 150),
                'modulo'       => substr($modulo, 0, 100),
                'detalle'      => $detalleJson,
                'direccion_ip' => $ip ?? Request::ip(),
                'fecha'        => now(),
            ]);
        } catch (\Throwable $e) {
            Log::error('Fallo en BitacoraService al registrar evento: ' . $e->getMessage(), [
                'accion' => $accion,
                'modulo' => $modulo,
            ]);

            // Devolver modelo no persistido para evitar romper la transacción principal
            $bitacora = new Bitacora([
                'user_id' => $userId ?? Auth::id(),
                'accion' => $accion,
                'modulo' => $modulo,
                'fecha' => now(),
            ]);

            return $bitacora;
        }
    }

    /**
     * Obtiene el listado de registros con filtros avanzados.
     */
    public function consultar(
        ?string $modulo = null,
        ?int $userId = null,
        ?string $fechaInicio = null,
        ?string $fechaFin = null,
        int $perPage = 25
    ): LengthAwarePaginator {
        $query = Bitacora::with('usuario')->latest('fecha');

        if ($modulo) {
            $query->where('modulo', $modulo);
        }

        if ($userId) {
            $query->where('user_id', $userId);
        }

        if ($fechaInicio) {
            $query->whereDate('fecha', '>=', $fechaInicio);
        }

        if ($fechaFin) {
            $query->whereDate('fecha', '<=', $fechaFin);
        }

        return $query->paginate($perPage);
    }

    /**
     * Obtiene los últimos eventos registrados para el widget del panel operativo.
     */
    public function obtenerActividadReciente(int $limite = 10): Collection
    {
        return Bitacora::with('usuario')
            ->latest('fecha')
            ->limit($limite)
            ->get();
    }
}
