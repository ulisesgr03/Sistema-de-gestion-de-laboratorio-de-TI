<?php

namespace Database\Seeders;

use App\Models\CategoriaEquipo;
use App\Models\Equipo;
use App\Models\Estacion;
use App\Models\Horario;
use App\Models\Incidencia;
use App\Models\Laboratorio;
use App\Models\Proceso;
use App\Models\ReservaLaboratorio;
use App\Models\Software;
use App\Models\User;
use App\Support\CatalogoLaboratorios;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database for MetalNet · Laboratorios de Ingeniería en Sistemas.
     */
    public function run(): void
    {
        $this->call(RoleSeeder::class);

        // 1. Crear o actualizar los 3 usuarios semilla oficiales leyendo desde .env con Hash::make(env(...))
        $adminUser = User::updateOrCreate(
            ['email' => env('ADMIN_EMAIL', 'admin@metalnet.edu')],
            [
                'name' => 'Dr. Alejandro Vance · Admin TI',
                'matricula' => 'MN-ADM-001',
                'password' => Hash::make(env('ADMIN_PASSWORD', 'AdminMN2026!')),
                'activo' => true,
                'telefono' => '55-8000-0001',
            ]
        );
        $adminUser->assignRole('admin');

        $teacherUser = User::updateOrCreate(
            ['email' => env('TEACHER_EMAIL', 'docente@metalnet.edu')],
            [
                'name' => 'Dr. Fernando Morales · Docente Titular',
                'matricula' => 'MN-DOC-001',
                'password' => Hash::make(env('TEACHER_PASSWORD', 'TeacherMN2026!')),
                'activo' => true,
                'telefono' => '55-8000-0003',
            ]
        );
        $teacherUser->assignRole('docente');

        $studentUser = User::updateOrCreate(
            ['email' => env('STUDENT_EMAIL', 'alumno@metalnet.edu')],
            [
                'name' => 'Daniel Freeman · Alumno Ingeniería',
                'matricula' => 'MN-ALU-2026',
                'password' => Hash::make(env('STUDENT_PASSWORD', 'StudentMN2026!')),
                'activo' => true,
                'telefono' => '55-8000-0004',
            ]
        );
        $studentUser->assignRole('alumno');

        // 2. Poblar los 10 Laboratorios y sus 20 Estaciones por Laboratorio (200 PCs)
        CatalogoLaboratorios::clearCache();
        $catalogoLabs = CatalogoLaboratorios::laboratorios();
        $catPc = CategoriaEquipo::firstOrCreate(
            ['nombre' => 'Estaciones de Cómputo (PC)'],
            ['descripcion' => 'Workstations fijas instaladas en los 10 laboratorios.']
        );

        foreach ($catalogoLabs as $labData) {
            $labModel = Laboratorio::updateOrCreate(
                ['id' => $labData['id']],
                [
                    'nombre' => $labData['nombre'],
                    'ubicacion' => $labData['ubicacion'],
                    'descripcion' => $labData['hardware'] . ' | Software: ' . $labData['software'],
                    'capacidad' => 20,
                    'activo' => true,
                ]
            );

            // Horarios base de Lunes (1) a Sábado (6)
            for ($dia = 1; $dia <= 6; $dia++) {
                Horario::updateOrCreate(
                    [
                        'laboratorio_id' => $labModel->id,
                        'dia_semana' => $dia,
                        'hora_inicio' => '07:00:00',
                    ],
                    [
                        'hora_fin' => '19:00:00',
                        'activo' => true,
                    ]
                );
            }

            // 20 Estaciones (PC-01 a PC-20) con sus equipos e inventario
            foreach ($labData['pcs'] as $pcData) {
                $estacion = Estacion::updateOrCreate(
                    [
                        'laboratorio_id' => $labModel->id,
                        'nombre' => $pcData['codigo'],
                    ],
                    [
                        'ubicacion' => $pcData['estado'] === 'mantenimiento'
                            ? 'En diagnóstico técnico · Revisión de hardware/red'
                            : ($pcData['estado'] === 'ocupado' ? 'En sesión académica activa' : 'Disponible para práctica'),
                        'estado' => $pcData['estado'],
                    ]
                );

                Equipo::updateOrCreate(
                    ['codigo_inventario' => $pcData['inventario']],
                    [
                        'categoria_equipo_id' => $catPc->id,
                        'estacion_id' => $estacion->id,
                        'nombre' => "{$labData['codigo']} · {$pcData['codigo']}",
                        'marca' => 'MetalNet Custom Workstation',
                        'modelo' => $labData['hardware_corto'],
                        'numero_serie' => 'SN-' . $pcData['inventario'],
                        'fecha_adquisicion' => '2026-01-15',
                        'estado' => $pcData['estado'],
                        'observaciones' => $labData['hardware'],
                    ]
                );
            }
        }

        // 3. Poblar Catálogo de Software Oficial
        foreach (CatalogoLaboratorios::software() as $swData) {
            Software::updateOrCreate(
                ['nombre' => $swData['nombre']],
                [
                    'version' => $swData['version'],
                    'fabricante' => $swData['fabricante'],
                    'descripcion' => $swData['descripcion'],
                ]
            );
        }

        // 4. Sembrar Solicitudes / Procesos de ejemplo para la bandeja de Admin y paneles de Docente/Alumno
        Proceso::updateOrCreate(
            [
                'user_id' => $teacherUser->id,
                'laboratorio_id' => 4,
                'tipo' => 'Actualización Software · PyTorch 2.5 + CUDA',
            ],
            [
                'descripcion' => 'Actualización de contenedores JupyterLab y librería PyTorch para práctica de Visión Artificial (Grupo MN-IA-902).',
                'estado' => 'pendiente',
            ]
        );

        Proceso::updateOrCreate(
            [
                'user_id' => $studentUser->id,
                'laboratorio_id' => 5,
                'tipo' => 'Préstamo Hardware · Kit IoT ESP32-S3 + Osciloscopio',
            ],
            [
                'descripcion' => 'Solicitud de préstamo de mesa de trabajo PC-04 y kit de sensores MQTT para proyecto integrador.',
                'estado' => 'pendiente',
            ]
        );

        // 5. Sembrar Reservas de ejemplo para Docente y Alumno
        ReservaLaboratorio::updateOrCreate(
            [
                'laboratorio_id' => 1,
                'user_id' => $teacherUser->id,
                'inicio' => now()->setTime(9, 0),
            ],
            [
                'fin' => now()->setTime(11, 0),
                'estado' => 'aprobada',
                'motivo' => '[PRÁCTICA] Ruteo WAN y BGP Avanzado · Grupo MN-RED-801 (20 Estaciones)',
            ]
        );

        ReservaLaboratorio::updateOrCreate(
            [
                'laboratorio_id' => 3,
                'user_id' => $studentUser->id,
                'inicio' => now()->setTime(11, 0),
            ],
            [
                'fin' => now()->setTime(13, 0),
                'estado' => 'aprobada',
                'motivo' => '[ESTACIÓN PC-04] Práctica libre de arquitectura de microservicios con Docker Desktop',
            ]
        );

        // 6. Sembrar Incidencias Técnicas para Admin
        Incidencia::updateOrCreate(
            [
                'laboratorio_id' => 1,
                'titulo' => '[HARDWARE] Fallo de enlace VLAN en PC-10',
            ],
            [
                'equipo_id' => null,
                'reportado_por' => $teacherUser->id,
                'asignado_a' => $adminUser->id,
                'descripcion' => 'Estación L-01 · PC-10 no detecta enlace troncal en segunda interfaz Gigabit Intel I225-V.',
                'prioridad' => 'alta',
                'estado' => 'abierta',
            ]
        );

        Incidencia::updateOrCreate(
            [
                'laboratorio_id' => 4,
                'titulo' => '[SOFTWARE] Driver NVIDIA CUDA requiere reinicio de demonio en PC-03',
            ],
            [
                'equipo_id' => null,
                'reportado_por' => $studentUser->id,
                'asignado_a' => $adminUser->id,
                'descripcion' => 'Al ejecutar entrenamiento distribuido en L-04 · PC-03 aparece mismatch de versión NVML.',
                'prioridad' => 'media',
                'estado' => 'abierta',
            ]
        );

        CatalogoLaboratorios::clearCache();
    }
}
