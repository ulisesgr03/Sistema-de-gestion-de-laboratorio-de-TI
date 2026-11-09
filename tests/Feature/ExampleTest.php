<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
    }

    public function test_salas_view_renders_successfully(): void
    {
        $response = $this->get('/salas');
        $response->assertStatus(200);
    }

    public function test_horarios_view_renders_successfully(): void
    {
        $response = $this->get('/horarios');
        $response->assertStatus(200);
    }

    public function test_software_view_renders_successfully(): void
    {
        $response = $this->get('/software');
        $response->assertStatus(200);
    }

    public function test_prestamos_view_renders_successfully(): void
    {
        $response = $this->get('/prestamos');
        $response->assertStatus(200);
    }

    public function test_reglamento_view_renders_successfully(): void
    {
        $response = $this->get('/reglamento');
        $response->assertStatus(200);
    }

    public function test_reportar_averia_view_renders_successfully(): void
    {
        $response = $this->get('/reportar-averia');
        $response->assertStatus(200);
    }

    public function test_login_view_renders_successfully(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
    }

    public function test_panel_operativo_view_renders_successfully_for_authenticated_user(): void
    {
        $user = \App\Models\User::factory()->create([
            'matricula' => 'ADM-001',
            'activo' => true,
        ]);
        $response = $this->actingAs($user)->get('/panel-operativo');
        $response->assertStatus(200);
    }
}
