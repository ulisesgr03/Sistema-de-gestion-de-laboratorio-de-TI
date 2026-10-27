<?php

namespace App\Services;

use App\Exceptions\BusinessRuleException;
use App\Models\AsignacionEquipo;
use App\Models\Equipo;
use App\Models\Incidencia;
use App\Models\Laboratorio;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class PrestamoService
{
    public function __construct(
        protected BitacoraService $bitacoraService,
        protected EquipoService $equipoService
    ) {}

    /**
     * Registrar un nuevo préstamo de equipo.
     */
    public function prestarEquipo(array $datos, int $asignadoPor): AsignacionEquipo
    {
        $userId = (int) $datos['user_id'];
        $equipoId = (int) $datos['equipo_id'];
        $usuario = User::findOrFail($userId);
        $equipo = Equipo::findOrFail($equipoId);

        // 1. Validar que el usuario esté activo
        if (!$usuario->isActive()) {
            throw new BusinessRuleException(
                "El usuario solicitante '{$usuario->name}' se encuentra inactivo.",
                'USUARIO_INACTIVO'
            );
        }

        // 2. Regla de negocio: Límite de 1 préstamo individual activo por persona
        $autorizacionEspecial = !empty($datos['autorizacion_especial']);
        if (!$autorizacionEspecial) {
            $prestamosActivos = AsignacionEquipo::where('user_id', $userId)
                ->whereIn('estado', ['asignado', 'prestado', 'activo'])
                ->count();

            if ($prestamosActivos >= 1) {
                throw new BusinessRuleException(
                    "El usuario ya cuenta con un préstamo activo ({$prestamosActivos}). No se permite más de un préstamo individual simultáneo sin autorización especial.",
                    'PRESTAMO_LIMITE_ALCANZADO',
                    ['prestamos_activos' => $prestamosActivos]
                );
            }
        }

        // 3. Regla de negocio: El equipo debe estar disponible y operativo
        if (!$equipo->estaDisponible()) {
            throw new BusinessRuleException(
                "El equipo '{$equipo->codigo_inventario}' no está disponible para préstamo (Estado actual: '{$equipo->estado}').",
                'EQUIPO_NO_DISPONIBLE'
            );
        }

        // 4. Determinar laboratorio de asignación (del equipo o estación, o indicado)
        $laboratorioId = $datos['laboratorio_id'] ?? null;
        if (!$laboratorioId && $equipo->estacion) {
            $laboratorioId = $equipo->estacion->laboratorio_id;
        }

        if (!$laboratorioId) {
            // Asignar primer laboratorio activo o crear ventanilla general de TI
            $primerLab = Laboratorio::where('activo', true)->first();
            if (!$primerLab) {
                $primerLab = Laboratorio::create([
                    'nombre' => 'Ventanilla General de TI',
                    'ubicacion' => 'Edificio Central',
                    'capacidad' => 10,
                    'activo' => true,
                ]);
            }
            $laboratorioId = $primerLab->id;
        }

        $fechaAsignacion = isset($datos['fecha_asignacion'])
            ? Carbon::parse($datos['fecha_asignacion'])
            : Carbon::now();

        $fechaLimite = isset($datos['fecha_limite'])
            ? Carbon::parse($datos['fecha_limite'])
            : Carbon::now()->addHours(4); // Límite predeterminado: 4 horas o fin de turno

        $prestamo = AsignacionEquipo::create([
            'equipo_id' => $equipo->id,
            'user_id' => $usuario->id,
            'laboratorio_id' => $laboratorioId,
            'asignado_por' => $asignadoPor,
            'fecha_asignacion' => $fechaAsignacion,
            'fecha_limite' => $fechaLimite,
            'estado' => 'prestado',
            'observaciones' => $datos['observaciones'] ?? null,
            'estado_fisico_entrega' => $datos['estado_fisico_entrega'] ?? 'Operativo en condiciones normales.',
        ]);

        // Actualizar estado del equipo a 'prestado'
        $this->equipoService->cambiarEstado(
            $equipo,
            'prestado',
            "Préstamo ID {$prestamo->id} a usuario {$usuario->name} [Matrícula: {$usuario->matricula}]",
            $asignadoPor
        );

        $this->bitacoraService->registrar(
            accion: 'Registrar Préstamo de Equipo',
            modulo: 'Préstamos',
            detalle: "Equipo '{$equipo->codigo_inventario}' prestado a {$usuario->name} hasta {$fechaLimite->format('d/m/Y H:i')}",
            userId: $asignadoPor
        );

        return $prestamo;
    }

    /**
     * Registrar la devolución de un equipo y evaluar su estado físico.
     */
    public function registrarDevolucion(
        int $asignacionId,
        array $datosDevolucion,
        int $recibidoPor
    ): AsignacionEquipo {
        $prestamo = AsignacionEquipo::with(['equipo', 'usuario'])->findOrFail($asignacionId);

        // Regla: No se permitirá registrar una devolución si no existe un préstamo activo asociado
        if (in_array($prestamo->estado, ['devuelto', 'cancelado'], true)) {
            throw new BusinessRuleException(
                "El préstamo ID {$asignacionId} ya fue devuelto o finalizado previamente.",
                'PRESTAMO_YA_DEVUELTO'
            );
        }

        $fechaDevolucion = Carbon::now();
        $equipo = $prestamo->equipo;
        $tieneDanio = !empty($datosDevolucion['presenta_danos']);
        $estadoFisicoDevolucion = $datosDevolucion['estado_fisico_devolucion'] ?? 'Devuelto en buenas condiciones.';
        $observaciones = $datosDevolucion['observaciones'] ?? null;

        $prestamo->fecha_devolucion = $fechaDevolucion;
        $prestamo->estado = 'devuelto';
        $prestamo->estado_fisico_devolucion = $estadoFisicoDevolucion;
        if ($observaciones) {
            $prestamo->observaciones = trim(($prestamo->observaciones ? $prestamo->observaciones . " | " : "") . "[Devolución] " . $observaciones);
        }
        $prestamo->save();

        // Si se reportan daños, pérdidas o faltantes:
        if ($tieneDanio) {
            $nuevoEstadoEquipo = $datosDevolucion['estado_equipo_post_devolucion'] ?? 'fuera de servicio';

            // Actualizar estado del equipo a fuera de servicio o mantenimiento
            $this->equipoService->cambiarEstado(
                $equipo,
                $nuevoEstadoEquipo,
                "Daño reportado al devolver: {$estadoFisicoDevolucion}",
                $recibidoPor
            );

            // Generar automáticamente ticket de incidencia
            Incidencia::create([
                'equipo_id' => $equipo->id,
                'laboratorio_id' => $prestamo->laboratorio_id,
                'reportado_por' => $recibidoPor,
                'asignado_a' => null,
                'titulo' => "Daño detectado al recibir devolución de equipo {$equipo->codigo_inventario}",
                'descripcion' => "Devolución registrada por {$prestamo->usuario->name}. Reporte físico: {$estadoFisicoDevolucion}. " . ($observaciones ? "Notas: {$observaciones}" : ""),
                'prioridad' => 'alta',
                'estado' => 'abierta',
            ]);

            $this->bitacoraService->registrar(
                accion: 'Devolución con Daños / Incidencia',
                modulo: 'Préstamos',
                detalle: "Equipo '{$equipo->codigo_inventario}' devuelto con daños por {$prestamo->usuario->name}. Se generó incidencia y equipo marcado '{$nuevoEstadoEquipo}'.",
                userId: $recibidoPor
            );
        } else {
            // Si está en condiciones normales, liberar y marcar disponible
            $this->equipoService->cambiarEstado(
                $equipo,
                'disponible',
                "Devolución normal de préstamo ID {$prestamo->id}",
                $recibidoPor
            );

            $this->bitacoraService->registrar(
                accion: 'Devolución Normal de Equipo',
                modulo: 'Préstamos',
                detalle: "Equipo '{$equipo->codigo_inventario}' devuelto exitosamente por {$prestamo->usuario->name}.",
                userId: $recibidoPor
            );
        }

        return $prestamo;
    }

    /**
     * Obtener préstamos activos con filtros.
     */
    public function obtenerPrestamosActivos(array $filtros = []): Collection
    {
        $query = AsignacionEquipo::with(['equipo.categoria', 'usuario', 'laboratorio', 'encargado'])
            ->whereIn('estado', ['prestado', 'asignado', 'activo'])
            ->orderBy('fecha_limite');

        if (!empty($filtros['user_id'])) {
            $query->where('user_id', $filtros['user_id']);
        }

        if (!empty($filtros['laboratorio_id'])) {
            $query->where('laboratorio_id', $filtros['laboratorio_id']);
        }

        return $query->get();
    }

    /**
     * Obtener préstamos con fecha límite vencida (atrasados).
     */
    public function obtenerPrestamosAtrasados(): Collection
    {
        return AsignacionEquipo::with(['equipo.categoria', 'usuario', 'laboratorio'])
            ->whereIn('estado', ['prestado', 'asignado', 'activo'])
            ->where('fecha_limite', '<', Carbon::now())
            ->orderBy('fecha_limite')
            ->get();
    }

    /**
     * Listar todos los préstamos con paginación e historial.
     */
    public function listar(array $filtros = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = AsignacionEquipo::with(['equipo.categoria', 'usuario', 'laboratorio', 'encargado'])
            ->latest('fecha_asignacion');

        if (!empty($filtros['estado'])) {
            $query->where('estado', $filtros['estado']);
        }

        if (!empty($filtros['user_id'])) {
            $query->where('user_id', $filtros['user_id']);
        }

        if (!empty($filtros['equipo_id'])) {
            $query->where('equipo_id', $filtros['equipo_id']);
        }

        if (!empty($filtros['buscar'])) {
            $buscar = $filtros['buscar'];
            $query->where(function ($q) use ($buscar) {
                $q->whereHas('usuario', function ($sub) use ($buscar) {
                    $sub->where('name', 'like', "%{$buscar}%")
                        ->orWhere('email', 'like', "%{$buscar}%")
                        ->orWhere('matricula', 'like', "%{$buscar}%");
                })->orWhereHas('equipo', function ($sub) use ($buscar) {
                    $sub->where('codigo_inventario', 'like', "%{$buscar}%")
                        ->orWhere('nombre', 'like', "%{$buscar}%")
                        ->orWhere('numero_serie', 'like', "%{$buscar}%");
                });
            });
        }

        return $query->paginate($perPage);
    }
}
