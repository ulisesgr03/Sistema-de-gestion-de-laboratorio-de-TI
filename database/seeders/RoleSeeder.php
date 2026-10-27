<?php

namespace Database\Seeders;

use App\Models\CategoriaEquipo;
use App\Models\Rol;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Roles institucionales requeridos
        $roles = [
            [
                'nombre' => 'Administrador',
                'descripcion' => 'Acceso total a la configuración del sistema, usuarios, inventario, reportes e indicadores globales.',
            ],
            [
                'nombre' => 'Encargado de laboratorio',
                'descripcion' => 'Administración operativa de laboratorios, estaciones, inventario, software, incidencias, mantenimientos y control de acceso.',
            ],
            [
                'nombre' => 'Docente',
                'descripcion' => 'Consulta de disponibilidad, solicitud de reservas para clases y prácticas académicas, supervisión de equipos.',
            ],
            [
                'nombre' => 'Alumno',
                'descripcion' => 'Consulta de horarios y prácticas autorizadas, solicitud de reservas individuales y uso de equipos asignados.',
            ],
        ];

        foreach ($roles as $r) {
            Rol::firstOrCreate(
                ['nombre' => $r['nombre']],
                ['descripcion' => $r['descripcion']]
            );
        }

        // 2. Categorías base de equipamiento
        $categorias = [
            ['nombre' => 'Estaciones de Cómputo (PC)', 'descripcion' => 'Equipos de escritorio fijos instalados en las estaciones de laboratorio.'],
            ['nombre' => 'Laptops y Portátiles', 'descripcion' => 'Equipos portátiles para préstamo y trabajo en campo.'],
            ['nombre' => 'Servidores y Racks', 'descripcion' => 'Servidores institucionales, switches centrales y unidades de respaldo.'],
            ['nombre' => 'Equipamiento de Redes', 'descripcion' => 'Switches didácticos, routers Cisco, patch panels y puntos de acceso.'],
            ['nombre' => 'Periféricos e Instrumentación', 'descripcion' => 'Multímetros digitales, osciloscopios, proyectores y kits didácticos IoT.'],
        ];

        foreach ($categorias as $c) {
            CategoriaEquipo::firstOrCreate(
                ['nombre' => $c['nombre']],
                ['descripcion' => $c['descripcion']]
            );
        }
    }
}
