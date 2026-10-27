<?php

namespace App\Services;

use App\Exceptions\BusinessRuleException;
use App\Models\Laboratorio;
use App\Models\ReservaLaboratorio;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class ReservaService
{
    // Horas mínimas de anticipación requeridas por política institucional
    protected int $horasAnticipacionMinima = 24;

    // Duración máxima permitida en horas por reserva individual
    protected int $maximoHorasPorReserva = 4;

    public function __construct(
        protected BitacoraService $bitacoraService
    ) {}

    /**
     * Solicitar una nueva reserva de laboratorio.
     */
    public function solicitar(array $datos, User $usuario): ReservaLaboratorio
    {
        // 1. Validar estado activo del usuario
        if (!$usuario->isActive()) {
            throw new BusinessRuleException(
                "El usuario solicitante se encuentra inactivo y no puede generar reservas.",
                'USUARIO_INACTIVO'
            );
        }

        $laboratorioId = (int) $datos['laboratorio_id'];
        $laboratorio = Laboratorio::findOrFail($laboratorioId);

        // 2. Validar que el laboratorio esté activo
        if (!$laboratorio->activo) {
            throw new BusinessRuleException(
                "El laboratorio '{$laboratorio->nombre}' se encuentra inactivo y no admite reservas.",
                'LABORATORIO_INACTIVO'
            );
        }

        $inicio = Carbon::parse($datos['inicio']);
        $fin = Carbon::parse($datos['fin']);
        $ahora = Carbon::now();

        // 3. Validar coherencia temporal
        if ($inicio->greaterThanOrEqualTo($fin)) {
            throw new BusinessRuleException(
                "La fecha y hora de inicio debe ser anterior a la hora de fin.",
                'HORARIO_INCOHERENTE'
            );
        }

        if ($inicio->lessThan($ahora)) {
            throw new BusinessRuleException(
                "No se pueden realizar reservas para fechas u horarios pasados.",
                'FECHA_PASADA'
            );
        }

        // 4. Regla de negocio: Anticipación mínima de 24 horas (Admin puede omitir esta restricción si se indica 'ignorar_anticipacion')
        $ignorarAnticipacion = !empty($datos['ignorar_anticipacion']) && $usuario->isAdmin();
        if (!$ignorarAnticipacion) {
            $horasDiferencia = $ahora->diffInHours($inicio, false);
            if ($horasDiferencia < $this->horasAnticipacionMinima) {
                throw new BusinessRuleException(
                    "Las reservas deben solicitarse con una anticipación mínima de {$this->horasAnticipacionMinima} horas. (Tiempo actual: {$horasDiferencia} horas de anticipación).",
                    'ANTICIPACION_INSUFICIENTE',
                    ['horas_anticipacion' => $horasDiferencia, 'minimo_requerido' => $this->horasAnticipacionMinima]
                );
            }
        }

        // 5. Regla de negocio: Límite máximo de duración por reserva
        $duracionHoras = $inicio->diffInHours($fin);
        if ($duracionHoras > $this->maximoHorasPorReserva && !$usuario->isAdmin() && !$usuario->isDocente()) {
            throw new BusinessRuleException(
                "La duración de la reserva excede el límite máximo permitido de {$this->maximoHorasPorReserva} horas.",
                'DURACION_EXCEDIDA',
                ['duracion_horas' => $duracionHoras, 'limite' => $this->maximoHorasPorReserva]
            );
        }

        // 6. Regla de negocio: No colisiones simultáneas en el mismo laboratorio
        $existeConflicto = ReservaLaboratorio::where('laboratorio_id', $laboratorioId)
            ->whereIn('estado', ['pendiente', 'aprobada', 'en_curso'])
            ->where(function ($query) use ($inicio, $fin) {
                $query->where('inicio', '<', $fin)
                      ->where('fin', '>', $inicio);
            })
            ->exists();

        if ($existeConflicto) {
            throw new BusinessRuleException(
                "El laboratorio '{$laboratorio->nombre}' ya cuenta con una reserva programada o en revisión en ese horario.",
                'CONFLICTO_RESERVA_SIMULTANEA'
            );
        }

        // Docentes y Administradores pueden aprobar automáticamente según rol o quedar en pendiente
        $estadoInicial = ($usuario->isAdmin()) ? 'aprobada' : 'pendiente';

        $reserva = ReservaLaboratorio::create([
            'laboratorio_id' => $laboratorioId,
            'user_id' => $usuario->id,
            'practica_id' => $datos['practica_id'] ?? null,
            'inicio' => $inicio,
            'fin' => $fin,
            'estado' => $estadoInicial,
            'motivo' => $datos['motivo'] ?? null,
        ]);

        $this->bitacoraService->registrar(
            accion: 'Solicitud de Reserva',
            modulo: 'Reservas',
            detalle: "Reserva ID {$reserva->id} solicitada por {$usuario->name} para laboratorio '{$laboratorio->nombre}' [{$inicio->format('d/m/Y H:i')} - {$fin->format('H:i')}]. Estado: {$estadoInicial}",
            userId: $usuario->id
        );

        return $reserva;
    }

    /**
     * Aprobar una reserva pendiente.
     */
    public function aprobar(ReservaLaboratorio $reserva, int $aprobadoPor): ReservaLaboratorio
    {
        if ($reserva->estado !== 'pendiente') {
            throw new BusinessRuleException(
                "Solo se pueden aprobar reservas en estado pendiente (Estado actual: {$reserva->estado}).",
                'ESTADO_RESERVA_INVALIDO'
            );
        }

        // Re-verificar que no haya conflicto surgido mientras estaba pendiente
        $conflicto = ReservaLaboratorio::where('laboratorio_id', $reserva->laboratorio_id)
            ->where('id', '!=', $reserva->id)
            ->whereIn('estado', ['aprobada', 'en_curso'])
            ->where(function ($query) use ($reserva) {
                $query->where('inicio', '<', $reserva->fin)
                      ->where('fin', '>', $reserva->inicio);
            })
            ->exists();

        if ($conflicto) {
            throw new BusinessRuleException(
                "No se puede aprobar la reserva porque existe un horario en conflicto ya aprobado previamente.",
                'CONFLICTO_RESERVA_SIMULTANEA'
            );
        }

        $reserva->estado = 'aprobada';
        $reserva->save();

        $this->bitacoraService->registrar(
            accion: 'Aprobar Reserva',
            modulo: 'Reservas',
            detalle: "Reserva ID {$reserva->id} aprobada por el usuario ID {$aprobadoPor}.",
            userId: $aprobadoPor
        );

        return $reserva;
    }

    /**
     * Rechazar una reserva pendiente con motivo obligatorio.
     */
    public function rechazar(ReservaLaboratorio $reserva, string $motivo, int $rechazadoPor): ReservaLaboratorio
    {
        if ($reserva->estado !== 'pendiente') {
            throw new BusinessRuleException(
                "Solo se pueden rechazar reservas que estén pendientes.",
                'ESTADO_RESERVA_INVALIDO'
            );
        }

        $reserva->estado = 'rechazada';
        $reserva->motivo = trim(($reserva->motivo ? $reserva->motivo . " | " : "") . "[RECHAZADA] " . $motivo);
        $reserva->save();

        $this->bitacoraService->registrar(
            accion: 'Rechazar Reserva',
            modulo: 'Reservas',
            detalle: "Reserva ID {$reserva->id} rechazada por usuario ID {$rechazadoPor}. Motivo: {$motivo}",
            userId: $rechazadoPor
        );

        return $reserva;
    }

    /**
     * Cancelar una reserva existente.
     */
    public function cancelar(ReservaLaboratorio $reserva, string $motivo, int $canceladoPor): ReservaLaboratorio
    {
        if (in_array($reserva->estado, ['cancelada', 'completada', 'rechazada'], true)) {
            throw new BusinessRuleException(
                "No se puede cancelar una reserva que ya se encuentra en estado '{$reserva->estado}'.",
                'ESTADO_RESERVA_FINALIZADO'
            );
        }

        $reserva->estado = 'cancelada';
        $reserva->motivo = trim(($reserva->motivo ? $reserva->motivo . " | " : "") . "[CANCELADA] " . $motivo);
        $reserva->save();

        $this->bitacoraService->registrar(
            accion: 'Cancelar Reserva',
            modulo: 'Reservas',
            detalle: "Reserva ID {$reserva->id} cancelada. Motivo: {$motivo}",
            userId: $canceladoPor
        );

        return $reserva;
    }

    /**
     * Cancelar automáticamente una reserva por inasistencia / tolerancia de llegada excedida.
     */
    public function cancelarPorInasistencia(ReservaLaboratorio $reserva): ReservaLaboratorio
    {
        return $this->cancelar(
            $reserva,
            'Cancelación automática por inasistencia dentro del tiempo de tolerancia institucional.',
            $reserva->user_id
        );
    }

    /**
     * Marcar una reserva como completada al finalizar la sesión.
     */
    public function completar(ReservaLaboratorio $reserva): ReservaLaboratorio
    {
        $reserva->estado = 'completada';
        $reserva->save();

        $this->bitacoraService->registrar(
            accion: 'Completar Reserva',
            modulo: 'Reservas',
            detalle: "Reserva ID {$reserva->id} marcada como completada con éxito."
        );

        return $reserva;
    }

    /**
     * Listar reservas con filtros y paginación.
     */
    public function listar(array $filtros = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = ReservaLaboratorio::with(['laboratorio', 'usuario', 'practica'])
            ->orderByDesc('inicio');

        if (!empty($filtros['estado'])) {
            $query->where('estado', $filtros['estado']);
        }

        if (!empty($filtros['laboratorio_id'])) {
            $query->where('laboratorio_id', $filtros['laboratorio_id']);
        }

        if (!empty($filtros['user_id'])) {
            $query->where('user_id', $filtros['user_id']);
        }

        if (!empty($filtros['fecha_desde'])) {
            $query->where('inicio', '>=', Carbon::parse($filtros['fecha_desde'])->startOfDay());
        }

        if (!empty($filtros['fecha_hasta'])) {
            $query->where('fin', '<=', Carbon::parse($filtros['fecha_hasta'])->endOfDay());
        }

        return $query->paginate($perPage);
    }

    /**
     * Obtener reservas en formato de calendario para un rango dado.
     */
    public function obtenerCalendario(?int $laboratorioId, Carbon $inicio, Carbon $fin): Collection
    {
        $query = ReservaLaboratorio::with(['laboratorio', 'usuario', 'practica'])
            ->whereIn('estado', ['aprobada', 'en_curso', 'pendiente'])
            ->where('inicio', '<', $fin)
            ->where('fin', '>', $inicio);

        if ($laboratorioId) {
            $query->where('laboratorio_id', $laboratorioId);
        }

        return $query->orderBy('inicio')->get();
    }
}
