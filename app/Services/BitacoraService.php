<?php

namespace App\Services;

use App\Models\Bitacora;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class BitacoraService
{
    /**
     * Registrar una acción en la bitácora del sistema.
     */
    public function registrar(
        string $accion,
        string $modulo,
        ?string $detalle = null,
        ?int $userId = null,
        ?string $direccionIp = null
    ): Bitacora {
        $finalUserId = $userId ?? Auth::id();
        $finalIp = $direccionIp ?? Request::ip();

        return Bitacora::create([
            'user_id' => $finalUserId,
            'accion' => $accion,
            'modulo' => $modulo,
            'detalle' => $detalle,
            'direccion_ip' => $finalIp,
            'fecha' => Carbon::now(),
        ]);
    }

    /**
     * Obtener registros de la bitácora con filtros y paginación.
     */
    public function listar(array $filtros = [], int $perPage = 20): LengthAwarePaginator
    {
        $query = Bitacora::with('usuario')->latest('fecha');

        if (!empty($filtros['modulo'])) {
            $query->where('modulo', $filtros['modulo']);
        }

        if (!empty($filtros['user_id'])) {
            $query->where('user_id', $filtros['user_id']);
        }

        if (!empty($filtros['accion'])) {
            $query->where('accion', 'like', '%' . $filtros['accion'] . '%');
        }

        if (!empty($filtros['fecha_desde'])) {
            $query->where('fecha', '>=', Carbon::parse($filtros['fecha_desde'])->startOfDay());
        }

        if (!empty($filtros['fecha_hasta'])) {
            $query->where('fecha', '<=', Carbon::parse($filtros['fecha_hasta'])->endOfDay());
        }

        if (!empty($filtros['buscar'])) {
            $buscar = $filtros['buscar'];
            $query->where(function ($q) use ($buscar) {
                $q->where('accion', 'like', "%{$buscar}%")
                  ->orWhere('modulo', 'like', "%{$buscar}%")
                  ->orWhere('detalle', 'like', "%{$buscar}%");
            });
        }

        return $query->paginate($perPage);
    }

    /**
     * Obtener las actividades recientes globales.
     */
    public function obtenerActividadReciente(int $limit = 10): Collection
    {
        return Bitacora::with('usuario')
            ->latest('fecha')
            ->limit($limit)
            ->get();
    }
}
