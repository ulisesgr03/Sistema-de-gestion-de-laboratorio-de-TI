<?php

namespace App\Support;

use App\Models\Estacion;
use App\Models\Incidencia;
use App\Models\Proceso;
use App\Models\ReservaLaboratorio;
use Illuminate\Support\Facades\Schema;

/**
 * Catálogo central de la estructura MetalNet · Laboratorios de Ingeniería en Sistemas
 * (10 Laboratorios: LAB 1 a LAB 10), estaciones interactivas PC-01 a PC-20, estado de cañón/proyector,
 * horarios detallados con modalidad (Clase, Práctica, Examen), catálogo de software con logotipos locales,
 * inventario físico de pañol y bandeja administrativa sincronizada con Base de Datos.
 */
class CatalogoLaboratorios
{
    private static ?array $labsCache = null;

    public static function clearCache(): void
    {
        self::$labsCache = null;
    }

    /**
     * 10 Laboratorios de TI distribuidos en Planta Baja (LAB 1 - LAB 5) y Planta Alta (LAB 6 - LAB 10).
     */
    public static function laboratorios(): array
    {
        if (self::$labsCache !== null) {
            return self::$labsCache;
        }

        // Mapear estados en vivo desde la base de datos si existen
        $dbStates = [];
        $activeReservas = [];
        try {
            if (Schema::hasTable('estaciones')) {
                $estacionesDb = Estacion::all();
                foreach ($estacionesDb as $est) {
                    $dbStates[$est->laboratorio_id . ':' . strtoupper(trim($est->nombre))] = [
                        'estado' => $est->estado,
                        'ubicacion' => $est->ubicacion,
                    ];
                }
            }
            if (Schema::hasTable('reservas_laboratorio')) {
                $reservas = ReservaLaboratorio::with('usuario')
                    ->whereIn('estado', ['confirmada', 'aprobada'])
                    ->where(function ($q) {
                        $q->whereBetween('inicio', [now()->startOfDay(), now()->endOfDay()])
                          ->orWhere(function ($q2) {
                              $q2->where('inicio', '<=', now())
                                 ->where('fin', '>=', now());
                          });
                    })
                    ->get();

                foreach ($reservas as $res) {
                    if (str_starts_with($res->materia ?? '', '[ESTACIÓN')) {
                        if (preg_match('/\[ESTACIÓN\s+PC-(\d+)\]/i', $res->materia, $m)) {
                            $pcNum = str_pad($m[1], 2, '0', STR_PAD_LEFT);
                            $dbStates[$res->laboratorio_id . ":PC-{$pcNum}"] = [
                                'estado' => 'ocupado',
                                'ubicacion' => 'Reservado por ' . ($res->usuario?->name ?? 'Alumno'),
                            ];
                        }
                    } else {
                        $activeReservas[$res->laboratorio_id] = $res;
                    }
                }
            }
        } catch (\Throwable $e) {
            // Fallback limpio en caso de migración en curso
        }

        $base = [
            [
                'id' => 1,
                'codigo' => 'L-01',
                'etiqueta_mapa' => 'LAB 1',
                'nombre_corto' => 'Redes Cisco y Telecomunicaciones',
                'icono' => 'router',
                'imagen' => '/images/labs/lab-01-redes-cisco.svg',
                'planta' => 'Planta Baja',
                'edificio' => 'Sector C · Nivel 1',
                'ubicacion' => 'Planta Baja · Ala Izquierda',
                'tipo' => 'clase',
                'estado_badge' => 'En Clase',
                'asignatura' => 'Ruteo WAN y BGP Avanzado',
                'docente' => 'Mtro. Fernando Morales',
                'grupo' => 'MN-RED-801',
                'canon' => true,
                'canon_estado' => 'Cañón 4K Activo',
                'aforo_max' => 20,
                'horas_libres' => 6,
                'horas_ocupadas' => 6,
                'horas_libres_rangos' => ['11:00 - 13:00', '15:00 - 19:00'],
                'hardware' => 'Intel Core i7-13700, 32GB DDR5, NVMe 1TB, Monitor 24" IPS, Dual NIC Intel I225-V',
                'specs_detalladas' => [
                    'cpu' => 'Intel Core i7-13700 (16 núcleos / 24 hilos @ 5.2 GHz)',
                    'ram' => '32 GB DDR5 5600 MHz Dual Channel',
                    'gpu' => 'Intel UHD Graphics 770 + Aceleración por Hardware',
                    'almacenamiento' => '1 TB SSD NVMe PCIe 4.0 (Lectura 7000 MB/s)',
                    'monitor' => 'Dell UltraSharp 24" FHD IPS Antirreflejante (1920x1080)',
                    'perifericos' => 'Teclado Mecánico TKL, Mouse Óptico 4000 DPI, Cable Consola RJ45-USB',
                    'red' => 'Doble Tarjeta Gigabit/2.5GbE VLAN Trunking + Rack Cisco Catalyst',
                    'os' => 'Dual Boot: Ubuntu 24.04 LTS / Windows 11 Pro for Workstations',
                ],
                'software' => 'Cisco Packet Tracer 8.2.2, Wireshark 4.2.5, GNS3, Putty, Ubuntu 24.04',
                'proximo_cambio' => '11:00 h',
                'mantenimiento' => [10, 20],
                'disponibles' => [4, 8, 12, 15, 16],
                'horarios_horas' => [
                    ['hora' => '07:00', 'rango' => '07:00 - 09:00', 'estado' => 'ocupado', 'modalidad' => 'Clase', 'titulo' => 'Conmutación y Redes LAN', 'docente' => 'Mtro. Fernando Morales', 'grupo' => 'MN-RED-401', 'pcs_uso' => 18],
                    ['hora' => '09:00', 'rango' => '09:00 - 11:00', 'estado' => 'ocupado', 'modalidad' => 'Práctica', 'titulo' => 'Ruteo WAN y BGP Avanzado', 'docente' => 'Mtro. Fernando Morales', 'grupo' => 'MN-RED-801', 'pcs_uso' => 13],
                    ['hora' => '11:00', 'rango' => '11:00 - 13:00', 'estado' => 'libre', 'modalidad' => 'Abierto', 'titulo' => 'Práctica Libre Certificación CCNA', 'docente' => 'Guardia Técnica', 'grupo' => 'Abierto', 'pcs_uso' => 0],
                    ['hora' => '13:00', 'rango' => '13:00 - 15:00', 'estado' => 'ocupado', 'modalidad' => 'Examen', 'titulo' => 'Evaluación Práctica Firewalls ASA', 'docente' => 'Ing. Carlos Mendoza', 'grupo' => 'MN-SEC-901', 'pcs_uso' => 18],
                    ['hora' => '15:00', 'rango' => '15:00 - 17:00', 'estado' => 'libre', 'modalidad' => 'Abierto', 'titulo' => 'Simulación Topológica Abierta', 'docente' => 'Guardia Técnica', 'grupo' => 'Abierto', 'pcs_uso' => 0],
                    ['hora' => '17:00', 'rango' => '17:00 - 19:00', 'estado' => 'libre', 'modalidad' => 'Abierto', 'titulo' => 'Acceso Abierto Vespertino', 'docente' => 'Guardia Técnica', 'grupo' => 'Abierto', 'pcs_uso' => 0],
                ],
            ],
            [
                'id' => 2,
                'codigo' => 'L-02',
                'etiqueta_mapa' => 'LAB 2',
                'nombre_corto' => 'Sistemas Operativos y Virtualización',
                'icono' => 'memory',
                'imagen' => '/images/labs/lab-02-sistemas-operativos.svg',
                'planta' => 'Planta Baja',
                'edificio' => 'Sector C · Nivel 1',
                'ubicacion' => 'Planta Baja · Ala Izquierda',
                'tipo' => 'clase',
                'estado_badge' => 'En Clase',
                'asignatura' => 'Kernels y Contenedores Linux',
                'docente' => 'Dra. Laura Sánchez',
                'grupo' => 'MN-SYS-502',
                'canon' => false,
                'canon_estado' => 'Sin Cañón',
                'aforo_max' => 20,
                'horas_libres' => 4,
                'horas_ocupadas' => 8,
                'horas_libres_rangos' => ['13:00 - 17:00'],
                'hardware' => 'Intel Core i5-13500, 32GB DDR4, NVMe 1TB, Monitor 24" FHD, VT-x / EPT Activo',
                'specs_detalladas' => [
                    'cpu' => 'Intel Core i5-13500 (14 núcleos / 20 hilos con Intel VT-x/VT-d)',
                    'ram' => '32 GB DDR4 3600 MHz ECC-ready para Hipervisores',
                    'gpu' => 'Intel UHD 770 integrada con soporte multi-monitor',
                    'almacenamiento' => '1 TB NVMe M.2 Gen4 + Partición LVM Dedicada',
                    'monitor' => 'Monitor Profesional 24" Full HD 75Hz Low Blue Light',
                    'perifericos' => 'Teclado Español ISO, Mouse Óptico, Programador USB-Blaster FPGA',
                    'red' => 'Ethernet Gigabit 1000BASE-T en VLAN de Virtualización',
                    'os' => 'Host Debian 12 Bookworm + VMware Workstation 17 Pro',
                ],
                'software' => 'VMware Workstation 17 Pro, Docker Desktop, Quartus Prime, Ubuntu 24.04',
                'proximo_cambio' => '13:00 h',
                'mantenimiento' => [7],
                'disponibles' => [3, 11, 14, 19],
                'horarios_horas' => [
                    ['hora' => '07:00', 'rango' => '07:00 - 09:00', 'estado' => 'ocupado', 'modalidad' => 'Clase', 'titulo' => 'Arquitectura x86-64 y Ensamblador', 'docente' => 'Dr. Arturo Vega', 'grupo' => 'MN-SYS-301', 'pcs_uso' => 19],
                    ['hora' => '09:00', 'rango' => '09:00 - 11:00', 'estado' => 'ocupado', 'modalidad' => 'Práctica', 'titulo' => 'Kernels y Contenedores Linux', 'docente' => 'Dra. Laura Sánchez', 'grupo' => 'MN-SYS-502', 'pcs_uso' => 15],
                    ['hora' => '11:00', 'rango' => '11:00 - 13:00', 'estado' => 'ocupado', 'modalidad' => 'Práctica', 'titulo' => 'Síntesis Digital VHDL / FPGA', 'docente' => 'Dr. Arturo Vega', 'grupo' => 'MN-EMB-701', 'pcs_uso' => 16],
                    ['hora' => '13:00', 'rango' => '13:00 - 15:00', 'estado' => 'libre', 'modalidad' => 'Abierto', 'titulo' => 'Laboratorio Abierto de Hipervisores', 'docente' => 'Guardia Técnica', 'grupo' => 'Abierto', 'pcs_uso' => 0],
                    ['hora' => '15:00', 'rango' => '15:00 - 17:00', 'estado' => 'libre', 'modalidad' => 'Abierto', 'titulo' => 'Práctica Libre Sistemas Operativos', 'docente' => 'Guardia Técnica', 'grupo' => 'Abierto', 'pcs_uso' => 0],
                    ['hora' => '17:00', 'rango' => '17:00 - 19:00', 'estado' => 'ocupado', 'modalidad' => 'Examen', 'titulo' => 'Examen Práctico SysAdmin Linux', 'docente' => 'Dra. Laura Sánchez', 'grupo' => 'MN-SYS-503', 'pcs_uso' => 19],
                ],
            ],
            [
                'id' => 3,
                'codigo' => 'L-03',
                'etiqueta_mapa' => 'LAB 3',
                'nombre_corto' => 'Ingeniería de Software y DevOps',
                'icono' => 'terminal',
                'imagen' => '/images/labs/lab-03-ing-software.svg',
                'planta' => 'Planta Baja',
                'edificio' => 'Sector C · Nivel 1',
                'ubicacion' => 'Planta Baja · Centro',
                'tipo' => 'libre',
                'estado_badge' => 'Disponible',
                'asignatura' => 'Práctica Libre DevOps',
                'docente' => 'Ing. Carlos Mendoza',
                'grupo' => 'Abierto',
                'canon' => true,
                'canon_estado' => 'Cañón FHD Activo',
                'aforo_max' => 20,
                'horas_libres' => 8,
                'horas_ocupadas' => 4,
                'horas_libres_rangos' => ['09:00 - 13:00', '15:00 - 19:00'],
                'hardware' => 'AMD Ryzen 7 7700X, 32GB DDR5, NVMe 1TB, Dual Monitor 24" IPS',
                'specs_detalladas' => [
                    'cpu' => 'AMD Ryzen 7 7700X (8 núcleos / 16 hilos @ 5.4 GHz)',
                    'ram' => '32 GB DDR5 6000 MHz para compilación y contenedores',
                    'gpu' => 'AMD Radeon Graphics integrada + Soporte Dual Display',
                    'almacenamiento' => '1 TB SSD NVMe Gen4 (Partición Dev + Docker Volumes)',
                    'monitor' => 'Doble Monitor 24" IPS Sin Bordes (Productividad Full-Stack)',
                    'perifericos' => 'Teclado Mecánico Keychron, Mouse Ergonómico, Hub USB-C',
                    'red' => 'Ethernet Gigabit + Acceso a Registro Privado GitLab MetalNet',
                    'os' => 'Ubuntu 24.04 LTS / Windows 11 Pro con WSL2 Activo',
                ],
                'software' => 'VS Code, Docker Desktop, PostgreSQL 16, Node.js 22, Git, IntelliJ IDEA',
                'proximo_cambio' => '13:00 h',
                'mantenimiento' => [6],
                'disponibles' => [1, 2, 4, 5, 8, 9, 11, 13, 14, 15, 17, 18, 19],
                'horarios_horas' => [
                    ['hora' => '07:00', 'rango' => '07:00 - 09:00', 'estado' => 'ocupado', 'modalidad' => 'Clase', 'titulo' => 'Arquitectura Limpia y Patrones POO', 'docente' => 'Mtra. Patricia Rivas', 'grupo' => 'MN-SW-202', 'pcs_uso' => 19],
                    ['hora' => '09:00', 'rango' => '09:00 - 11:00', 'estado' => 'libre', 'modalidad' => 'Abierto', 'titulo' => 'Desarrollo Libre Full-Stack', 'docente' => 'Ing. Carlos Mendoza', 'grupo' => 'Abierto', 'pcs_uso' => 6],
                    ['hora' => '11:00', 'rango' => '11:00 - 13:00', 'estado' => 'libre', 'modalidad' => 'Abierto', 'titulo' => 'Sesión Abierta Proyectos CI/CD', 'docente' => 'Ing. Carlos Mendoza', 'grupo' => 'Abierto', 'pcs_uso' => 5],
                    ['hora' => '13:00', 'rango' => '13:00 - 15:00', 'estado' => 'ocupado', 'modalidad' => 'Práctica', 'titulo' => 'Microservicios y Contenedores Docker', 'docente' => 'Mtro. Daniel Cruz', 'grupo' => 'MN-SW-702', 'pcs_uso' => 18],
                    ['hora' => '15:00', 'rango' => '15:00 - 17:00', 'estado' => 'libre', 'modalidad' => 'Abierto', 'titulo' => 'Estaciones Abiertas Ingeniería SW', 'docente' => 'Guardia Técnica', 'grupo' => 'Abierto', 'pcs_uso' => 0],
                    ['hora' => '17:00', 'rango' => '17:00 - 19:00', 'estado' => 'libre', 'modalidad' => 'Abierto', 'titulo' => 'Estaciones Abiertas Ingeniería SW', 'docente' => 'Guardia Técnica', 'grupo' => 'Abierto', 'pcs_uso' => 0],
                ],
            ],
            [
                'id' => 4,
                'codigo' => 'L-04',
                'etiqueta_mapa' => 'LAB 4',
                'nombre_corto' => 'IA y Ciencia de Datos',
                'icono' => 'neurology',
                'imagen' => '/images/labs/lab-04-ia-datos.svg',
                'planta' => 'Planta Baja',
                'edificio' => 'Sector C · Nivel 1',
                'ubicacion' => 'Planta Baja · Centro',
                'tipo' => 'taller',
                'estado_badge' => 'En Práctica',
                'asignatura' => 'Deep Learning & Redes Convolucionales',
                'docente' => 'Dra. Elena Ramírez',
                'grupo' => 'MN-IA-902',
                'canon' => true,
                'canon_estado' => 'Cañón Laser 4K',
                'aforo_max' => 20,
                'horas_libres' => 4,
                'horas_ocupadas' => 8,
                'horas_libres_rangos' => ['13:00 - 17:00'],
                'hardware' => 'Workstation Precision, NVIDIA RTX 4070 12GB, 64GB DDR5, 2TB NVMe',
                'specs_detalladas' => [
                    'cpu' => 'Intel Core i9-13900K (24 núcleos / 32 hilos @ 5.8 GHz)',
                    'ram' => '64 GB DDR5 6000 MHz ECC para datasets en memoria',
                    'gpu' => 'NVIDIA GeForce RTX 4070 12 GB GDDR6X (5888 núcleos CUDA + Tensor)',
                    'almacenamiento' => '2 TB SSD NVMe PCIe 4.0 Samsung 990 Pro',
                    'monitor' => 'Monitor 27" QHD (2560x1440) 100% sRGB Calibrado',
                    'perifericos' => 'Teclado Mecánico, Mouse de Precisión, Lector SmartCard',
                    'red' => 'Enlace 10GbE al Servidor de Datasets MetalNet',
                    'os' => 'Ubuntu 24.04 LTS con Driver NVIDIA 555 + CUDA 12.4',
                ],
                'software' => 'PyTorch + CUDA, MATLAB R2024b, VS Code, Docker Desktop',
                'proximo_cambio' => '13:00 h',
                'mantenimiento' => [3, 15],
                'disponibles' => [7, 12, 18, 19],
                'horarios_horas' => [
                    ['hora' => '07:00', 'rango' => '07:00 - 09:00', 'estado' => 'ocupado', 'modalidad' => 'Clase', 'titulo' => 'Modelos Probabilísticos e IA Clásica', 'docente' => 'Dra. Elena Ramírez', 'grupo' => 'MN-IA-701', 'pcs_uso' => 17],
                    ['hora' => '09:00', 'rango' => '09:00 - 11:00', 'estado' => 'ocupado', 'modalidad' => 'Práctica', 'titulo' => 'Deep Learning & Redes Convolucionales', 'docente' => 'Dra. Elena Ramírez', 'grupo' => 'MN-IA-902', 'pcs_uso' => 14],
                    ['hora' => '11:00', 'rango' => '11:00 - 13:00', 'estado' => 'ocupado', 'modalidad' => 'Práctica', 'titulo' => 'Minería de Datos y Pipelines LLM', 'docente' => 'Dr. Gonzalo Ortiz', 'grupo' => 'MN-IA-801', 'pcs_uso' => 16],
                    ['hora' => '13:00', 'rango' => '13:00 - 15:00', 'estado' => 'libre', 'modalidad' => 'Abierto', 'titulo' => 'Cluster GPU Abierto para Entrenamiento', 'docente' => 'Investigación MN', 'grupo' => 'Abierto', 'pcs_uso' => 0],
                    ['hora' => '15:00', 'rango' => '15:00 - 17:00', 'estado' => 'libre', 'modalidad' => 'Abierto', 'titulo' => 'Cluster GPU Abierto para Tesis', 'docente' => 'Investigación MN', 'grupo' => 'Abierto', 'pcs_uso' => 0],
                    ['hora' => '17:00', 'rango' => '17:00 - 19:00', 'estado' => 'ocupado', 'modalidad' => 'Examen', 'titulo' => 'Evaluación de Cómputo Paralelo CUDA', 'docente' => 'Dr. Gonzalo Ortiz', 'grupo' => 'MN-IA-904', 'pcs_uso' => 18],
                ],
            ],
            [
                'id' => 5,
                'codigo' => 'L-05',
                'etiqueta_mapa' => 'LAB 5',
                'nombre_corto' => 'IoT y Sistemas Embebidos',
                'icono' => 'developer_board',
                'imagen' => '/images/labs/lab-05-embebidos-iot.svg',
                'planta' => 'Planta Baja',
                'edificio' => 'Sector C · Nivel 1',
                'ubicacion' => 'Planta Baja · Ala Derecha',
                'tipo' => 'libre',
                'estado_badge' => 'Disponible',
                'asignatura' => 'Prototipado Abierto ARM / ESP32',
                'docente' => 'Dr. Arturo Vega',
                'grupo' => 'Abierto',
                'canon' => false,
                'canon_estado' => 'Sin Cañón',
                'aforo_max' => 20,
                'horas_libres' => 6,
                'horas_ocupadas' => 6,
                'horas_libres_rangos' => ['09:00 - 11:00', '15:00 - 19:00'],
                'hardware' => 'Intel Core i5-12500, 16GB RAM, Osciloscopio Rigol DS1054Z, Fuente DC Siglent',
                'specs_detalladas' => [
                    'cpu' => 'Intel Core i5-12500 (6 núcleos / 12 hilos @ 4.6 GHz)',
                    'ram' => '16 GB DDR4 3200 MHz Industrial Grade',
                    'gpu' => 'Intel UHD Graphics 770',
                    'almacenamiento' => '512 GB SSD NVMe M.2',
                    'monitor' => 'Monitor 24" FHD en Brazo Articulado para Mesa de Electrónica',
                    'perifericos' => 'Osciloscopio Rigol 4 Canales, Fuente DC Triple, Depurador ST-Link V3',
                    'red' => 'Ethernet Gigabit + Broker MQTT Local en Subred IoT',
                    'os' => 'Ubuntu 24.04 LTS + Toolchain ARM GCC & ESP-IDF',
                ],
                'software' => 'STM32CubeIDE, Wireshark, VS Code, ROS 2 Humble',
                'proximo_cambio' => '11:00 h',
                'mantenimiento' => [5, 14],
                'disponibles' => [1, 3, 4, 6, 8, 10, 13, 15, 16, 18, 20],
                'horarios_horas' => [
                    ['hora' => '07:00', 'rango' => '07:00 - 09:00', 'estado' => 'ocupado', 'modalidad' => 'Práctica', 'titulo' => 'Instrumentación y Electrónica Digital', 'docente' => 'Dr. Arturo Vega', 'grupo' => 'MN-EMB-302', 'pcs_uso' => 16],
                    ['hora' => '09:00', 'rango' => '09:00 - 11:00', 'estado' => 'libre', 'modalidad' => 'Abierto', 'titulo' => 'Bancos de Prototipado IoT Libres', 'docente' => 'Dr. Arturo Vega', 'grupo' => 'Abierto', 'pcs_uso' => 7],
                    ['hora' => '11:00', 'rango' => '11:00 - 13:00', 'estado' => 'ocupado', 'modalidad' => 'Clase', 'titulo' => 'Microcontroladores ARM Cortex-M (STM32)', 'docente' => 'Dr. Arturo Vega', 'grupo' => 'MN-EMB-601', 'pcs_uso' => 18],
                    ['hora' => '13:00', 'rango' => '13:00 - 15:00', 'estado' => 'ocupado', 'modalidad' => 'Práctica', 'titulo' => 'Redes de Sensores y Telemetría MQTT', 'docente' => 'Mtro. Juan Pablo Ruiz', 'grupo' => 'MN-EMB-703', 'pcs_uso' => 17],
                    ['hora' => '15:00', 'rango' => '15:00 - 17:00', 'estado' => 'libre', 'modalidad' => 'Abierto', 'titulo' => 'Taller Abierto de Sistemas Embebidos', 'docente' => 'Guardia Técnica', 'grupo' => 'Abierto', 'pcs_uso' => 0],
                    ['hora' => '17:00', 'rango' => '17:00 - 19:00', 'estado' => 'libre', 'modalidad' => 'Abierto', 'titulo' => 'Taller Abierto de Sistemas Embebidos', 'docente' => 'Guardia Técnica', 'grupo' => 'Abierto', 'pcs_uso' => 0],
                ],
            ],
            [
                'id' => 6,
                'codigo' => 'L-06',
                'etiqueta_mapa' => 'LAB 6',
                'nombre_corto' => 'Cómputo General y Simulación',
                'icono' => 'desktop_windows',
                'imagen' => '/images/labs/lab-06-computo-general.svg',
                'planta' => 'Planta Alta',
                'edificio' => 'Sector C · Nivel 2',
                'ubicacion' => 'Planta Alta · Ala Izquierda',
                'tipo' => 'libre',
                'estado_badge' => 'Disponible',
                'asignatura' => 'Práctica Libre e Investigación',
                'docente' => 'Encargado de Turno',
                'grupo' => 'Comunidad MetalNet',
                'canon' => true,
                'canon_estado' => 'Cañón FHD Activo',
                'aforo_max' => 20,
                'horas_libres' => 8,
                'horas_ocupadas' => 4,
                'horas_libres_rangos' => ['07:00 - 11:00', '15:00 - 19:00'],
                'hardware' => 'Dell OptiPlex 7010, Core i5-13500, 16GB DDR5, SSD NVMe 512GB, 24" IPS',
                'specs_detalladas' => [
                    'cpu' => 'Intel Core i5-13500 (14 núcleos @ 4.8 GHz)',
                    'ram' => '16 GB DDR5 4800 MHz',
                    'gpu' => 'Intel UHD Graphics 770',
                    'almacenamiento' => '512 GB SSD NVMe PCIe 4.0',
                    'monitor' => 'Dell 24" IPS Full HD (1920x1080)',
                    'perifericos' => 'Teclado y Mouse Silenciosos Dell Pro, Audio Frontal',
                    'red' => 'Ethernet Gigabit Campus LAN',
                    'os' => 'Windows 11 Enterprise + WSL2 Ubuntu',
                ],
                'software' => 'MATLAB R2024b, VS Code, PostgreSQL 16',
                'proximo_cambio' => '11:00 h',
                'mantenimiento' => [12],
                'disponibles' => [1, 2, 4, 5, 6, 9, 10, 13, 14, 16, 17, 19, 20],
                'horarios_horas' => [
                    ['hora' => '07:00', 'rango' => '07:00 - 09:00', 'estado' => 'libre', 'modalidad' => 'Abierto', 'titulo' => 'Práctica Libre Matutina', 'docente' => 'Encargado de Turno', 'grupo' => 'Abierto', 'pcs_uso' => 5],
                    ['hora' => '09:00', 'rango' => '09:00 - 11:00', 'estado' => 'libre', 'modalidad' => 'Abierto', 'titulo' => 'Práctica Libre e Investigación', 'docente' => 'Encargado de Turno', 'grupo' => 'Abierto', 'pcs_uso' => 6],
                    ['hora' => '11:00', 'rango' => '11:00 - 13:00', 'estado' => 'ocupado', 'modalidad' => 'Clase', 'titulo' => 'Métodos Numéricos con MATLAB', 'docente' => 'Dra. Laura Sánchez', 'grupo' => 'MN-MAT-402', 'pcs_uso' => 19],
                    ['hora' => '13:00', 'rango' => '13:00 - 15:00', 'estado' => 'ocupado', 'modalidad' => 'Examen', 'titulo' => 'Examen Departamental de Simulación', 'docente' => 'Comité Académico', 'grupo' => 'MN-GEN-500', 'pcs_uso' => 19],
                    ['hora' => '15:00', 'rango' => '15:00 - 17:00', 'estado' => 'libre', 'modalidad' => 'Abierto', 'titulo' => 'Sala Abierta para Alumnos', 'docente' => 'Encargado de Turno', 'grupo' => 'Abierto', 'pcs_uso' => 0],
                    ['hora' => '17:00', 'rango' => '17:00 - 19:00', 'estado' => 'libre', 'modalidad' => 'Abierto', 'titulo' => 'Sala Abierta Vespertina', 'docente' => 'Encargado de Turno', 'grupo' => 'Abierto', 'pcs_uso' => 0],
                ],
            ],
            [
                'id' => 7,
                'codigo' => 'L-07',
                'etiqueta_mapa' => 'LAB 7',
                'nombre_corto' => 'Ciberseguridad y Operaciones SOC',
                'icono' => 'shield_lock',
                'imagen' => '/images/labs/lab-07-ciberseguridad-soc.svg',
                'planta' => 'Planta Alta',
                'edificio' => 'Sector C · Nivel 2',
                'ubicacion' => 'Planta Alta · Ala Izquierda',
                'tipo' => 'clase',
                'estado_badge' => 'En Clase',
                'asignatura' => 'Pentesting y Auditoría Forense',
                'docente' => 'Mtro. Roberto Hernández',
                'grupo' => 'MN-SEC-903',
                'canon' => true,
                'canon_estado' => 'Cañón FHD Activo',
                'aforo_max' => 20,
                'horas_libres' => 6,
                'horas_ocupadas' => 6,
                'horas_libres_rangos' => ['11:00 - 13:00', '15:00 - 19:00'],
                'hardware' => 'Intel Core i7-13700, 32GB DDR5, Dual NIC Gigabit, 1TB NVMe Hardware Encrypted',
                'specs_detalladas' => [
                    'cpu' => 'Intel Core i7-13700 con aceleración criptográfica AES-NI',
                    'ram' => '32 GB DDR5 5600 MHz para laboratorios virtuales SOC',
                    'gpu' => 'NVIDIA T1000 8GB para auditoría de hashes y visualización',
                    'almacenamiento' => '1 TB NVMe con Cifrado OPAL 2.0',
                    'monitor' => 'Monitor 24" IPS Antirreflejante con Filtro de Privacidad',
                    'perifericos' => 'Teclado Mecánico, Adaptador Wi-Fi Alfa Modo Monitor, Llave FIDO2',
                    'red' => 'Red Aislada Sandbox SOC + VLAN de Monitoreo',
                    'os' => 'Kali Linux 2024.3 / Ubuntu 24.04 Hardened',
                ],
                'software' => 'Kali Linux, Wireshark, Cisco Packet Tracer, VMware Workstation',
                'proximo_cambio' => '11:00 h',
                'mantenimiento' => [4, 18],
                'disponibles' => [2, 9, 15, 20],
                'horarios_horas' => [
                    ['hora' => '07:00', 'rango' => '07:00 - 09:00', 'estado' => 'ocupado', 'modalidad' => 'Clase', 'titulo' => 'Criptografía Aplicada y PKI', 'docente' => 'Mtro. Fernando Morales', 'grupo' => 'MN-SEC-704', 'pcs_uso' => 17],
                    ['hora' => '09:00', 'rango' => '09:00 - 11:00', 'estado' => 'ocupado', 'modalidad' => 'Práctica', 'titulo' => 'Pentesting y Auditoría Forense', 'docente' => 'Mtro. Roberto Hernández', 'grupo' => 'MN-SEC-903', 'pcs_uso' => 14],
                    ['hora' => '11:00', 'rango' => '11:00 - 13:00', 'estado' => 'libre', 'modalidad' => 'Abierto', 'titulo' => 'Entorno Sandbox CTF Abierto', 'docente' => 'Guardia SOC', 'grupo' => 'Abierto', 'pcs_uso' => 0],
                    ['hora' => '13:00', 'rango' => '13:00 - 15:00', 'estado' => 'ocupado', 'modalidad' => 'Examen', 'titulo' => 'Evaluación de Respuesta a Incidentes', 'docente' => 'Mtro. Roberto Hernández', 'grupo' => 'MN-SEC-903', 'pcs_uso' => 18],
                    ['hora' => '15:00', 'rango' => '15:00 - 17:00', 'estado' => 'libre', 'modalidad' => 'Abierto', 'titulo' => 'Práctica Libre de Seguridad Ofensiva', 'docente' => 'Guardia SOC', 'grupo' => 'Abierto', 'pcs_uso' => 0],
                    ['hora' => '17:00', 'rango' => '17:00 - 19:00', 'estado' => 'libre', 'modalidad' => 'Abierto', 'titulo' => 'Práctica Libre de Seguridad Ofensiva', 'docente' => 'Guardia SOC', 'grupo' => 'Abierto', 'pcs_uso' => 0],
                ],
            ],
            [
                'id' => 8,
                'codigo' => 'L-08',
                'etiqueta_mapa' => 'LAB 8',
                'nombre_corto' => 'Bases de Datos y Cloud Computing',
                'icono' => 'cloud_done',
                'imagen' => '/images/labs/lab-08-bases-datos-cloud.svg',
                'planta' => 'Planta Alta',
                'edificio' => 'Sector C · Nivel 2',
                'ubicacion' => 'Planta Alta · Centro',
                'tipo' => 'libre',
                'estado_badge' => 'Disponible',
                'asignatura' => 'Clusters Cloud y Bases Distribuidas',
                'docente' => 'Dra. Elena Ramírez',
                'grupo' => 'Abierto',
                'canon' => false,
                'canon_estado' => 'Sin Cañón',
                'aforo_max' => 20,
                'horas_libres' => 8,
                'horas_ocupadas' => 4,
                'horas_libres_rangos' => ['07:00 - 11:00', '15:00 - 19:00'],
                'hardware' => 'AMD Ryzen 5 7600, 32GB DDR5, SSD NVMe 1TB RAID, Monitor 24" IPS',
                'specs_detalladas' => [
                    'cpu' => 'AMD Ryzen 5 7600 (6 núcleos / 12 hilos @ 5.1 GHz)',
                    'ram' => '32 GB DDR5 5200 MHz para nodos Kubernetes y motores SQL',
                    'gpu' => 'AMD Radeon Graphics integrada',
                    'almacenamiento' => '1 TB SSD NVMe de alto IOPS para motores transaccionales',
                    'monitor' => 'Monitor 24" IPS Widescreen (1920x1080)',
                    'perifericos' => 'Teclado Mecánico Compacto, Mouse Óptico',
                    'red' => 'Ethernet 2.5GbE conectada al Cluster Interno MetalNet',
                    'os' => 'Ubuntu Server / Desktop 24.04 LTS',
                ],
                'software' => 'PostgreSQL 16, Docker Desktop, VS Code',
                'proximo_cambio' => '11:00 h',
                'mantenimiento' => [11],
                'disponibles' => [1, 3, 5, 6, 7, 8, 10, 12, 14, 16, 18, 19],
                'horarios_horas' => [
                    ['hora' => '07:00', 'rango' => '07:00 - 09:00', 'estado' => 'libre', 'modalidad' => 'Abierto', 'titulo' => 'Práctica Libre SQL y Modelado', 'docente' => 'Guardia Técnica', 'grupo' => 'Abierto', 'pcs_uso' => 4],
                    ['hora' => '09:00', 'rango' => '09:00 - 11:00', 'estado' => 'libre', 'modalidad' => 'Abierto', 'titulo' => 'Cluster Cloud Abierto', 'docente' => 'Dra. Elena Ramírez', 'grupo' => 'Abierto', 'pcs_uso' => 7],
                    ['hora' => '11:00', 'rango' => '11:00 - 13:00', 'estado' => 'ocupado', 'modalidad' => 'Clase', 'titulo' => 'Bases de Datos Distribuidas y NoSQL', 'docente' => 'Dra. Elena Ramírez', 'grupo' => 'MN-BD-602', 'pcs_uso' => 18],
                    ['hora' => '13:00', 'rango' => '13:00 - 15:00', 'estado' => 'ocupado', 'modalidad' => 'Práctica', 'titulo' => 'Orquestación en Kubernetes y Cloud', 'docente' => 'Dr. Gonzalo Ortiz', 'grupo' => 'MN-CLD-803', 'pcs_uso' => 17],
                    ['hora' => '15:00', 'rango' => '15:00 - 17:00', 'estado' => 'libre', 'modalidad' => 'Abierto', 'titulo' => 'Laboratorio Abierto de Datos', 'docente' => 'Guardia Técnica', 'grupo' => 'Abierto', 'pcs_uso' => 0],
                    ['hora' => '17:00', 'rango' => '17:00 - 19:00', 'estado' => 'libre', 'modalidad' => 'Abierto', 'titulo' => 'Laboratorio Abierto de Datos', 'docente' => 'Guardia Técnica', 'grupo' => 'Abierto', 'pcs_uso' => 0],
                ],
            ],
            [
                'id' => 9,
                'codigo' => 'L-09',
                'etiqueta_mapa' => 'LAB 9',
                'nombre_corto' => 'Multimedia, Motores 3D y VR',
                'icono' => 'view_in_ar',
                'imagen' => '/images/labs/lab-09-multimedia-vr.svg',
                'planta' => 'Planta Alta',
                'edificio' => 'Sector C · Nivel 2',
                'ubicacion' => 'Planta Alta · Centro',
                'tipo' => 'taller',
                'estado_badge' => 'En Práctica',
                'asignatura' => 'Graficación por Computadora y Shaders',
                'docente' => 'Mtro. Daniel Cruz',
                'grupo' => 'MN-VR-705',
                'canon' => true,
                'canon_estado' => 'Cañón 4K Activo',
                'aforo_max' => 20,
                'horas_libres' => 6,
                'horas_ocupadas' => 6,
                'horas_libres_rangos' => ['11:00 - 13:00', '15:00 - 19:00'],
                'hardware' => 'Intel Core i7-13700F, RTX 4060 Ti 16GB, 32GB DDR5, Monitor 27" QHD 144Hz',
                'specs_detalladas' => [
                    'cpu' => 'Intel Core i7-13700F (16 núcleos / 24 hilos @ 5.2 GHz)',
                    'ram' => '32 GB DDR5 6000 MHz para renderizado y escenas 3D',
                    'gpu' => 'NVIDIA GeForce RTX 4060 Ti 16 GB VRAM con Ray Tracing & DLSS 3',
                    'almacenamiento' => '2 TB SSD NVMe Gen4 para texturas y proyectos Unity/Blender',
                    'monitor' => 'Monitor 27" QHD (2560x1440) 144Hz IPS HDR400',
                    'perifericos' => 'Tableta Digitalizadora Wacom, Visor Meta Quest 3 Link, Mouse 3D',
                    'red' => 'Ethernet Gigabit + Wi-Fi 6E para Streaming XR',
                    'os' => 'Windows 11 Pro for Workstations',
                ],
                'software' => 'Unity & Blender, VS Code',
                'proximo_cambio' => '11:00 h',
                'mantenimiento' => [2, 9, 17],
                'disponibles' => [5, 6, 12, 15, 20],
                'horarios_horas' => [
                    ['hora' => '07:00', 'rango' => '07:00 - 09:00', 'estado' => 'ocupado', 'modalidad' => 'Clase', 'titulo' => 'Modelado Poligonal e Iluminación PBR', 'docente' => 'Mtro. Daniel Cruz', 'grupo' => 'MN-VR-504', 'pcs_uso' => 16],
                    ['hora' => '09:00', 'rango' => '09:00 - 11:00', 'estado' => 'ocupado', 'modalidad' => 'Práctica', 'titulo' => 'Graficación por Computadora y Shaders', 'docente' => 'Mtro. Daniel Cruz', 'grupo' => 'MN-VR-705', 'pcs_uso' => 12],
                    ['hora' => '11:00', 'rango' => '11:00 - 13:00', 'estado' => 'libre', 'modalidad' => 'Abierto', 'titulo' => 'Granja de Render y Práctica Libre 3D', 'docente' => 'Guardia Multimedia', 'grupo' => 'Abierto', 'pcs_uso' => 0],
                    ['hora' => '13:00', 'rango' => '13:00 - 15:00', 'estado' => 'ocupado', 'modalidad' => 'Práctica', 'titulo' => 'Entornos Inmersivos y Motores VR', 'docente' => 'Mtro. Daniel Cruz', 'grupo' => 'MN-VR-903', 'pcs_uso' => 17],
                    ['hora' => '15:00', 'rango' => '15:00 - 17:00', 'estado' => 'libre', 'modalidad' => 'Abierto', 'titulo' => 'Estaciones Abiertas Render & XR', 'docente' => 'Guardia Multimedia', 'grupo' => 'Abierto', 'pcs_uso' => 0],
                    ['hora' => '17:00', 'rango' => '17:00 - 19:00', 'estado' => 'libre', 'modalidad' => 'Abierto', 'titulo' => 'Estaciones Abiertas Render & XR', 'docente' => 'Guardia Multimedia', 'grupo' => 'Abierto', 'pcs_uso' => 0],
                ],
            ],
            [
                'id' => 10,
                'codigo' => 'L-10',
                'etiqueta_mapa' => 'LAB 10',
                'nombre_corto' => 'Robótica y Automatización Industrial',
                'icono' => 'precision_manufacturing',
                'imagen' => '/images/labs/lab-10-robotica-industrial.svg',
                'planta' => 'Planta Alta',
                'edificio' => 'Sector C · Nivel 2',
                'ubicacion' => 'Planta Alta · Ala Derecha',
                'tipo' => 'libre',
                'estado_badge' => 'Disponible',
                'asignatura' => 'Cinemática con ROS 2 y Control PLC',
                'docente' => 'Mtro. Juan Pablo Ruiz',
                'grupo' => 'Abierto',
                'canon' => false,
                'canon_estado' => 'Sin Cañón',
                'aforo_max' => 20,
                'horas_libres' => 8,
                'horas_ocupadas' => 4,
                'horas_libres_rangos' => ['07:00 - 11:00', '15:00 - 19:00'],
                'hardware' => 'Intel Core i5-12500, 16GB RAM, Estación PLC Siemens S7-1200, Brazos 6-DOF',
                'specs_detalladas' => [
                    'cpu' => 'Intel Core i5-12500 (6 núcleos / 12 hilos @ 4.6 GHz)',
                    'ram' => '16 GB DDR4 3200 MHz para simulación Gazebo y TIA Portal',
                    'gpu' => 'NVIDIA T600 4GB para visualización cinemática 3D',
                    'almacenamiento' => '1 TB SSD NVMe M.2',
                    'monitor' => 'Monitor Industrial 24" FHD Antirreflejante',
                    'perifericos' => 'Interfaz PROFINET Siemens S7, Brazo Manipulador 6-DOF',
                    'red' => 'Doble Red: LAN Académica + Red Industrial PROFINET',
                    'os' => 'Ubuntu 22.04 LTS (ROS 2 Humble) / Windows 11 Pro',
                ],
                'software' => 'ROS 2 Humble, MATLAB R2024b, STM32CubeIDE',
                'proximo_cambio' => '11:00 h',
                'mantenimiento' => [16],
                'disponibles' => [1, 2, 3, 4, 7, 8, 10, 11, 13, 15, 18, 19, 20],
                'horarios_horas' => [
                    ['hora' => '07:00', 'rango' => '07:00 - 09:00', 'estado' => 'libre', 'modalidad' => 'Abierto', 'titulo' => 'Celdas de Manufactura Abiertas', 'docente' => 'Guardia Robótica', 'grupo' => 'Abierto', 'pcs_uso' => 4],
                    ['hora' => '09:00', 'rango' => '09:00 - 11:00', 'estado' => 'libre', 'modalidad' => 'Abierto', 'titulo' => 'Práctica Libre Simulación ROS 2', 'docente' => 'Mtro. Juan Pablo Ruiz', 'grupo' => 'Abierto', 'pcs_uso' => 6],
                    ['hora' => '11:00', 'rango' => '11:00 - 13:00', 'estado' => 'ocupado', 'modalidad' => 'Clase', 'titulo' => 'Control PID y Dinámica de Manipuladores', 'docente' => 'Mtro. Juan Pablo Ruiz', 'grupo' => 'MN-ROB-803', 'pcs_uso' => 18],
                    ['hora' => '13:00', 'rango' => '13:00 - 15:00', 'estado' => 'ocupado', 'modalidad' => 'Práctica', 'titulo' => 'Automatización con PLC y Redes PROFINET', 'docente' => 'Mtro. Juan Pablo Ruiz', 'grupo' => 'MN-ROB-901', 'pcs_uso' => 19],
                    ['hora' => '15:00', 'rango' => '15:00 - 17:00', 'estado' => 'libre', 'modalidad' => 'Abierto', 'titulo' => 'Práctica Libre Robótica Móvil', 'docente' => 'Guardia Robótica', 'grupo' => 'Abierto', 'pcs_uso' => 0],
                    ['hora' => '17:00', 'rango' => '17:00 - 19:00', 'estado' => 'libre', 'modalidad' => 'Abierto', 'titulo' => 'Práctica Libre Robótica Móvil', 'docente' => 'Guardia Robótica', 'grupo' => 'Abierto', 'pcs_uso' => 0],
                ],
            ],
        ];

        $labs = [];
        foreach ($base as $lab) {
            $lab['nombre'] = "{$lab['etiqueta_mapa']} ({$lab['codigo']}) · {$lab['nombre_corto']}";
            $lab['especialidad'] = $lab['nombre_corto'];
            $lab['red'] = '192.168.' . $lab['id'] . '0.0/24';
            $lab['imagen_banner'] = asset(ltrim($lab['imagen'], '/'));
            $lab['hw_detallado'] = $lab['specs_detalladas'] ?? [];
            $lab['hardware_lista'] = array_map('trim', explode(',', $lab['hardware']));
            $lab['software_lista'] = array_map('trim', explode(',', $lab['software']));
            $lab['hardware_corto'] = implode(' · ', array_slice($lab['hardware_lista'], 0, 2));
            $lab['software_chips'] = $lab['software_lista'];

            $resActiva = $activeReservas[$lab['id']] ?? null;
            $tieneClaseOExamen = false;
            $tituloSesionDocente = null;

            if ($resActiva && in_array(strtolower($resActiva->modalidad), ['clase', 'examen', 'práctica', 'practica'], true)) {
                $docenteName = $resActiva->usuario ? $resActiva->usuario->name : ($lab['docente'] ?? 'Docente Titular');
                $lab['estado_badge'] = "{$resActiva->modalidad}: {$resActiva->materia} · {$docenteName}";
                $lab['tipo'] = 'clase';
                $lab['asignatura'] = $resActiva->materia;
                $lab['docente'] = $docenteName;
                $lab['grupo'] = $resActiva->grupo;
                $tieneClaseOExamen = true;
                $tituloSesionDocente = "{$resActiva->modalidad}: {$resActiva->materia} · {$docenteName}";
            } elseif (
                in_array(strtolower($lab['tipo'] ?? ''), ['clase', 'taller'], true) ||
                str_contains(strtolower($lab['estado_badge'] ?? ''), 'clase') ||
                str_contains(strtolower($lab['estado_badge'] ?? ''), 'práctica') ||
                str_contains(strtolower($lab['estado_badge'] ?? ''), 'practica') ||
                str_contains(strtolower($lab['estado_badge'] ?? ''), 'examen')
            ) {
                $tieneClaseOExamen = true;
                $tituloSesionDocente = "En Clase: {$lab['asignatura']} · {$lab['docente']}";
            }

            $lab['en_sesion_docente'] = $tieneClaseOExamen;
            $lab['titulo_sesion_docente'] = $tituloSesionDocente;

            $lab['pcs'] = self::generarEstaciones($lab, $dbStates, $resActiva, $tieneClaseOExamen, $tituloSesionDocente);

            $conteo = ['disponible' => 0, 'ocupado' => 0, 'mantenimiento' => 0];
            foreach ($lab['pcs'] as $pc) {
                $st = $pc['estado'];
                if (!isset($conteo[$st])) {
                    $st = 'disponible';
                }
                $conteo[$st]++;
            }

            if ($tieneClaseOExamen) {
                $conteo['disponible'] = 0;
                $conteo['ocupado'] = count($lab['pcs']) - $conteo['mantenimiento'];
            }

            $lab['conteo'] = $conteo;
            $lab['capacidad'] = count($lab['pcs']);
            $lab['aforo_total'] = count($lab['pcs']);
            $lab['aforo_ocupado'] = $conteo['ocupado'];
            $lab['porcentaje'] = (int) round(($conteo['ocupado'] / max(1, count($lab['pcs']))) * 100);

            [$lab['badge_color'], $lab['dot_color']] = match ($lab['tipo']) {
                'libre' => ['bg-[#DCFCE7] text-[#16512C] border border-[#316A42]/35', 'bg-[#16A34A]'],
                'taller' => ['bg-[#FEF3C7] text-[#854D0E] border border-[#EAB308]/60', 'bg-[#EAB308]'],
                default => ['bg-[#FEF3C7] text-[#854D0E] border border-[#EAB308]/60', 'bg-[#EAB308]'],
            };

            unset($lab['mantenimiento'], $lab['disponibles']);
            $lab['total'] = $lab['capacidad'];
            $lab['libres'] = $conteo['disponible'];
            $lab['ocupadas'] = $conteo['ocupado'];
            $lab['mantenimiento'] = $conteo['mantenimiento'];
            $lab['estado_general'] = $tieneClaseOExamen ? ($lab['estado_badge'] ?? 'En Clase') : ($lab['tipo'] === 'libre' ? 'Disponible' : 'En Clase');
            $lab['horarios_libres'] = $lab['horas_libres_rangos'] ?? [];
            $labs[] = $lab;
        }

        return self::$labsCache = $labs;
    }

