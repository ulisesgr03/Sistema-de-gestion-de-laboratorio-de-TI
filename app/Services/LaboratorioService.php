<?php

namespace App\Services;

use App\Exceptions\BusinessRuleException;
use App\Models\Laboratorio;
use App\Models\ReservaLaboratorio;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class LaboratorioService
{
    public function __construct(
        protected BitacoraService $bitacoraService
    ) {}

    /**
     * Crear un nuevo laboratorio.
     */
    public function crear(array $datos): Laboratorio
    {
        $laboratorio = Laboratorio::create([
            'nombre' => trim($datos['nombre']),
            'ubicacion' => trim($datos['ubicacion']),
            'descripcion' => $datos['descripcion'] ?? null,
            'capacidad' => (int) ($datos['capacidad'] ?? 0),
            'activo' => isset($datos['activo']) ? (bool) $datos['activo'] : true,
        ]);

        $this->bitacoraService->registrar(
            accion: 'Crear Laboratorio',
            modulo: 'Laboratorios',
            detalle: "Laboratorio creado: {$laboratorio->nombre} (ID: {$laboratorio->id}, Capacidad: {$laboratorio->capacidad})"
        );

        return $laboratorio;
    }

    /**
     * Actualizar los datos de un laboratorio.
     */
    public function actualizar(Laboratorio $laboratorio, array $datos): Laboratorio
    {
        $cambios = [];
        foreach (['nombre', 'ubicacion', 'descripcion', 'capacidad', 'activo'] as $campo) {
            if (array_key_exists($campo, $datos) && $laboratorio->{$campo} != $datos[$campo]) {
                $cambios[$campo] = [
                    'anterior' => $laboratorio->{$campo},
                    'nuevo' => $datos[$campo],
                ];
            }
        }

        $laboratorio->update($datos);

        if (!empty($cambios)) {
            $this->bitacoraService->registrar(
                accion: 'Actualizar Laboratorio',
                modulo: 'Laboratorios',
                detalle: "Laboratorio ID: {$laboratorio->id} actualizado: " . json_encode($cambios, JSON_UNESCAPED_UNICODE)
            );
        }

        return $laboratorio;
    }

    /**
     * Desactivar lógicamente un laboratorio, verificando que no existan reservas activas.
     */
    public function desactivar(Laboratorio $laboratorio): bool
    {
        // Regla: No desactivar si existen reservas activas o en curso
        $reservasActivas = ReservaLaboratorio::where('laboratorio_id', $laboratorio->id)
            ->whereIn('estado', ['pendiente', 'aprobada', 'en_curso'])
            ->where('fin', '>=', Carbon::now())
            ->count();

        if ($reservasActivas > 0) {
            throw new BusinessRuleException(
                "No se puede desactivar el laboratorio '{$laboratorio->nombre}' porque tiene {$reservasActivas} reserva(s) activas o próximas.",
                'LABORATORIO_RESERVAS_ACTIVAS',
                ['reservas_activas' => $reservasActivas]
            );
        }

        $laboratorio->activo = false;
        $laboratorio->save();

        $this->bitacoraService->registrar(
            accion: 'Desactivar Laboratorio',
            modulo: 'Laboratorios',
            detalle: "Laboratorio ID: {$laboratorio->id} ({$laboratorio->nombre}) desactivado lógicamente."
        );

        return true;
    }

    /**
     * Reactivar un laboratorio.
     */
    public function activar(Laboratorio $laboratorio): bool
    {
        $laboratorio->activo = true;
        $laboratorio->save();

        $this->bitacoraService->registrar(
            accion: 'Activar Laboratorio',
            modulo: 'Laboratorios',
            detalle: "Laboratorio ID: {$laboratorio->id} ({$laboratorio->nombre}) reactivado."
        );

        return true;
    }

    /**
     * Listar laboratorios con filtros y paginación.
     */
    public function listar(array $filtros = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Laboratorio::withCount(['estaciones', 'reservas', 'incidencias']);

        if (isset($filtros['activo']) && $filtros['activo'] !== '') {
            $query->where('activo', filter_var($filtros['activo'], FILTER_VALIDATE_BOOLEAN));
        }

        if (!empty($filtros['ubicacion'])) {
            $query->where('ubicacion', 'like', '%' . $filtros['ubicacion'] . '%');
        }

        if (!empty($filtros['capacidad_minima'])) {
            $query->where('capacidad', '>=', (int) $filtros['capacidad_minima']);
        }

        if (!empty($filtros['buscar'])) {
            $buscar = $filtros['buscar'];
            $query->where(function ($q) use ($buscar) {
                $q->where('nombre', 'like', "%{$buscar}%")
                  ->orWhere('ubicacion', 'like', "%{$buscar}%")
                  ->orWhere('descripcion', 'like', "%{$buscar}%");
            });
        }

        return $query->orderBy('nombre')->paginate($perPage);
    }

    /**
     * Obtener un laboratorio con sus relaciones clave.
     */
    public function obtenerConDetalles(int $id): Laboratorio
    {
        return Laboratorio::with([
            'estaciones.equipos.categoria',
            'horarios',
            'usuarios',
            'incidencias' => fn($q) => $q->whereIn('estado', ['abierta', 'en_revision', 'en_proceso']),
        ])->findOrFail($id);
    }

    /**
     * Asignar usuario/encargado a un laboratorio.
     */
    public function asignarUsuario(Laboratorio $laboratorio, int $userId, string $tipoAcceso = 'consulta'): void
    {
        $laboratorio->usuarios()->syncWithoutDetaching([
            $userId => ['tipo_acceso' => $tipoAcceso],
        ]);

        $this->bitacoraService->registrar(
            accion: 'Asignar Usuario a Laboratorio',
            modulo: 'Laboratorios',
            detalle: "Usuario ID {$userId} asignado a laboratorio ID {$laboratorio->id} con acceso {$tipoAcceso}."
        );
    }

    /**
     * Remover usuario de un laboratorio.
     */
    public function removerUsuario(Laboratorio $laboratorio, int $userId): void
    {
        $laboratorio->usuarios()->detach($userId);

        $this->bitacoraService->registrar(
            accion: 'Remover Usuario de Laboratorio',
            modulo: 'Laboratorios',
            detalle: "Usuario ID {$userId} removido de laboratorio ID {$laboratorio->id}."
        );
    }

    /**
     * Verificar si el laboratorio está disponible en el rango de fechas.
     */
    public function verificarDisponibilidad(
        Laboratorio $laboratorio,
        Carbon $inicio,
        Carbon $fin,
        ?int $reservaIdExcluir = null
    ): bool {
        if (!$laboratorio->activo) {
            return false;
        }

        $query = ReservaLaboratorio::where('laboratorio_id', $laboratorio->id)
            ->whereIn('estado', ['pendiente', 'aprobada', 'en_curso'])
            ->where(function ($q) use ($inicio, $fin) {
                $q->where(function ($sub) use ($inicio, $fin) {
                    $sub->where('inicio', '<', $fin)
                        ->where('fin', '>', $inicio);
                });
            });

        if ($reservaIdExcluir) {
            $query->where('id', '!=', $reservaIdExcluir);
        }

        return $query->count() === 0;
    }
}
