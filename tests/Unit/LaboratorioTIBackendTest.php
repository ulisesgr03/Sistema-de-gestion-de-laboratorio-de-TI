<?php

namespace Tests\Unit;

use App\Exceptions\BusinessRuleException;
use App\Models\CategoriaEquipo;
use App\Models\Equipo;
use App\Models\Estacion;
use App\Models\Incidencia;
use App\Models\Laboratorio;
use App\Models\Licencia;
use App\Models\Mantenimiento;
use App\Models\ReservaLaboratorio;
use App\Models\Rol;
use App\Models\Software;
use App\Models\User;
use App\Services\AuthService;
use App\Services\BitacoraService;
use App\Services\DisponibilidadService;
use App\Services\EquipoService;
use App\Services\EstacionService;
use App\Services\IncidenciaService;
use App\Services\LaboratorioService;
use App\Services\MantenimientoService;
use App\Services\PracticaService;
use App\Services\PrestamoService;
use App\Services\ReporteService;
use App\Services\ReservaService;
use App\Services\SoftwareService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LaboratorioTIBackendTest extends TestCase
{
    use RefreshDatabase;

    protected BitacoraService $bitacoraService;
    protected LaboratorioService $laboratorioService;
    protected EstacionService $estacionService;
    protected EquipoService $equipoService;
    protected SoftwareService $softwareService;
    protected ReservaService $reservaService;
    protected PrestamoService $prestamoService;
    protected IncidenciaService $incidenciaService;
    protected MantenimientoService $mantenimientoService;
    protected PracticaService $practicaService;
    protected DisponibilidadService $disponibilidadService;
    protected ReporteService $reporteService;
    protected AuthService $authService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->bitacoraService = app(BitacoraService::class);
        $this->laboratorioService = app(LaboratorioService::class);
        $this->estacionService = app(EstacionService::class);
        $this->equipoService = app(EquipoService::class);
        $this->softwareService = app(SoftwareService::class);
        $this->reservaService = app(ReservaService::class);
        $this->prestamoService = app(PrestamoService::class);
        $this->incidenciaService = app(IncidenciaService::class);
        $this->mantenimientoService = app(MantenimientoService::class);
        $this->practicaService = app(PracticaService::class);
        $this->disponibilidadService = app(DisponibilidadService::class);
        $this->reporteService = app(ReporteService::class);
        $this->authService = app(AuthService::class);

        // Crear roles institucionales
        Rol::create(['nombre' => 'Administrador']);
        Rol::create(['nombre' => 'Encargado de laboratorio']);
        Rol::create(['nombre' => 'Docente']);
        Rol::create(['nombre' => 'Alumno']);

        // Crear categoría base
        CategoriaEquipo::create(['nombre' => 'Cómputo']);

        // Crear laboratorio base
        Laboratorio::create([
            'nombre' => 'Laboratorio Central de TI',
            'ubicacion' => 'Edificio B, Aula 101',
            'capacidad' => 30,
            'activo' => true,
        ]);
    }

    public function test_usuario_puede_asignar_y_verificar_roles(): void
    {
        $usuario = User::create([
            'name' => 'Carlos Mendoza',
            'email' => 'carlos@uptex.edu.mx',
            'matricula' => 'TEC-1001',
            'password' => 'secret123',
            'activo' => true,
        ]);

        $usuario->assignRole('Encargado de laboratorio');

        $this->assertTrue($usuario->hasRole('Encargado de laboratorio'));
        $this->assertTrue($usuario->isEncargado());
        $this->assertFalse($usuario->isAdmin());
        $this->assertFalse($usuario->isAlumno());
    }

    public function test_regla_anticipacion_minima_24_horas_en_reservas(): void
    {
        $lab = $this->laboratorioService->crear([
            'nombre' => 'Laboratorio de Redes Cisco',
            'ubicacion' => 'Edificio D, Planta Alta',
            'capacidad' => 30,
        ]);

        $alumno = User::create([
            'name' => 'Ana Garcia',
            'email' => 'ana@uptex.edu.mx',
            'matricula' => '2026101',
            'password' => 'secret123',
            'activo' => true,
        ]);
        $alumno->assignRole('Alumno');

        // Intentar reservar dentro de 2 horas (menor a 24h de anticipación)
        $this->expectException(BusinessRuleException::class);
        $this->expectExceptionMessage('anticipación mínima');

        $this->reservaService->solicitar([
            'laboratorio_id' => $lab->id,
            'inicio' => Carbon::now()->addHours(2),
            'fin' => Carbon::now()->addHours(4),
            'motivo' => 'Práctica no tutelada de redes',
        ], $alumno);
    }

    public function test_no_se_permiten_reservas_simultaneas_en_el_mismo_laboratorio(): void
    {
        $lab = $this->laboratorioService->crear([
            'nombre' => 'Laboratorio de Software Libre',
            'ubicacion' => 'Edificio A',
            'capacidad' => 25,
        ]);

        $docente = User::create([
            'name' => 'Profesor Juan Perez',
            'email' => 'juan@uptex.edu.mx',
            'matricula' => 'DOC-501',
            'password' => 'secret123',
            'activo' => true,
        ]);
        $docente->assignRole('Docente');

        $inicio = Carbon::now()->addDays(2)->setHour(10)->setMinute(0);
        $fin = $inicio->copy()->addHours(2);

        // Primera reserva válida
        $reserva1 = $this->reservaService->solicitar([
            'laboratorio_id' => $lab->id,
            'inicio' => $inicio,
            'fin' => $fin,
            'motivo' => 'Clase de Programación Avanzada',
        ], $docente);

        $this->assertNotNull($reserva1->id);

        // Segunda solicitud con solapamiento de horario
        $this->expectException(BusinessRuleException::class);
        $this->expectExceptionMessage('ya cuenta con una reserva programada');

        $this->reservaService->solicitar([
            'laboratorio_id' => $lab->id,
            'inicio' => $inicio->copy()->addMinutes(30),
            'fin' => $fin->copy()->addHours(1),
            'motivo' => 'Conflicto intencional',
        ], $docente);
    }

    public function test_prestamo_limite_un_equipo_activo_por_persona(): void
    {
        $categoria = CategoriaEquipo::first();
        $equipo1 = $this->equipoService->crear([
            'codigo_inventario' => 'PC-LAB1-01',
            'nombre' => 'Workstation Dell Precision',
            'categoria_equipo_id' => $categoria->id,
        ]);

        $equipo2 = $this->equipoService->crear([
            'codigo_inventario' => 'PC-LAB1-02',
            'nombre' => 'Workstation Dell Precision 2',
            'categoria_equipo_id' => $categoria->id,
        ]);

        $alumno = User::create([
            'name' => 'Luis Hernandez',
            'email' => 'luis@uptex.edu.mx',
            'matricula' => '2026102',
            'password' => 'secret123',
            'activo' => true,
        ]);
        $alumno->assignRole('Alumno');

        $admin = User::create([
            'name' => 'Admin Central',
            'email' => 'admin@uptex.edu.mx',
            'matricula' => 'ADM-001',
            'password' => 'secret123',
            'activo' => true,
        ]);
        $admin->assignRole('Administrador');

        // Primer préstamo exitoso
        $prestamo1 = $this->prestamoService->prestarEquipo([
            'user_id' => $alumno->id,
            'equipo_id' => $equipo1->id,
        ], $admin->id);

        $this->assertEquals('prestado', $prestamo1->estado);
        $this->assertEquals('prestado', $equipo1->fresh()->estado);

        // Segundo préstamo debe ser bloqueado por la regla de negocio
        $this->expectException(BusinessRuleException::class);
        $this->expectExceptionMessage('No se permite más de un préstamo individual simultáneo');

        $this->prestamoService->prestarEquipo([
            'user_id' => $alumno->id,
            'equipo_id' => $equipo2->id,
        ], $admin->id);
    }

    public function test_devolucion_con_dano_genera_incidencia_automatica(): void
    {
        $categoria = CategoriaEquipo::first();
        $equipo = $this->equipoService->crear([
            'codigo_inventario' => 'SW-CISCO-2960',
            'nombre' => 'Switch Cisco Catalyst 2960',
            'categoria_equipo_id' => $categoria->id,
        ]);

        $alumno = User::create([
            'name' => 'Mario Rossi',
            'email' => 'mario@uptex.edu.mx',
            'matricula' => '2026103',
            'password' => 'secret123',
            'activo' => true,
        ]);

        $admin = User::create([
            'name' => 'Admin Central',
            'email' => 'admin2@uptex.edu.mx',
            'matricula' => 'ADM-002',
            'password' => 'secret123',
            'activo' => true,
        ]);

        $prestamo = $this->prestamoService->prestarEquipo([
            'user_id' => $alumno->id,
            'equipo_id' => $equipo->id,
        ], $admin->id);

        // Registrar devolución reportando daño en puerto Gigabit
        $prestamoDevuelto = $this->prestamoService->registrarDevolucion(
            $prestamo->id,
            [
                'presenta_danos' => true,
                'estado_fisico_devolucion' => 'Puerto Gi0/1 dañado físicamente con pines rotos.',
                'observaciones' => 'Requiere cambio de módulo.',
            ],
            $admin->id
        );

        $this->assertEquals('devuelto', $prestamoDevuelto->estado);
        // Equipo pasa a fuera de servicio automáticamente
        $this->assertEquals('fuera de servicio', $equipo->fresh()->estado);

        // Verificar que se haya creado la incidencia técnica en el sistema
        $incidenciaCreada = Incidencia::where('equipo_id', $equipo->id)->first();
        $this->assertNotNull($incidenciaCreada);
        $this->assertEquals('alta', $incidenciaCreada->prioridad);
        $this->assertStringContainsString('Puerto Gi0/1 dañado', $incidenciaCreada->descripcion);
    }

    public function test_software_control_limite_de_licencias(): void
    {
        $categoria = CategoriaEquipo::first();
        $equipo1 = $this->equipoService->crear([
            'codigo_inventario' => 'PC-MATLAB-01',
            'nombre' => 'PC 01',
            'categoria_equipo_id' => $categoria->id,
        ]);

        $equipo2 = $this->equipoService->crear([
            'codigo_inventario' => 'PC-MATLAB-02',
            'nombre' => 'PC 02',
            'categoria_equipo_id' => $categoria->id,
        ]);

        $software = $this->softwareService->registrarSoftware([
            'nombre' => 'MATLAB R2026b',
            'version' => '2026b',
            'fabricante' => 'MathWorks',
        ]);

        // Registrar licencia con capacidad estricta de 1 asiento
        $licencia = $this->softwareService->registrarLicencia($software, [
            'tipo' => 'educativa',
            'clave_licencia' => 'MATLAB-1-SEAT-XYZ',
            'cantidad_licencias' => 1,
            'fecha_vencimiento' => Carbon::now()->addYear(),
        ]);

        // Primera instalación en equipo 1 debe ser exitosa
        $inst1 = $this->softwareService->instalarEnEquipo(
            $equipo1->id,
            $software->id,
            $licencia->id
        );
        $this->assertEquals('instalado', $inst1->estado);

        // Segunda instalación en equipo 2 debe ser rechazada por agotar el límite
        $this->expectException(BusinessRuleException::class);
        $this->expectExceptionMessage('límite máximo de instalaciones');

        $this->softwareService->instalarEnEquipo(
            $equipo2->id,
            $software->id,
            $licencia->id
        );
    }

    public function test_mantenimiento_preventivo_restablece_disponibilidad_tras_verificacion(): void
    {
        $categoria = CategoriaEquipo::first();
        $equipo = $this->equipoService->crear([
            'codigo_inventario' => 'PC-SRV-01',
            'nombre' => 'Servidor de Prácticas',
            'categoria_equipo_id' => $categoria->id,
        ]);

        $tecnico = User::create([
            'name' => 'Tecnico Mantenimiento',
            'email' => 'tecnico@uptex.edu.mx',
            'matricula' => 'TEC-200',
            'password' => 'secret123',
            'activo' => true,
        ]);

        // Programar mantenimiento para hoy
        $mantenimiento = $this->mantenimientoService->programar([
            'equipo_id' => $equipo->id,
            'tipo' => 'preventivo',
            'titulo' => 'Limpieza de disipador y actualización de firmware',
            'actividades' => 'Mantenimiento integral y cambio de pasta térmica',
            'fecha_programada' => Carbon::now(),
        ], $tecnico->id);

        $this->assertEquals('en mantenimiento', $equipo->fresh()->estado);

        // Completar mantenimiento con verificación exitosa
        $mantenimientoCompletado = $this->mantenimientoService->completar(
            $mantenimiento,
            [
                'resultado' => 'Firmware actualizado a v4.2. Sensores térmicos en rango nominal.',
                'verificacion_funcionamiento_exitosa' => true,
            ],
            $tecnico->id
        );

        $this->assertEquals('completado', $mantenimientoCompletado->estado);
        $this->assertEquals('disponible', $equipo->fresh()->estado);
    }

    public function test_reportes_e_indicadores_dashboard(): void
    {
        $indicadores = $this->reporteService->obtenerIndicadoresDashboard();

        $this->assertArrayHasKey('kpis', $indicadores);
        $this->assertArrayHasKey('distribucion_equipos', $indicadores);
        $this->assertArrayHasKey('actividad_reciente', $indicadores);

        $kpis = $indicadores['kpis'];
        $this->assertArrayHasKey('laboratorios_disponibles', $kpis);
        $this->assertArrayHasKey('equipos_disponibles', $kpis);
        $this->assertArrayHasKey('porcentaje_ocupacion', $kpis);
    }
}