    /**
     * Busca un laboratorio por su id o código (L-01 a L-10).
     */
    public static function buscar(int|string|null $clave): ?array
    {
        if ($clave === null || $clave === '') {
            return null;
        }
        foreach (self::laboratorios() as $lab) {
            if (
                (string) $lab['id'] === (string) $clave ||
                strcasecmp($lab['codigo'], (string) $clave) === 0 ||
                strcasecmp($lab['etiqueta_mapa'], (string) $clave) === 0
            ) {
                return $lab;
            }
        }
        return null;
    }

    /**
     * Totales globales de los 10 laboratorios (200 PCs).
     */
    public static function totales(): array
    {
        $tot = [
            'disponible' => 0,
            'ocupado' => 0,
            'mantenimiento' => 0,
            'total' => 0,
            'salas' => 0,
            'con_canon' => 0,
            'sin_canon' => 0,
            'horas_libres' => 0,
            'horas_ocupadas' => 0,
        ];
        foreach (self::laboratorios() as $lab) {
            foreach ($lab['conteo'] as $estado => $n) {
                $tot[$estado] += $n;
                $tot['total'] += $n;
            }
            $tot['salas']++;
            if ($lab['canon']) {
                $tot['con_canon']++;
            } else {
                $tot['sin_canon']++;
            }
            $tot['horas_libres'] += $lab['horas_libres'];
            $tot['horas_ocupadas'] += $lab['horas_ocupadas'];
        }
        $tot['labs'] = $tot['salas'];
        $tot['operativo'] = $tot['total'] > 0
            ? (int) round((($tot['total'] - $tot['mantenimiento']) / $tot['total']) * 100)
            : 100;
        return $tot;
    }

