<?php

namespace Tests\Feature;

use App\Models\Bitacora;
use App\Models\Laboratorio;
use App\Models\Rol;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class LaboratorioMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Rol::create(['nombre' => 'Administrador']);
        Rol::create(['nombre' => 'Encargado de laboratorio']);
        Rol::create(['nombre' => 'Docente']);
        Rol::create(['nombre' => 'Alumno']);

        // Registrar rutas de prueba para los middlewares
        Route::get('/test/admin-only', function () {
            return response()->json(['success' => true, 'message' => 'Zona de Administrador']);
        })->middleware(['role:administrador']);

        Route::get('/test/encargado-o-admin', function () {
            return response()->json(['success' => true, 'message' => 'Zona Operativa']);
        })->middleware(['role:administrador,encargado de laboratorio']);

        Route::post('/test/accion-mutante', function () {
            return response()->json(['success' => true, 'message' => 'Operación realizada']);
        })->middleware(['bitacora']);
    }

    public function test_usuario_no_autenticado_es_rechazado_por_middleware_role(): void
    {
        $response = $this->getJson('/test/admin-only');
        $response->assertStatus(401);
    }

    public function test_alumno_no_puede_acceder_a_ruta_de_administrador(): void
    {
        $alumno = User::create([
            'name' => 'Alumno Prueba',
            'email' => 'alumno@uptex.edu.mx',
            'matricula' => 'ALU-100',
            'password' => 'secret123',
            'activo' => true,
        ]);
        $alumno->assignRole('Alumno');

        $response = $this->actingAs($alumno)->getJson('/test/admin-only');
        $response->assertStatus(403);
    }

    public function test_administrador_accede_satisfactoriamente_a_ruta_protegida(): void
    {
        $admin = User::create([
            'name' => 'Admin General',
            'email' => 'admin@uptex.edu.mx',
            'matricula' => 'ADM-100',
            'password' => 'secret123',
            'activo' => true,
        ]);
        $admin->assignRole('Administrador');

        $response = $this->actingAs($admin)->getJson('/test/admin-only');
        $response->assertStatus(200);
        $response->assertJson(['success' => true, 'message' => 'Zona de Administrador']);
    }

    public function test_usuario_desactivado_es_bloqueado_por_middleware_activo(): void
    {
        $usuarioInactivo = User::create([
            'name' => 'Usuario Desactivado',
            'email' => 'inactivo@uptex.edu.mx',
            'matricula' => 'INA-001',
            'password' => 'secret123',
            'activo' => false,
        ]);
        $usuarioInactivo->assignRole('Administrador');

        $response = $this->actingAs($usuarioInactivo)->getJson('/test/admin-only');
        $response->assertStatus(403);
    }

    public function test_middleware_bitacora_registra_operaciones_mutantes(): void
    {
        $docente = User::create([
            'name' => 'Docente Prueba',
            'email' => 'docente@uptex.edu.mx',
            'matricula' => 'DOC-200',
            'password' => 'secret123',
            'activo' => true,
        ]);

        $response = $this->actingAs($docente)->postJson('/test/accion-mutante', [
            'parametro' => 'valor_prueba',
        ]);

        $response->assertStatus(200);

        // Verificar que exista el registro en la tabla de bitácoras
        $registroBitacora = Bitacora::where('user_id', $docente->id)->first();
        $this->assertNotNull($registroBitacora);
        $this->assertStringContainsString('POST test/accion-mutante', $registroBitacora->accion);
    }
}
