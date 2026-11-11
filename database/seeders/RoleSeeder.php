<?php

namespace Database\Seeders;

use App\Models\CategoriaEquipo;
use App\Models\Rol;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Los 3 roles institucionales definitivos de MetalNet
        $roles = [
            [
                'nombre' => 'admin',
                'descripcion' => 'Administrador de Laboratorios · Control total institucional, inventario, mantenimiento y operación de salas, reparación de averías, autorización de solicitudes y horarios maestros.',
            ],
            [
                'nombre' => 'docente',
                'descripcion' => 'Docente · Programación de laboratorios completos por bloque (Clase, Práctica o Examen), solicitud de software y gestión académica.',
            ],
            [
                'nombre' => 'alumno',
                'descripcion' => 'Alumno · Acceso a salas, reserva de estación individual para práctica libre y consulta de disponibilidad en tiempo real.',
            ],
        ];

        foreach ($roles as $r) {
            Rol::updateOrCreate(
                ['nombre' => $r['nombre']],
                ['descripcion' => $r['descripcion']]
            );
        }

        // 2. Categorías base de equipamiento
        $categorias = [
            ['nombre' => 'Estaciones de Cómputo (PC)', 'descripcion' => 'Workstations fijas instaladas en las estaciones PC-01 a PC-20 de cada laboratorio.'],
            ['nombre' => 'Proyectores y Cañones', 'descripcion' => 'Cañones láser 4K/FHD fijos y portátiles para sesiones académicas.'],
            ['nombre' => 'Servidores y Racks', 'descripcion' => 'Servidores de clúster GPU, switches centrales y unidades de almacenamiento.'],
            ['nombre' => 'Equipamiento de Redes', 'descripcion' => 'Switches Cisco Catalyst, routers WAN, firewalls y transceptores SFP+.'],
            ['nombre' => 'Periféricos e Instrumentación', 'descripcion' => 'Osciloscopios digitales, fuentes DC programables, visores XR y kits embebidos.'],
        ];

        foreach ($categorias as $c) {
            CategoriaEquipo::updateOrCreate(
                ['nombre' => $c['nombre']],
                ['descripcion' => $c['descripcion']]
            );
        }
    }
}