    /**
     * Datos estructurados para JavaScript global.
     */
    public static function paraJs(): array
    {
        return array_map(function ($lab) {
            return [
                'id' => $lab['id'],
                'codigo' => $lab['codigo'],
                'etiqueta' => $lab['etiqueta_mapa'],
                'nombre' => $lab['nombre'],
                'corto' => $lab['nombre_corto'],
                'icono' => $lab['icono'],
                'imagen' => $lab['imagen'],
                'planta' => $lab['planta'],
                'ubicacion' => $lab['ubicacion'],
                'badge' => $lab['estado_badge'],
                'asignatura' => $lab['asignatura'],
                'docente' => $lab['docente'],
                'grupo' => $lab['grupo'],
                'tipo' => $lab['tipo'],
                'canon' => $lab['canon'],
                'canon_estado' => $lab['canon_estado'],
                'horas_libres' => $lab['horas_libres'],
                'horas_ocupadas' => $lab['horas_ocupadas'],
                'horas_libres_rangos' => $lab['horas_libres_rangos'],
                'horarios' => $lab['horarios_horas'],
                'hardware' => $lab['hardware_lista'],
                'specs_detalladas' => $lab['specs_detalladas'],
                'hw_detallado' => $lab['specs_detalladas'],
                'software' => $lab['software_lista'],
                'conteo' => $lab['conteo'],
                'libres' => $lab['libres'],
                'ocupadas' => $lab['ocupadas'],
                'mantenimiento' => $lab['mantenimiento'],
                'en_sesion_docente' => $lab['en_sesion_docente'] ?? false,
                'titulo_sesion_docente' => $lab['titulo_sesion_docente'] ?? null,
                'pcs' => array_map(fn ($pc) => [
                    'id' => $pc['id'],
                    'numero' => $pc['numero'],
                    'codigo' => $pc['codigo'],
                    'inventario' => $pc['inventario'],
                    'estado' => $pc['estado'],
                    'estado_label' => $pc['estado_label'],
                    'ip' => $pc['ip'],
                    'detalle' => $pc['usuario'],
                    'averia_detalle' => $pc['averia_detalle'],
                    'imagen_estado' => $pc['imagen_estado'],
                    'hw' => $pc['specs_detalladas'],
                    'sw_versiones' => $lab['software_lista'],
                ], $lab['pcs']),
            ];
        }, self::laboratorios());
    }

