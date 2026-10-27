<?php

namespace Tests\Feature;

use App\Models\AsignacionEquipo;
use App\Models\Bitacora;
use App\Models\CategoriaEquipo;
use App\Models\Equipo;
use App\Models\Laboratorio;
use App\Models\Rol;
use App\Models\User;
use App\Models\UsuarioLaboratorio;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class LaboratorioMiddlewaresTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Registrar rutas de prueba protegidas con los middlewares
        Route::get('/test-role-admin', fn() => 'OK')->middleware(['role:Administrador']);
        Route::get('/test-role-multi', fn() => 'OK')->middleware(['role:Docente,Tecnico']);
        Route::get('/test-lab-active/{laboratorio}', fn() => 'OK')->middleware(['lab.active']);
        Route::get('/test-lab-access/{laboratorio}', fn() => 'OK')->middleware(['lab.access:responsable']);
        Route::post('/test-loan-eligible', fn() => 'OK')->middleware(['loan.eligible']);
        Route::post('/test-audit-log', fn() => response()->json(['success' => true]))->middleware(['audit.log:TestModulo']);
    }

    public function test_role_middleware_allows_authorized_user(): void
    {
        $user = User::factory()->create();
        $rol = Rol::create(['nombre' => 'Administrador']);
        $user->roles()->attach($rol);

        $response = $this->actingAs($user)->get('/test-role-admin');
        $response->assertStatus(200);
        $this->assertEquals('OK', $response->getContent());
    }

    public function test_role_middleware_denies_unauthorized_user(): void
    {
        $user = User::factory()->create();
        $rol = Rol::create(['nombre' => 'Estudiante']);
        $user->roles()->attach($rol);

        $response = $this->actingAs($user)->get('/test-role-admin');
        $response->assertStatus(403);
    }

    public function test_laboratory_active_middleware_blocks_inactive_room_for_normal_users(): void
    {
        $user = User::factory()->create(); // Usuario normal
        $lab = Laboratorio::create(['nombre' => 'L-05 En Remodelación', 'ubicacion' => 'Edificio E', 'activo' => false]);

        $response = $this->actingAs($user)->get("/test-lab-active/{$lab->id}");
        $response->assertStatus(423); // 423 Locked
    }

    public function test_laboratory_active_middleware_allows_admin_for_maintenance(): void
    {
        $admin = User::factory()->create();
        $rol = Rol::create(['nombre' => 'Administrador']);
        $admin->roles()->attach($rol);

        $lab = Laboratorio::create(['nombre' => 'L-05 En Remodelación', 'ubicacion' => 'Edificio E', 'activo' => false]);

        $response = $this->actingAs($admin)->get("/test-lab-active/{$lab->id}");
        $response->assertStatus(200);
    }

    public function test_laboratory_access_middleware_validates_assignment(): void
    {
        $user = User::factory()->create();
        $lab = Laboratorio::create(['nombre' => 'L-04', 'ubicacion' => 'Edificio E', 'activo' => true]);

        // Sin asignación -> 403
        $response = $this->actingAs($user)->get("/test-lab-access/{$lab->id}");
        $response->assertStatus(403);

        // Con asignación de responsable -> 200
        UsuarioLaboratorio::create([
            'user_id'        => $user->id,
            'laboratorio_id' => $lab->id,
            'tipo_acceso'    => 'responsable',
        ]);

        $responseOk = $this->actingAs($user)->get("/test-lab-access/{$lab->id}");
        $responseOk->assertStatus(200);
    }

    public function test_equipment_loan_eligibility_blocks_overdue_student(): void
    {
        $alumno = User::factory()->create();
        $tecnico = User::factory()->create();
        $lab = Laboratorio::create(['nombre' => 'L-01', 'ubicacion' => 'E']);
        $cat = CategoriaEquipo::create(['nombre' => 'Herramientas']);
        $equipo = Equipo::create([
            'categoria_equipo_id' => $cat->id,
            'codigo_inventario'   => 'UPT-TOOL-01',
            'nombre'              => 'Pinzas Ponchadoras',
            'estado'              => 'prestado',
        ]);

        // Crear adeudo vencido
        AsignacionEquipo::create([
            'equipo_id'        => $equipo->id,
            'user_id'          => $alumno->id,
            'laboratorio_id'   => $lab->id,
            'asignado_por'     => $tecnico->id,
            'fecha_asignacion' => now()->subDays(5),
            'fecha_devolucion' => now()->subDays(2), // Vencido hace 2 días
            'estado'           => 'vencido',
        ]);

        $response = $this->actingAs($alumno)->postJson('/test-loan-eligible', [
            'user_id' => $alumno->id,
        ]);

        $response->assertStatus(422);
        $this->assertStringContainsString('adeudo', $response->json('message'));
    }

    public function test_audit_log_middleware_records_event(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/test-audit-log', [
            'parametro_prueba' => 'valor_seguro',
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('bitacoras', [
            'user_id' => $user->id,
            'modulo'  => 'TestModulo',
            'accion'  => 'POST test-audit-log',
        ]);
    }
}
