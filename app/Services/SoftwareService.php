<?php

namespace App\Services;

use App\Exceptions\BusinessRuleException;
use App\Models\Equipo;
use App\Models\InstalacionSoftware;
use App\Models\Licencia;
use App\Models\Software;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class SoftwareService
{
    public function __construct(
        protected BitacoraService $bitacoraService
    ) {}

    /**
     * Registrar un nuevo software en el catálogo institucional.
     */
    public function registrarSoftware(array $datos, ?int $userId = null): Software
    {
        $nombre = trim($datos['nombre']);

        if (Software::where('nombre', $nombre)->exists()) {
            throw new BusinessRuleException(
                "El software '{$nombre}' ya se encuentra registrado en el catálogo.",
                'SOFTWARE_DUPLICADO'
            );
        }

        $software = Software::create([
            'nombre' => $nombre,
            'version' => $datos['version'] ?? null,
            'fabricante' => $datos['fabricante'] ?? null,
            'descripcion' => $datos['descripcion'] ?? null,
        ]);

        $this->bitacoraService->registrar(
            accion: 'Registrar Software',
            modulo: 'Software y Licencias',
            detalle: "Software registrado: {$software->nombre} (Versión: {$software->version})",
            userId: $userId
        );

        return $software;
    }

    /**
     * Actualizar información de un software.
     */
    public function actualizarSoftware(Software $software, array $datos, ?int $userId = null): Software
    {
        if (isset($datos['nombre']) && $datos['nombre'] !== $software->nombre) {
            $existe = Software::where('nombre', trim($datos['nombre']))
                ->where('id', '!=', $software->id)
                ->exists();

            if ($existe) {
                throw new BusinessRuleException(
                    "Ya existe otro software registrado con el nombre '{$datos['nombre']}'.",
                    'SOFTWARE_DUPLICADO'
                );
            }
        }

        $software->update($datos);

        $this->bitacoraService->registrar(
            accion: 'Actualizar Software',
            modulo: 'Software y Licencias',
            detalle: "Software ID {$software->id} actualizado.",
            userId: $userId
        );

        return $software;
    }

    /**
     * Registrar una licencia para un software.
     */
    public function registrarLicencia(Software $software, array $datos, ?int $userId = null): Licencia
    {
        if (!empty($datos['clave_licencia'])) {
            $clave = trim($datos['clave_licencia']);
            if (Licencia::where('clave_licencia', $clave)->exists()) {
                throw new BusinessRuleException(
                    "La clave de licencia ya se encuentra registrada en el sistema.",
                    'LICENCIA_CLAVE_DUPLICADA'
                );
            }
        }

        $licencia = Licencia::create([
            'software_id' => $software->id,
            'tipo' => $datos['tipo'] ?? 'educativa', // educativa, comercial, libre, oem
            'clave_licencia' => $datos['clave_licencia'] ?? null,
            'cantidad_licencias' => (int) ($datos['cantidad_licencias'] ?? 1),
            'fecha_inicio' => $datos['fecha_inicio'] ?? null,
            'fecha_vencimiento' => $datos['fecha_vencimiento'] ?? null,
            'estado' => $datos['estado'] ?? 'activa',
        ]);

        $this->bitacoraService->registrar(
            accion: 'Registrar Licencia',
            modulo: 'Software y Licencias',
            detalle: "Licencia registrada para {$software->nombre}: tipo {$licencia->tipo}, {$licencia->cantidad_licencias} asientos.",
            userId: $userId
        );

        return $licencia;
    }

    /**
     * Instalar software en un equipo de cómputo validando disponibilidad de licencias.
     */
    public function instalarEnEquipo(
        int $equipoId,
        int $softwareId,
        ?int $licenciaId = null,
        ?int $instaladoPor = null,
        ?string $version = null
    ): InstalacionSoftware {
        $equipo = Equipo::findOrFail($equipoId);
        $software = Software::findOrFail($softwareId);

        // Validar que el equipo esté operativo
        if (!$equipo->esOperativo()) {
            throw new BusinessRuleException(
                "No se puede instalar software en el equipo '{$equipo->codigo_inventario}' porque está en mantenimiento o fuera de servicio.",
                'EQUIPO_NO_OPERATIVO'
            );
        }

        // Validar que no esté ya instalado
        $instalacionExistente = InstalacionSoftware::where('equipo_id', $equipoId)
            ->where('software_id', $softwareId)
            ->first();

        if ($instalacionExistente) {
            if ($instalacionExistente->estado === 'instalado') {
                throw new BusinessRuleException(
                    "El software '{$software->nombre}' ya está instalado en el equipo '{$equipo->codigo_inventario}'.",
                    'SOFTWARE_YA_INSTALADO'
                );
            }

            // Si estaba desinstalado previamente, reactivar la instalación
            $instalacionExistente->estado = 'instalado';
            $instalacionExistente->licencia_id = $licenciaId;
            $instalacionExistente->instalado_por = $instaladoPor;
            $instalacionExistente->fecha_instalacion = Carbon::now();
            if ($version) {
                $instalacionExistente->version_instalada = $version;
            }
            $instalacionExistente->save();

            return $instalacionExistente;
        }

        // Si se especificó licencia, validar disponibilidad de asientos y vigencia
        if ($licenciaId) {
            $licencia = Licencia::findOrFail($licenciaId);

            if ($licencia->software_id !== $softwareId) {
                throw new BusinessRuleException(
                    "La licencia seleccionada no corresponde al software '{$software->nombre}'.",
                    'LICENCIA_INCOMPATIBLE'
                );
            }

            if (strtolower($licencia->estado) !== 'activa') {
                throw new BusinessRuleException(
                    "La licencia seleccionada no se encuentra activa (Estado: {$licencia->estado}).",
                    'LICENCIA_INACTIVA'
                );
            }

            if ($licencia->fecha_vencimiento && Carbon::parse($licencia->fecha_vencimiento)->isPast()) {
                throw new BusinessRuleException(
                    "La licencia seleccionada venció el " . Carbon::parse($licencia->fecha_vencimiento)->format('d/m/Y') . ".",
                    'LICENCIA_VENCIDA'
                );
            }

            // Contar instalaciones activas de esta licencia
            $asientosUsados = InstalacionSoftware::where('licencia_id', $licenciaId)
                ->where('estado', 'instalado')
                ->count();

            if ($asientosUsados >= $licencia->cantidad_licencias) {
                throw new BusinessRuleException(
                    "Se ha alcanzado el límite máximo de instalaciones ({$licencia->cantidad_licencias}) para esta licencia.",
                    'LICENCIA_LIMITE_ALCANZADO',
                    [
                        'asientos_totales' => $licencia->cantidad_licencias,
                        'asientos_usados' => $asientosUsados,
                    ]
                );
            }
        }

        $instalacion = InstalacionSoftware::create([
            'equipo_id' => $equipoId,
            'software_id' => $softwareId,
            'licencia_id' => $licenciaId,
            'instalado_por' => $instaladoPor,
            'fecha_instalacion' => Carbon::now(),
            'version_instalada' => $version ?? $software->version,
            'estado' => 'instalado',
        ]);

        $this->bitacoraService->registrar(
            accion: 'Instalar Software',
            modulo: 'Software y Licencias',
            detalle: "Software '{$software->nombre}' instalado en equipo '{$equipo->codigo_inventario}'" . ($licenciaId ? " [Licencia ID: {$licenciaId}]" : ""),
            userId: $instaladoPor
        );

        return $instalacion;
    }

    /**
     * Desinstalar software de un equipo liberando la licencia.
     */
    public function desinstalarDeEquipo(int $equipoId, int $softwareId, ?int $desinstaladoPor = null): bool
    {
        $instalacion = InstalacionSoftware::where('equipo_id', $equipoId)
            ->where('software_id', $softwareId)
            ->firstOrFail();

        $instalacion->delete();

        $this->bitacoraService->registrar(
            accion: 'Desinstalar Software',
            modulo: 'Software y Licencias',
            detalle: "Software ID {$softwareId} desinstalado del equipo ID {$equipoId}.",
            userId: $desinstaladoPor
        );

        return true;
    }

    /**
     * Obtener licencias próximas a vencer en los siguientes $dias días.
     */
    public function obtenerLicenciasPorVencer(int $dias = 30): Collection
    {
        $hoy = Carbon::now()->startOfDay();
        $limite = Carbon::now()->addDays($dias)->endOfDay();

        return Licencia::with('software')
            ->where('estado', 'activa')
            ->whereNotNull('fecha_vencimiento')
            ->whereBetween('fecha_vencimiento', [$hoy, $limite])
            ->orderBy('fecha_vencimiento')
            ->get();
    }

    /**
     * Listar software con conteo de licencias e instalaciones.
     */
    public function listar(array $filtros = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Software::with(['licencias', 'instalaciones.equipo']);

        if (!empty($filtros['buscar'])) {
            $buscar = $filtros['buscar'];
            $query->where(function ($q) use ($buscar) {
                $q->where('nombre', 'like', "%{$buscar}%")
                  ->orWhere('fabricante', 'like', "%{$buscar}%")
                  ->orWhere('version', 'like', "%{$buscar}%");
            });
        }

        return $query->orderBy('nombre')->paginate($perPage);
    }
}