    /**
     * Genera PC-01 a PC-20 por laboratorio, sincronizando con la tabla estaciones si existe
     * y aplicando la ocupación total si existe una Clase o Examen activo.
     */
    private static function generarEstaciones(
        array $lab,
        array $dbStates = [],
        ?ReservaLaboratorio $reservaActiva = null,
        bool $esClaseOExamen = false,
        ?string $tituloSesionDocente = null
    ): array {
        $pcs = [];
        for ($i = 1; $i <= 20; $i++) {
            $num = str_pad((string) $i, 2, '0', STR_PAD_LEFT);
            $codigo = "PC-{$num}";
            $dbKey = $lab['id'] . ':' . $codigo;

            if (isset($dbStates[$dbKey])) {
                $rawState = strtolower(trim($dbStates[$dbKey]['estado']));
                if (in_array($rawState, ['mantenimiento', 'no_disponible', 'averia'], true)) {
                    $estado = 'mantenimiento';
                } elseif (in_array($rawState, ['ocupado', 'ocupada', 'en_uso', 'reservado'], true)) {
                    $estado = 'ocupado';
                } else {
                    $estado = 'disponible';
                }
            } else {
                if (in_array($i, $lab['mantenimiento'], true)) {
                    $estado = 'mantenimiento';
                } elseif (in_array($i, $lab['disponibles'], true)) {
                    $estado = 'disponible';
                } else {
                    $estado = 'ocupado';
                }
            }

            // REGLA ESTRICTA: Si hay Clase, Práctica o Examen docente activo,
            // todas las computadoras operativas pasan obligatoriamente a OCUPADO (#F59E0B).
            // Si la PC está en avería técnica (mantenimiento), se mantiene en No disponible (#EF4444),
            // pero NINGUNA PC puede aparecer como Disponible (Verde) durante la clase.
            if ($esClaseOExamen && $estado !== 'mantenimiento') {
                $estado = 'ocupado';
            }

            if ($estado === 'mantenimiento') {
                $label = 'No Disponible';
                $usuario = 'No disponible temporalmente · En diagnóstico técnico';
                $averiaDetalle = $dbStates[$dbKey]['ubicacion'] ?? 'En revisión técnica por diagnóstico de hardware/red';
                $imgEstado = '/images/equipos/pc-no-disponible.svg';
            } elseif ($estado === 'disponible') {
                $label = 'Libre';
                $usuario = 'Disponible para reserva inmediata';
                $averiaDetalle = null;
                $imgEstado = '/images/equipos/pc-libre.svg';
            } else {
                $label = 'Ocupada';
                if ($esClaseOExamen) {
                    $usuario = $tituloSesionDocente ?? "En Clase: {$lab['asignatura']} · {$lab['docente']}";
                } else {
                    $usuario = ($dbStates[$dbKey]['ubicacion'] ?? ('En sesión activa · Cambio ' . $lab['proximo_cambio']));
                }
                $averiaDetalle = null;
                $imgEstado = '/images/equipos/pc-ocupada.svg';
            }

            $pcs[] = [
                'id' => "LAB{$lab['id']}-PC{$num}",
                'numero' => $num,
                'codigo' => $codigo,
                'inventario' => sprintf('MN-INV-%02d-%03d', $lab['id'], $i),
                'estado' => $estado,
                'estado_label' => $label,
                'imagen_estado' => $imgEstado,
                'ip' => "10.24.{$lab['id']}0." . (100 + $i),
                'usuario' => $usuario,
                'averia_detalle' => $averiaDetalle,
                'hardware' => $lab['hardware'],
                'specs_detalladas' => $lab['specs_detalladas'],
                'software' => $lab['software'],
                'lab_nombre' => $lab['nombre'],
                'lab_codigo' => $lab['codigo'],
                'lab_ubicacion' => $lab['ubicacion'],
                'lab_id' => $lab['id'],
            ];
        }
        return $pcs;
    }

