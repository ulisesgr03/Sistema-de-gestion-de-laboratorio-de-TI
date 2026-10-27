<?php

namespace App\Services;

use App\Models\Equipo;
use App\Models\InstalacionSoftware;
use App\Models\Licencia;
use App\Models\Software;
use DomainException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SoftwareService
{
    public function __construct(
        protected BitacoraService $bitacoraService
    ) {}

    /**
     * Da de alta un nuevo paquete de software en el catálogo institucional.
     */
    public function registrarSoftware(array $datos, ?int $operadorId = null): Software
    {
        return DB::transaction(function () use ($datos, $operadorId) {
            $software = Software::create([
                'nombre'      => $datos['nombre'],
                'version'     => $datos['version'] ?? 'Última estable',
                'fabricante'  => $datos['fabricante'] ?? null,
                'descripcion' => $datos['descripcion'] ?? null,
            ]);

            $this->bitacoraService->registrar(
                accion: "Alta de software: {$software->nombre} ({$software->version})",
                modulo: 'Catálogo de Software',
                detalle: [
                    'software_id' => $software->id,
                    'fabricante'  => $software->fabricante,
                ],
                userId: $operadorId ?? Auth::id()
            );

            return $software;
        });
    }

    /**
     * Asocia una clave o contrato de licenciamiento a un título de software.
     */
    public function registrarLicencia(int $softwareId, array $datos, ?int $operadorId = null): Licencia
    {
        return DB::transaction(function () use ($softwareId, $datos, $operadorId) {
            $software = Software::findOrFail($softwareId);

            $licencia = Licencia::create([
                'software_id'        => $software->id,
                'tipo'               => $datos['tipo'] ?? 'academica',
                'clave_licencia'     => $datos['clave_licencia'] ?? null,
                'cantidad_licencias' => (int) ($datos['cantidad_licencias'] ?? 30),
                'fecha_inicio'       => $datos['fecha_inicio'] ?? now(),
                'fecha_vencimiento'  => $datos['fecha_vencimiento'] ?? null,
                'estado'             => $datos['estado'] ?? 'activa',
            ]);

            $this->bitacoraService->registrar(
                accion: "Registro de licencia para {$software->nombre}",
                modulo: 'Catálogo de Software',
                detalle: [
                    'software_id' => $software->id,
                    'licencia_id' => $licencia->id,
                    'asientos'    => $licencia->cantidad_licencias,
                    'tipo'        => $licencia->tipo,
                ],
                userId: $operadorId ?? Auth::id()
            );

            return $licencia;
        });
    }

    /**
     * Registra la instalación de un software en una terminal o equipo, controlando cupo de licencias.
     */
    public function instalarEnEquipo(
        int $softwareId,
        int $equipoId,
        ?int $licenciaId = null,
        ?int $instaladoPorId = null
    ): InstalacionSoftware {
        return DB::transaction(function () use (
            $softwareId,
            $equipoId,
            $licenciaId,
            $instaladoPorId
        ) {
            $software = Software::findOrFail($softwareId);
            $equipo = Equipo::with('estacion.laboratorio')->findOrFail($equipoId);

            // Verificar si ya está instalado en este equipo
            $instalacionExistente = InstalacionSoftware::where('equipo_id', $equipoId)
                ->where('software_id', $softwareId)
                ->first();

            if ($instalacionExistente) {
                throw new DomainException("El paquete '{$software->nombre}' ya se encuentra instalado en el equipo '{$equipo->codigo_inventario}'.");
            }

            // Si se vincula a una licencia, verificar que esté activa y con asientos disponibles
            if ($licenciaId) {
                $licencia = Licencia::withCount('instalaciones')->lockForUpdate()->findOrFail($licenciaId);

                if ($licencia->estado !== 'activa') {
                    throw new DomainException("La licencia #{$licencia->id} no está activa. Estado actual: {$licencia->estado}.");
                }

                if ($licencia->cantidad_licencias > 0 && $licencia->instalaciones_count >= $licencia->cantidad_licencias) {
                    throw new DomainException("Se ha alcanzado el límite máximo de asientos permitidos ({$licencia->cantidad_licencias}) para esta licencia.");
                }
            }

            $instalacion = InstalacionSoftware::create([
                'equipo_id'          => $equipo->id,
                'software_id'        => $software->id,
                'licencia_id'        => $licenciaId,
                'instalado_por'      => $instaladoPorId ?? Auth::id(),
                'fecha_instalacion'  => now(),
            ]);

            $this->bitacoraService->registrar(
                accion: "Instalación de {$software->nombre} en {$equipo->codigo_inventario}",
                modulo: 'Catálogo de Software',
                detalle: [
                    'software_id'    => $software->id,
                    'equipo_id'      => $equipo->id,
                    'laboratorio_id' => $equipo->estacion?->laboratorio_id,
                    'licencia_id'    => $licenciaId,
                ],
                userId: $instaladoPorId ?? Auth::id()
            );

            return $instalacion;
        });
    }

    /**
     * Desinstala un paquete de una terminal, liberando el asiento de la licencia.
     */
    public function desinstalarDeEquipo(int $instalacionId, ?int $operadorId = null): bool
    {
        return DB::transaction(function () use ($instalacionId, $operadorId) {
            $instalacion = InstalacionSoftware::with(['software', 'equipo'])->findOrFail($instalacionId);

            $softwareNombre = $instalacion->software?->nombre ?? 'Software';
            $equipoCodigo = $instalacion->equipo?->codigo_inventario ?? 'Equipo';

            $instalacion->delete();

            $this->bitacoraService->registrar(
                accion: "Desinstalación de {$softwareNombre} de {$equipoCodigo}",
                modulo: 'Catálogo de Software',
                detalle: [
                    'instalacion_id' => $instalacionId,
                ],
                userId: $operadorId ?? Auth::id()
            );

            return true;
        });
    }

    /**
     * Obtiene el catálogo completo de software con estadísticas de despliegue y licencias.
     */
    public function obtenerCatalogoConDisponibilidad(): Collection
    {
        return Software::with(['licencias', 'instalaciones.equipo.estacion.laboratorio'])
            ->withCount('instalaciones')
            ->get();
    }
}
