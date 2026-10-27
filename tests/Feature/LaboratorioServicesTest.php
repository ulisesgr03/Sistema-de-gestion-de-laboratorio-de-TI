<?php

namespace Tests\Feature;

use App\Models\AsignacionEquipo;
use App\Models\Bitacora;
use App\Models\Equipo;
use App\Models\Estacion;
use App\Models\Laboratorio;
use App\Models\Licencia;
use App\Models\Rol;
use App\Models\Software;
use App\Models\User;
use App\Services\BitacoraService;
use App\Services\IncidenciaService;
use App\Services\InventarioService;
use App\Services\LaboratorioService;
use App\Services\ReservaService;
use App\Services\SoftwareService;
use DomainException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LaboratorioServicesTest extends TestCase
{
    use RefreshDatabase;

    public function test_inventario_service_flow(): void
    {
        $inventarioService = app(InventarioService::class);

        $tecnico = User::factory()->create(['name' => 'Carlos Mendoza']);
        $alumno = User::factory()->create(['name' => 'Brenda López']);
        $laboratorio = Laboratorio::create([
            'nombre'    => 'Laboratorio L-04',
            'ubicacion' => 'Edificio E',
            'capacidad' => 30,
            'activo'    => true,
        ]);

        // 1. Alta de equipo
        $equipo = $inventarioService->crearEquipo([
            'codigo_inventario' => 'UPT-SW-01',
            'nombre'            => 'Switch Cisco 2960',
            'marca'             => 'Cisco',
            'modelo'            => 'WS-C2960',
            'numero_serie'      => 'FOC1823W0AB',
            'estado'            => 'disponible',
        ], $tecnico->id);

        $this->assertEquals('disponible', $equipo->estado);
        $this->assertDatabaseHas('equipos', ['codigo_inventario' => 'UPT-SW-01']);

        // 2. Préstamo en ventanilla
        $asignacion = $inventarioService->registrarPrestamo(
            equipoId: $equipo->id,
            solicitanteId: $alumno->id,
            laboratorioId: $laboratorio->id,
            asignadoPorId: $tecnico->id
        );

        $this->assertEquals('asignado', $asignacion->estado);
        $this->assertEquals('prestado', $equipo->fresh()->estado);

        // 3. Devolución de equipo
        $devolucion = $inventarioService->registrarDevolucion(
            asignacionId: $asignacion->id,
            estadoFisico: 'bueno',
            operadorId: $tecnico->id
        );

        $this->assertEquals('devuelto', $devolucion->estado);
        $this->assertEquals('disponible', $equipo->fresh()->estado);

        // 4. Estadísticas
        $stats = $inventarioService->obtenerEstadisticasParqueComputacional();
        $this->assertEquals(1, $stats['total_equipos']);
        $this->assertEquals(1, $stats['disponibles_inmediatos']);
    }

    public function test_laboratorio_service_flow(): void
    {
        $laboratorioService = app(LaboratorioService::class);
        $tecnico = User::factory()->create();

        $laboratorio = Laboratorio::create([
            'nombre'    => 'Laboratorio L-01',
            'ubicacion' => 'Edificio E',
            'capacidad' => 30,
            'activo'    => true,
        ]);

        $e1 = Estacion::create(['laboratorio_id' => $laboratorio->id, 'nombre' => 'PC-01', 'estado' => 'libre']);
        $e2 = Estacion::create(['laboratorio_id' => $laboratorio->id, 'nombre' => 'PC-02', 'estado' => 'libre']);

        // 1. Bloqueo masivo
        $afectadas = $laboratorioService->bloquearTerminales($laboratorio->id, [], 'Fin de clase', $tecnico->id);
        $this->assertEquals(2, $afectadas);
        $this->assertEquals('bloqueada', $e1->fresh()->estado);
        $this->assertEquals('bloqueada', $e2->fresh()->estado);

        // 2. Desbloqueo
        $desbloqueadas = $laboratorioService->desbloquearTerminales($laboratorio->id, [], $tecnico->id);
        $this->assertEquals(2, $desbloqueadas);
        $this->assertEquals('libre', $e1->fresh()->estado);

        // 3. Malla topológica
        $malla = $laboratorioService->obtenerMallaTopologica($laboratorio->id);
        $this->assertCount(2, $malla['estaciones']);
        $this->assertEquals(2, $malla['conteo']['libres']);
    }

    public function test_incidencia_service_lifecycle(): void
    {
        $incidenciaService = app(IncidenciaService::class);
        $alumno = User::factory()->create();
        $tecnico = User::factory()->create();

        $laboratorio = Laboratorio::create(['nombre' => 'L-02', 'ubicacion' => 'E', 'capacidad' => 20]);
        $categoria = \App\Models\CategoriaEquipo::firstOrCreate(['nombre' => 'Cómputo']);
        $equipo = Equipo::create([
            'categoria_equipo_id' => $categoria->id,
            'codigo_inventario'   => 'UPT-PC-02',
            'nombre'              => 'Dell OptiPlex 7090',
            'estado'              => 'disponible',
        ]);

        // 1. Reportar avería (el equipo pasa automáticamente a mantenimiento)
        $incidencia = $incidenciaService->reportarIncidencia([
            'laboratorio_id' => $laboratorio->id,
            'equipo_id'      => $equipo->id,
            'titulo'         => 'Monitor parpadea y sin señal HDMI',
            'prioridad'      => 'alta',
        ], $alumno->id);

        $this->assertEquals('reportada', $incidencia->estado);
        $this->assertEquals('mantenimiento', $equipo->fresh()->estado);

        // 2. Asignar técnico
        $incidenciaService->asignarTecnico($incidencia->id, $tecnico->id);
        $this->assertEquals($tecnico->id, $incidencia->fresh()->asignado_a);

        // 3. Resolver incidencia (el equipo se restaura a disponible)
        $incidenciaService->cambiarEstado(
            incidenciaId: $incidencia->id,
            nuevoEstado: 'resuelta',
            notas: 'Se reemplazó cable HDMI y conector de tarjeta madre.',
            restaurarEquipo: true,
            operadorId: $tecnico->id
        );

        $this->assertEquals('resuelta', $incidencia->fresh()->estado);
        $this->assertNotNull($incidencia->fresh()->fecha_resolucion);
        $this->assertEquals('disponible', $equipo->fresh()->estado);
    }

    public function test_reserva_service_conflict_detection(): void
    {
        $reservaService = app(ReservaService::class);
        $docente = User::factory()->create();

        $laboratorio = Laboratorio::create([
            'nombre'    => 'L-03',
            'ubicacion' => 'E',
            'capacidad' => 30,
            'activo'    => true,
        ]);

        // 1. Primera reserva: 10:00 a 12:00
        $reserva1 = $reservaService->crearReserva([
            'laboratorio_id' => $laboratorio->id,
            'inicio'         => '2026-11-01 10:00:00',
            'fin'            => '2026-11-01 12:00:00',
            'motivo'         => 'Clase Redes',
        ], $docente->id);

        $this->assertEquals('confirmada', $reserva1->estado);

        // 2. Intentar solapar: 11:00 a 13:00 (debe lanzar DomainException)
        $this->expectException(DomainException::class);
        $reservaService->crearReserva([
            'laboratorio_id' => $laboratorio->id,
            'inicio'         => '2026-11-01 11:00:00',
            'fin'            => '2026-11-01 13:00:00',
            'motivo'         => 'Práctica traslapada',
        ], $docente->id);
    }

    public function test_software_service_license_seats_limit(): void
    {
        $softwareService = app(SoftwareService::class);
        $tecnico = User::factory()->create();

        $sw = $softwareService->registrarSoftware([
            'nombre'  => 'Matlab Campus',
            'version' => 'R2026a',
        ], $tecnico->id);

        // Licencia con sólo 1 asiento
        $licencia = $softwareService->registrarLicencia($sw->id, [
            'tipo'               => 'academica',
            'cantidad_licencias' => 1,
            'estado'             => 'activa',
        ], $tecnico->id);

        $cat = \App\Models\CategoriaEquipo::firstOrCreate(['nombre' => 'Cómputo']);
        $equipo1 = Equipo::create(['categoria_equipo_id' => $cat->id, 'codigo_inventario' => 'PC-A', 'nombre' => 'PC A']);
        $equipo2 = Equipo::create(['categoria_equipo_id' => $cat->id, 'codigo_inventario' => 'PC-B', 'nombre' => 'PC B']);

        // 1. Instalar en PC-A (exitoso)
        $softwareService->instalarEnEquipo($sw->id, $equipo1->id, $licencia->id, $tecnico->id);
        $this->assertDatabaseHas('instalacion_software', ['equipo_id' => $equipo1->id]);

        // 2. Instalar en PC-B (debe rechazar por límite de asientos)
        $this->expectException(DomainException::class);
        $softwareService->instalarEnEquipo($sw->id, $equipo2->id, $licencia->id, $tecnico->id);
    }

    public function test_bitacora_service_persists_logs(): void
    {
        $bitacoraService = app(BitacoraService::class);
        $user = User::factory()->create();

        $log = $bitacoraService->registrar(
            accion: 'Acceso a consola de telecomunicaciones',
            modulo: 'Redes',
            detalle: ['rack' => 'R-E104', 'vlan' => 10],
            userId: $user->id,
            ip: '192.168.10.50'
        );

        $this->assertDatabaseHas('bitacoras', [
            'id'           => $log->id,
            'accion'       => 'Acceso a consola de telecomunicaciones',
            'direccion_ip' => '192.168.10.50',
        ]);
    }
}