    /**
     * Solicitudes pendientes de autorización para el Admin / Encargado (combina DB + semillas).
     */
    public static function solicitudesPendientes(): array
    {
        $items = [];

        try {
            if (Schema::hasTable('procesos')) {
                $procesos = Proceso::with(['usuario', 'laboratorio'])
                    ->orderByDesc('created_at')
                    ->limit(15)
                    ->get();

                foreach ($procesos as $proc) {
                    $items[] = [
                        'id' => 'PROC-' . $proc->id,
                        'db_id' => $proc->id,
                        'origen' => 'proceso',
                        'tipo' => $proc->tipo,
                        'solicitante' => $proc->usuario->name ?? 'Usuario MetalNet',
                        'matricula' => $proc->usuario->matricula ?? ($proc->usuario->email ?? 'MN-USR'),
                        'lab' => $proc->laboratorio ? ('L-' . str_pad((string) $proc->laboratorio->id, 2, '0', STR_PAD_LEFT)) : 'L-01',
                        'equipo' => $proc->tipo,
                        'horario' => $proc->created_at ? $proc->created_at->format('d/m H:i') : 'Hoy',
                        'hora' => $proc->created_at ? $proc->created_at->format('H:i') : 'Hoy',
                        'motivo' => $proc->descripcion ?: 'Solicitud registrada en plataforma',
                        'estado' => $proc->estado,
                        'hace' => $proc->created_at ? $proc->created_at->diffForHumans() : 'Reciente',
                        'prioridad' => str_contains(strtolower($proc->tipo), 'actualizaci') || str_contains(strtolower($proc->tipo), 'software') ? 'alta' : 'media',
                    ];
                }
            }
        } catch (\Throwable $e) {
            // Fallback
        }

        $defaults = [
            ['id' => 'SOL-101', 'db_id' => null, 'origen' => 'seed', 'tipo' => 'Reserva de Sala (Examen)', 'solicitante' => 'Mtro. Fernando Morales', 'matricula' => 'MN-DOC-0412', 'lab' => 'L-01', 'equipo' => 'Sala completa (20 PCs)', 'horario' => 'Hoy 13:00 – 15:00', 'hora' => '13:00 – 15:00', 'motivo' => 'Evaluación Práctica Ruteo WAN', 'estado' => 'pendiente', 'hace' => '4 min', 'prioridad' => 'alta'],
            ['id' => 'SOL-102', 'db_id' => null, 'origen' => 'seed', 'tipo' => 'Préstamo Hardware / Cañón', 'solicitante' => 'Dra. Laura Sánchez', 'matricula' => 'MN-DOC-0198', 'lab' => 'L-02', 'equipo' => 'Proyector Laser 4K Portátil', 'horario' => 'Hoy 11:00 – 13:00', 'hora' => '11:00 – 13:00', 'motivo' => 'Sesión de Kernels en L-02 (sala sin cañón fijo)', 'estado' => 'pendiente', 'hace' => '8 min', 'prioridad' => 'critica'],
            ['id' => 'SOL-103', 'db_id' => null, 'origen' => 'seed', 'tipo' => 'Actualización Software', 'solicitante' => 'Dr. Arturo Vega', 'matricula' => 'MN-DOC-0310', 'lab' => 'L-05', 'equipo' => 'STM32CubeIDE v1.17.0 en PC-01..20', 'horario' => 'Mañana 07:00', 'hora' => '07:00 – 09:00', 'motivo' => 'Soporte para nuevos paquetes HAL ARM Cortex-M', 'estado' => 'pendiente', 'hace' => '15 min', 'prioridad' => 'alta'],
            ['id' => 'SOL-104', 'db_id' => null, 'origen' => 'seed', 'tipo' => 'Reserva Estación GPU', 'solicitante' => 'Alumno Ingeniería TI', 'matricula' => 'MN-ALU-2026', 'lab' => 'L-04', 'equipo' => 'PC-07 · RTX 4070', 'horario' => 'Hoy 13:00 – 15:00', 'hora' => '13:00 – 15:00', 'motivo' => 'Entrenamiento de red neuronal con PyTorch 2.4', 'estado' => 'pendiente', 'hace' => '22 min', 'prioridad' => 'media'],
        ];

        return array_merge($items, $defaults);
    }

