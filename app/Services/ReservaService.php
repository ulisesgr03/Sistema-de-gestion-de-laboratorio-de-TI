<?php

namespace App\Services;

use App\Models\Horario;
use App\Models\Laboratorio;
use App\Models\Practica;
use App\Models\ReservaLaboratorio;
use DomainException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReservaService
{
    public function __construct(
        protected BitacoraService $bitacoraService
    ) {}

    /**
     * Valida y crea una nueva reserva de laboratorio, verificando conflictos de horario.
     */
    public function crearReserva(array $datos, ?int $userId = null): ReservaLaboratorio
    {
        return DB::transaction(function () use ($datos, $userId) {
            $laboratorioId = (int) $datos['laboratorio_id'];
            $inicio = Carbon::parse($datos['inicio']);
            $fin = Carbon::parse($datos['fin']);

            if ($fin->lessThanOrEqualTo($inicio)) {
                throw new DomainException('La fecha y hora de fin debe ser posterior al inicio de la reserva.');
            }

            $laboratorio = Laboratorio::findOrFail($laboratorioId);
            if (!$laboratorio->activo) {
                throw new DomainException("El laboratorio '{$laboratorio->nombre}' está inactivo y no admite reservas.");
            }

            // Verificar si el horario no se traslapa con otra reserva confirmada
            if (!$this->verificarDisponibilidad($laboratorioId, $inicio, $fin)) {
                throw new DomainException("El intervalo solicitado ({$inicio->format('H:i')} - {$fin->format('H:i')}) presenta conflicto con otra reserva existente en este laboratorio.");
            }

            $reserva = ReservaLaboratorio::create([
                'laboratorio_id' => $laboratorioId,
                'user_id'        => $userId ?? Auth::id(),
                'practica_id'    => $datos['practica_id'] ?? null,
                'inicio'         => $inicio,
                'fin'            => $fin,
                'estado'         => $datos['estado'] ?? 'confirmada',
                'motivo'         => $datos['motivo'] ?? 'Práctica académica o investigación',
            ]);

            $this->bitacoraService->registrar(
                accion: "Nueva reserva #{$reserva->id} en {$laboratorio->nombre}",
                modulo: 'Horarios y Prácticas',
                detalle: [
                    'reserva_id'     => $reserva->id,
                    'laboratorio_id' => $laboratorioId,
                    'inicio'         => $inicio->toDateTimeString(),
                    'fin'            => $fin->toDateTimeString(),
                    'motivo'         => $reserva->motivo,
                ],
                userId: $userId ?? Auth::id()
            );

            return $reserva;
        });
    }

    /**
     * Comprueba si el laboratorio se encuentra desocupado y libre en el rango especificado.
     */
    public function verificarDisponibilidad(
        int $laboratorioId,
        Carbon|string $inicio,
        Carbon|string $fin,
        ?int $excluirReservaId = null
    ): bool {
        $fechaInicio = Carbon::parse($inicio);
        $fechaFin = Carbon::parse($fin);

        $query = ReservaLaboratorio::where('laboratorio_id', $laboratorioId)
            ->whereIn('estado', ['confirmada', 'en_curso', 'pendiente'])
            ->where(function ($q) use ($fechaInicio, $fechaFin) {
                // Condición de solapamiento de intervalos: (A_inicio < B_fin) AND (A_fin > B_inicio)
                $q->where('inicio', '<', $fechaFin)
                  ->where('fin', '>', $fechaInicio);
            });

        if ($excluirReservaId !== null) {
            $query->where('id', '!=', $excluirReservaId);
        }

        return !$query->exists();
    }

    /**
     * Cancela una reserva existente y libera el espacio.
     */
    public function cancelarReserva(int $reservaId, ?string $motivo = null, ?int $operadorId = null): ReservaLaboratorio
    {
        $reserva = ReservaLaboratorio::with('laboratorio')->findOrFail($reservaId);

        $reserva->update([
            'estado' => 'cancelada',
            'motivo' => $motivo ? "{$reserva->motivo} | Cancelación: {$motivo}" : $reserva->motivo,
        ]);

        $this->bitacoraService->registrar(
            accion: "Cancelación de reserva #{$reserva->id} en {$reserva->laboratorio?->nombre}",
            modulo: 'Horarios y Prácticas',
            detalle: [
                'reserva_id' => $reserva->id,
                'motivo'     => $motivo,
            ],
            userId: $operadorId ?? Auth::id()
        );

        return $reserva;
    }

    /**
     * Registra una práctica curricular o libre para un docente o grupo.
     */
    public function crearPractica(array $datos, ?int $docenteId = null): Practica
    {
        return DB::transaction(function () use ($datos, $docenteId) {
            $practica = Practica::create([
                'laboratorio_id' => $datos['laboratorio_id'],
                'user_id'        => $docenteId ?? Auth::id(),
                'nombre'         => $datos['nombre'],
                'descripcion'    => $datos['descripcion'] ?? null,
                'fecha_inicio'   => $datos['fecha_inicio'] ?? now(),
                'fecha_fin'      => $datos['fecha_fin'] ?? null,
                'estado'         => $datos['estado'] ?? 'programada',
            ]);

            $this->bitacoraService->registrar(
                accion: "Alta de práctica: {$practica->nombre}",
                modulo: 'Horarios y Prácticas',
                detalle: [
                    'practica_id'    => $practica->id,
                    'laboratorio_id' => $practica->laboratorio_id,
                ],
                userId: $docenteId ?? Auth::id()
            );

            return $practica;
        });
    }

    /**
     * Obtiene el itinerario del día para un laboratorio.
     */
    public function obtenerAgendaDiaria(int $laboratorioId, Carbon|string $fecha): Collection
    {
        $dia = Carbon::parse($fecha)->toDateString();

        return ReservaLaboratorio::with(['usuario', 'practica'])
            ->where('laboratorio_id', $laboratorioId)
            ->whereDate('inicio', $dia)
            ->where('estado', '!=', 'cancelada')
            ->orderBy('inicio')
            ->get();
    }
}