    /**
     * Tareas y bitácora técnica del Admin clasificadas por prioridad.
     */
    public static function tareasAdmin(): array
    {
        return [
            ['id' => 'TAR-01', 'prioridad' => 'critica', 'lab' => 'L-01', 'pc' => 'PC-10, PC-20', 'equipo' => 'PC-10, PC-20', 'sla' => '30 min', 'titulo' => 'Reemplazo de módulo transceptor SFP+ y diagnóstico NIC en PC-10 y PC-20', 'categoria' => 'Hardware'],
            ['id' => 'TAR-02', 'prioridad' => 'critica', 'lab' => 'L-02', 'pc' => 'PC-07', 'equipo' => 'PC-07', 'sla' => '45 min', 'titulo' => 'Reparación de arranque UEFI y asignación de cañón portátil a L-02', 'categoria' => 'Sistemas'],
            ['id' => 'TAR-03', 'prioridad' => 'alta', 'lab' => 'L-04', 'pc' => 'PC-03, PC-15', 'equipo' => 'PC-03, PC-15', 'sla' => '2 hrs', 'titulo' => 'Actualización de driver NVIDIA CUDA 12.4 y validación térmica de GPU RTX 4070', 'categoria' => 'Software'],
            ['id' => 'TAR-04', 'prioridad' => 'alta', 'lab' => 'L-07', 'pc' => 'PC-04, PC-18', 'equipo' => 'PC-04, PC-18', 'sla' => '2 hrs', 'titulo' => 'Restaurar snapshot limpio de Kali Linux tras auditoría forense SOC', 'categoria' => 'Seguridad'],
            ['id' => 'TAR-05', 'prioridad' => 'media', 'lab' => 'L-09', 'pc' => 'PC-02, PC-09, PC-17', 'equipo' => 'PC-02, PC-09, PC-17', 'sla' => 'Hoy', 'titulo' => 'Calibración de sensores XR Meta Quest 3 y actualización a Blender 4.2 LTS', 'categoria' => 'Periféricos'],
            ['id' => 'TAR-06', 'prioridad' => 'baja', 'lab' => 'L-08', 'pc' => 'PC-11', 'equipo' => 'PC-11', 'sla' => 'Mañana', 'titulo' => 'Mantenimiento preventivo de fuente ATX y limpieza de filtros en L-08', 'categoria' => 'Mantenimiento'],
        ];
    }

    /**
     * Catálogo de software homologado con logotipos vectoriales oficiales locales,
     * comparativa de versión instalada vs versión oficial más reciente y requisitos técnicos.
     */
    public static function software(): array
    {
        return [
            [
                'id' => 'sw-packet-tracer',
                'nombre' => 'Cisco Packet Tracer',
                'version' => '8.2.2',
                'version_oficial' => '8.2.2',
                'estado_version' => 'Actualizado',
                'fabricante' => 'Cisco Systems',
                'categoria' => 'Redes',
                'logo' => '/images/software/cisco-packet-tracer.svg',
                'licencia' => 'Cisco NetAcad Institucional',
                'puestos' => 'Ilimitada (200 PCs)',
                'vigencia' => 'Diciembre 2027',
                'descripcion' => 'Simulador oficial de topologías de red CCNA/CCNP, ruteo OSPF/BGP, VLANs y controladores SDN.',
                'requisitos' => [
                    'cpu' => 'Intel Core i5 / AMD Ryzen 5 (64-bit, 4+ núcleos)',
                    'ram' => '8 GB RAM mínimo (16 GB recomendado para topologías multiárea)',
                    'gpu' => 'Gráficos integrados FHD con aceleración OpenGL 3.3',
                    'disco' => '2.5 GB de espacio libre en SSD NVMe',
                ],
                'modulos' => ['Multiuser WAN Peers', 'IoT Smart Devices Pack', 'REST API SDN Controller', 'PT Activity Grader'],
                'labs' => ['L-01', 'L-07'],
            ],
            [
                'id' => 'sw-wireshark',
                'nombre' => 'Wireshark Network Analyzer',
                'version' => '4.2.4',
                'version_oficial' => '4.4.1',
                'estado_version' => 'Actualización disponible',
                'fabricante' => 'Wireshark Foundation',
                'categoria' => 'Redes',
                'logo' => '/images/software/wireshark.svg',
                'licencia' => 'GPLv2 Open Source',
                'puestos' => 'Ilimitada',
                'vigencia' => 'Permanente',
                'descripcion' => 'Analizador de protocolos de red en tiempo real para inspección profunda de tramas, auditoría SOC y depuración VoIP/TLS.',
                'requisitos' => [
                    'cpu' => 'Procesador x86-64 de 4 núcleos @ 2.5 GHz+',
                    'ram' => '16 GB RAM para capturas PCAP de alto tráfico',
                    'gpu' => 'Resolución 1920x1080 IPS',
                    'disco' => '10 GB libres para buffers de captura PCAPNG',
                ],
                'modulos' => ['Npcap / libpcap Driver', 'TShark CLI', 'USBcap', 'Extcap Bluetooth & Wi-Fi Monitor'],
                'labs' => ['L-01', 'L-05', 'L-07'],
            ],
            [
                'id' => 'sw-vscode',
                'nombre' => 'Visual Studio Code',
                'version' => '1.94.2',
                'version_oficial' => '1.94.2',
                'estado_version' => 'Actualizado',
                'fabricante' => 'Microsoft Corp.',
                'categoria' => 'Desarrollo',
                'logo' => '/images/software/vscode.svg',
                'licencia' => 'MIT Open Source',
                'puestos' => 'Ilimitada (200 PCs)',
                'vigencia' => 'Permanente',
                'descripcion' => 'Entorno de desarrollo integrado ligero con soporte para contenedores remotos, depuración multi-lenguaje y Git integrado.',
                'requisitos' => [
                    'cpu' => 'Intel Core i5 / Ryzen 5 multi-hilo',
                    'ram' => '8 GB RAM (16 GB con Remote Containers)',
                    'gpu' => 'Aceleración por hardware WebGL/Canvas',
                    'disco' => '4 GB libres con extensiones preinstaladas',
                ],
                'modulos' => ['C/C++ & CMake Tools', 'Python & Jupyter Extension', 'Docker & Dev Containers', 'ESLint + Prettier + PHP Intelephense'],
                'labs' => ['L-03', 'L-04', 'L-05', 'L-06', 'L-08', 'L-09'],
            ],
            [
                'id' => 'sw-docker',
                'nombre' => 'Docker Desktop & Engine',
                'version' => '4.33.1',
                'version_oficial' => '4.34.3',
                'estado_version' => 'Actualización disponible',
                'fabricante' => 'Docker Inc.',
                'categoria' => 'Desarrollo',
                'logo' => '/images/software/docker.svg',
                'licencia' => 'Educativa Institucional',
                'puestos' => '80 Estaciones',
                'vigencia' => 'Noviembre 2027',
                'descripcion' => 'Plataforma de contenedorización, Docker Compose y clústeres locales Kubernetes para arquitecturas de microservicios.',
                'requisitos' => [
                    'cpu' => '64-bit con soporte de virtualización hardware VT-x / AMD-V',
                    'ram' => '16 GB RAM mínimo (32 GB instalado en L-03 y L-08)',
                    'gpu' => 'Soporte NVIDIA Container Toolkit opcional',
                    'disco' => '40 GB en SSD NVMe para imágenes y volúmenes',
                ],
                'modulos' => ['Docker Compose v2', 'Kubernetes Single-Node Cluster', 'BuildKit Engine', 'Docker Scout CLI'],
                'labs' => ['L-02', 'L-03', 'L-04', 'L-08'],
            ],
            [
                'id' => 'sw-pytorch',
                'nombre' => 'PyTorch + NVIDIA CUDA',
                'version' => '2.4.1 + CUDA 12.4',
                'version_oficial' => '2.4.1 + CUDA 12.4',
                'estado_version' => 'Actualizado',
                'fabricante' => 'Linux Foundation / NVIDIA',
                'categoria' => 'IA y Datos',
                'logo' => '/images/software/pytorch.svg',
                'licencia' => 'BSD-3 Open Source',
                'puestos' => '20 Nodos GPU',
                'vigencia' => 'Permanente',
                'descripcion' => 'Framework de Deep Learning acelerado por Tensor Cores en tarjetas NVIDIA RTX 4070 para visión artificial y LLMs.',
                'requisitos' => [
                    'cpu' => 'Intel Core i9-13900K (24 núcleos)',
                    'ram' => '64 GB DDR5 RAM',
                    'gpu' => 'NVIDIA GeForce RTX 4070 12 GB GDDR6X (CUDA Compute 8.9)',
                    'disco' => '80 GB NVMe para pesos de modelos y datasets',
                ],
                'modulos' => ['cuDNN 9.1', 'TorchVision & TorchAudio', 'JupyterLab + TensorBoard', 'HuggingFace Transformers'],
                'labs' => ['L-04'],
            ],
            [
                'id' => 'sw-matlab',
                'nombre' => 'MATLAB & Simulink R2024b',
                'version' => 'R2024a',
                'version_oficial' => 'R2024b',
                'estado_version' => 'Actualización disponible',
                'fabricante' => 'MathWorks',
                'categoria' => 'Ciencia',
                'logo' => '/images/software/matlab.svg',
                'licencia' => 'Campus-Wide Total Headcount',
                'puestos' => 'Ilimitada',
                'vigencia' => 'Agosto 2027',
                'descripcion' => 'Entorno de cálculo numérico, procesamiento de señales, sistemas de control PID y modelado multifísico en Simulink.',
                'requisitos' => [
                    'cpu' => 'Intel Core i5 / i7 con soporte AVX2',
                    'ram' => '16 GB RAM (32 GB para Simulink Parallel Computing)',
                    'gpu' => 'GPU compatible con OpenGL 4.2 / CUDA para Parallel Toolbox',
                    'disco' => '28 GB en SSD NVMe',
                ],
                'modulos' => ['Simulink Control Design', 'Deep Learning Toolbox', 'Robotics System Toolbox', 'Signal Processing Toolbox'],
                'labs' => ['L-04', 'L-06', 'L-10'],
            ],
            [
                'id' => 'sw-stm32',
                'nombre' => 'STM32CubeIDE & Toolchain',
                'version' => '1.16.0',
                'version_oficial' => '1.16.1',
                'estado_version' => 'Actualización disponible',
                'fabricante' => 'STMicroelectronics',
                'categoria' => 'Embebidos',
                'logo' => '/images/software/stm32.svg',
                'licencia' => 'ST SLA0048 Freeware',
                'puestos' => 'Ilimitada',
                'vigencia' => 'Permanente',
                'descripcion' => 'Plataforma integral de configuración de pines, generación de código HAL/LL y depuración JTAG/SWD para ARM Cortex-M.',
                'requisitos' => [
                    'cpu' => 'Intel Core i5 @ 3.0 GHz+',
                    'ram' => '8 GB RAM mínimo (16 GB instalado)',
                    'gpu' => 'Gráficos Full HD 1080p',
                    'disco' => '8 GB libres para paquetes de firmware STM32CubeF4/H7',
                ],
                'modulos' => ['STM32CubeMX Pinout Configurator', 'GNU Tools for STM32 (ARM GCC)', 'ST-LINK GDB Server', 'FreeRTOS Middleware'],
                'labs' => ['L-05', 'L-10'],
            ],
            [
                'id' => 'sw-vmware',
                'nombre' => 'VMware Workstation Pro',
                'version' => '17.5.2 Pro',
                'version_oficial' => '17.6.0 Pro',
                'estado_version' => 'Actualización disponible',
                'fabricante' => 'Broadcom / VMware',
                'categoria' => 'Sistemas',
                'logo' => '/images/software/vmware.svg',
                'licencia' => 'Académica Institucional',
                'puestos' => '40 Estaciones',
                'vigencia' => 'Diciembre 2027',
                'descripcion' => 'Hipervisor de tipo 2 con soporte para redes virtuales aisladas, snapshots múltiples y laboratorios de ciberseguridad.',
                'requisitos' => [
                    'cpu' => 'Intel Core i5/i7 con soporte VT-x y EPT habilitado en BIOS',
                    'ram' => '32 GB RAM para ejecución simultánea de 3+ máquinas virtuales',
                    'gpu' => 'Soporte DirectX 11 / OpenGL 4.1 3D Accelerated Guest',
                    'disco' => '120 GB libres en NVMe para discos VMDK',
                ],
                'modulos' => ['Virtual Network Editor (VMnet)', 'Snapshot Manager', 'vSphere Remote Connect', 'OVFTool CLI'],
                'labs' => ['L-02', 'L-07'],
            ],
            [
                'id' => 'sw-unity-blender',
                'nombre' => 'Unity Engine & Blender 3D',
                'version' => 'Unity 2023.2 / Blender 4.2 LTS',
                'version_oficial' => 'Unity 6 / Blender 4.2 LTS',
                'estado_version' => 'Actualizado',
                'fabricante' => 'Unity Technologies / Blender Found.',
                'categoria' => 'Multimedia',
                'logo' => '/images/software/unity-blender.svg',
                'licencia' => 'Educativa Grant / GPL',
                'puestos' => '20 Estaciones RTX',
                'vigencia' => 'Octubre 2027',
                'descripcion' => 'Suite completa de modelado 3D, esculpido, motores de render Cycles/EEVEE y desarrollo de experiencias interactivas XR/VR.',
                'requisitos' => [
                    'cpu' => 'Intel Core i7-13700F (16 núcleos)',
                    'ram' => '32 GB DDR5 RAM',
                    'gpu' => 'NVIDIA GeForce RTX 4060 Ti 16 GB con OptiX Ray Tracing',
                    'disco' => '60 GB libres en unidad NVMe PCIe 4.0',
                ],
                'modulos' => ['OpenXR Plugin (Meta Quest 3)', 'Universal Render Pipeline (URP)', 'Blender Cycles OptiX Denoiser', 'FBX & glTF 2.0 Pipeline'],
                'labs' => ['L-09'],
            ],
            [
                'id' => 'sw-postgresql',
                'nombre' => 'PostgreSQL + pgAdmin 4',
                'version' => '16.4',
                'version_oficial' => '17.0',
                'estado_version' => 'Actualización disponible',
                'fabricante' => 'PostgreSQL Global Dev Group',
                'categoria' => 'Datos y Cloud',
                'logo' => '/images/software/postgresql.svg',
                'licencia' => 'PostgreSQL License (Open Source)',
                'puestos' => 'Ilimitada',
                'vigencia' => 'Permanente',
                'descripcion' => 'Sistema gestor de bases de datos relacional-objeto de alto rendimiento con soporte JSONB, replicación lógica y PostGIS.',
                'requisitos' => [
                    'cpu' => 'AMD Ryzen 5 / 7 multi-core',
                    'ram' => '16 GB a 32 GB RAM para shared_buffers y tuning de consultas',
                    'gpu' => 'No requerida',
                    'disco' => '20 GB en SSD NVMe de baja latencia',
                ],
                'modulos' => ['pgAdmin 4 Web/Desktop', 'PostGIS Spatial Extension', 'pg_stat_statements Profiler', 'pgvector para Embeddings IA'],
                'labs' => ['L-03', 'L-06', 'L-08'],
            ],
            [
                'id' => 'sw-kali',
                'nombre' => 'Kali Linux Security Suite',
                'version' => '2024.3',
                'version_oficial' => '2024.3',
                'estado_version' => 'Actualizado',
                'fabricante' => 'OffSec',
                'categoria' => 'Seguridad',
                'logo' => '/images/software/kali-linux.svg',
                'licencia' => 'GPL Open Source',
                'puestos' => '20 Estaciones SOC',
                'vigencia' => 'Permanente',
                'descripcion' => 'Distribución especializada en auditoría de seguridad, análisis forense digital, ingeniería inversa y pruebas de penetración.',
                'requisitos' => [
                    'cpu' => 'Intel Core i7-13700 con virtualización activa',
                    'ram' => '32 GB DDR5 RAM',
                    'gpu' => 'Aceleración OpenCL para auditoría criptográfica',
                    'disco' => '80 GB en partición cifrada LUKS',
                ],
                'modulos' => ['Metasploit Framework', 'Burp Suite Community', 'Nmap & Zenmap', 'Autopsy Digital Forensics + Wazuh Agent'],
                'labs' => ['L-07'],
            ],
            [
                'id' => 'sw-ros2',
                'nombre' => 'ROS 2 Humble + Gazebo Sim',
                'version' => 'Humble Hawksbill LTS',
                'version_oficial' => 'Jazzy Jalisco',
                'estado_version' => 'Actualizado',
                'fabricante' => 'Open Robotics',
                'categoria' => 'Embebidos',
                'logo' => '/images/software/ros2.svg',
                'licencia' => 'Apache 2.0 Open Source',
                'puestos' => '40 Estaciones',
                'vigencia' => 'Mayo 2027',
                'descripcion' => 'Middleware estándar para control de robots móviles, cinemática inversa MoveIt 2, navegación SLAM y gemelos digitales en Gazebo.',
                'requisitos' => [
                    'cpu' => 'Intel Core i5-12500 (6 núcleos)',
                    'ram' => '16 GB DDR4 RAM',
                    'gpu' => 'NVIDIA T600 con soporte OpenGL 4.5 para RViz2 y Gazebo',
                    'disco' => '15 GB libres en partición Ubuntu',
                ],
                'modulos' => ['RViz2 3D Visualizer', 'MoveIt 2 Motion Planning', 'Nav2 Autonomous Navigation', 'Gazebo Fortress Physics'],
                'labs' => ['L-05', 'L-10'],
            ],
        ];
    }

    /**
     * Instrumental físico e inventario de pañol para Encargado y Préstamos.
     */
    public static function instrumental(): array
    {
        return [
            ['codigo' => 'MN-PAN-PRO-01', 'nombre' => 'Cañón Proyector Laser Portátil FHD', 'categoria' => 'Proyección', 'icono' => 'videocam', 'marca' => 'Epson PowerLite L200', 'serie' => 'MN-EP-1022', 'ubicacion' => 'Almacén E-102 · Estante A1', 'disponibles' => 3, 'total' => 4, 'observaciones' => 'Para laboratorios sin cañón fijo (L-02, L-05, L-08, L-10). Incluye cable HDMI trenzado.'],
            ['codigo' => 'MN-PAN-OSC-01', 'nombre' => 'Osciloscopio Digital 4 Canales 100MHz', 'categoria' => 'Medición', 'icono' => 'monitor_heart', 'marca' => 'Rigol DS1104Z Plus', 'serie' => 'MN-DS-2041', 'ubicacion' => 'Almacén E-102 · Estante B2', 'disponibles' => 5, 'total' => 6, 'observaciones' => 'Incluye 4 sondas atenuadoras x10 calibradas.'],
            ['codigo' => 'MN-PAN-FTE-02', 'nombre' => 'Fuente DC Triple Programable 30V/3A', 'categoria' => 'Medición', 'icono' => 'bolt', 'marca' => 'Siglent SPD3303X-E', 'serie' => 'MN-SP-1809', 'ubicacion' => 'Almacén E-102 · Estante B3', 'disponibles' => 6, 'total' => 8, 'observaciones' => 'Puntas banana de seguridad y protección OVP/OCP.'],
            ['codigo' => 'MN-PAN-RPI-04', 'nombre' => 'Kit Raspberry Pi 5 (8GB RAM + NVMe Hat)', 'categoria' => 'Embebidos', 'icono' => 'developer_board', 'marca' => 'Raspberry Pi Ltd', 'serie' => 'MN-RPI5-09', 'ubicacion' => 'Gabinete L-05 · Cajón 2', 'disponibles' => 9, 'total' => 15, 'observaciones' => 'Carcasa con disipador activo y fuente USB-C 27W.'],
            ['codigo' => 'MN-PAN-ESP-05', 'nombre' => 'Kit Desarrollo IoT ESP32-S3 + Sensores', 'categoria' => 'Embebidos', 'icono' => 'sensors', 'marca' => 'Espressif Systems', 'serie' => 'MN-ESP-77', 'ubicacion' => 'Gabinete L-05 · Cajón 4', 'disponibles' => 14, 'total' => 20, 'observaciones' => 'Protoboard doble, módulo OLED I2C, sensores BME280 e IMU.'],
            ['codigo' => 'MN-PAN-CON-06', 'nombre' => 'Kit Consola Cisco RJ45 a USB-C + Tester', 'categoria' => 'Redes', 'icono' => 'cable', 'marca' => 'Cisco Systems', 'serie' => 'MN-CAB-221', 'ubicacion' => 'Rack L-01 · Gaveta Cisco', 'disponibles' => 16, 'total' => 20, 'observaciones' => 'Para configuración directa de switches y routers en L-01 y L-07.'],
            ['codigo' => 'MN-PAN-VR-07', 'nombre' => 'Visor Realidad Mixta Meta Quest 3 (128GB)', 'categoria' => 'Multimedia', 'icono' => 'view_in_ar', 'marca' => 'Meta Reality Labs', 'serie' => 'MN-MQ3-012', 'ubicacion' => 'Gabinete L-09 · Caja XR', 'disponibles' => 4, 'total' => 6, 'observaciones' => 'Incluye cable Link fibra óptica 5m para estaciones de L-09.'],
        ];
    }
}
