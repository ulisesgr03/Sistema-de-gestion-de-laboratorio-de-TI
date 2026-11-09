<?php

namespace App\Http\Controllers;

use App\Models\CategoriaEquipo;
use App\Models\Equipo;
use App\Models\Estacion;
use App\Models\Horario;
use App\Models\Incidencia;
use App\Models\Laboratorio;
use App\Models\Practica;
use App\Models\Software;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PublicController extends Controller
{
    /**
     * Portal Público · Inicio y Servicios Tecnológicos.
     */
    public function index(): View
    {
        $laboratorios = Laboratorio::where('activo', true)
            ->withCount([
                'estaciones',
                'estaciones as estaciones_activas_count' => function ($query) {
                    $query->where('estado', 'activa');
                },
                'estaciones as estaciones_mantenimiento_count' => function ($query) {
                    $query->where('estado', 'mantenimiento');
                },
            ])
            ->get();

        $totalEstaciones = Estacion::count();
        $softwareCount = Software::count();

        // Especificaciones técnicas enriquecidas por laboratorio para la vista pública
        $laboratoriosDetallados = [
            [
                'id' => 1,
                'codigo' => 'L-01',
                'nombre' => 'Laboratorio L-01 · Redes y Telecomunicaciones',
                'ubicacion' => 'Edificio E · Planta Alta (E-201)',
                'estado_badge' => 'Clase en Curso',
                'badge_color' => 'bg-surface-container text-on-surface border border-surface-container-highest',
                'dot_color' => 'bg-primary',
                'aforo_total' => 30,
                'aforo_ocupado' => 24,
                'porcentaje' => 80,
                'reserva_info' => '6 estaciones en reserva · Switch Cisco Catalyst 3850 activo',
                'asignatura' => 'Redes WAN y Ruteo',
                'docente' => 'Mtro. Fernando Morales',
                'grupo' => 'ITI-801 · Matutino',
                'hardware' => 'Intel Core i7-12700, 32GB RAM DDR5, SSD NVMe 512GB, Display 24" IPS FHD.',
                'software' => 'Cisco Packet Tracer 8.2, GNS3, Wireshark, PuTTY, Ubuntu 22.04 LTS y Windows 11 Pro en arranque dual.',
                'proximo_cambio' => 'Próximo cambio de clase: 10:00 h',
            ],
            [
                'id' => 3,
                'codigo' => 'L-03',
                'nombre' => 'Laboratorio L-03 · Ingeniería de Software',
                'ubicacion' => 'Edificio E · Planta Baja (E-104)',
                'estado_badge' => 'Práctica Libre Autorizada',
                'badge_color' => 'bg-secondary-container text-on-secondary-container',
                'dot_color' => 'bg-secondary',
                'aforo_total' => 30,
                'aforo_ocupado' => 18,
                'porcentaje' => 60,
                'reserva_info' => '12 estaciones libres para ingreso · Acceso directo con credencial',
                'asignatura' => 'Proyectos Terminales',
                'docente' => 'Ing. Carlos Mendoza (Encargado)',
                'grupo' => 'Cupo abierto · Todas las ingenierías',
                'hardware' => 'AMD Ryzen 7 5700G, 32GB RAM DDR4 3200MHz, SSD NVMe 1TB, Monitores duales.',
                'software' => 'VS Code, Docker Desktop, Node.js 20, IntelliJ IDEA Ultimate, PostgreSQL 16, Git.',
                'proximo_cambio' => 'Sin clase programada hasta las 13:00 h',
            ],
            [
                'id' => 4,
                'codigo' => 'L-04',
                'nombre' => 'Laboratorio L-04 · Inteligencia Artificial y Datos',
                'ubicacion' => 'Edificio F · Planta Alta (F-205)',
                'estado_badge' => 'Taller Especializado',
                'badge_color' => 'bg-surface-container text-on-surface border border-surface-container-highest',
                'dot_color' => 'bg-tertiary',
                'aforo_total' => 30,
                'aforo_ocupado' => 28,
                'porcentaje' => 93,
                'reserva_info' => '2 estaciones en cómputo por lotes · Clústeres de GPU con refrigeración activa',
                'asignatura' => 'Deep Learning y Visión Artificial',
                'docente' => 'Dra. Elena Ramírez',
                'grupo' => 'ITI-902 · Posgrado y especialidad',
                'hardware' => 'Dell Precision 3660, NVIDIA RTX 4070 12GB VRAM, 64GB DDR5, SSD NVMe 2TB.',
                'software' => 'CUDA Toolkit 12.2, PyTorch 2.2, TensorFlow, JupyterHub, Python 3.11, Anaconda.',
                'proximo_cambio' => 'Cierre de sesión: 11:30 h',
            ],
            [
                'id' => 5,
                'codigo' => 'L-05',
                'nombre' => 'Laboratorio L-05 · Internet de las Cosas y Embebidos',
                'ubicacion' => 'Edificio F · Planta Baja (F-102)',
                'estado_badge' => 'Práctica Libre Disponible',
                'badge_color' => 'bg-secondary-container text-on-secondary-container',
                'dot_color' => 'bg-secondary',
                'aforo_total' => 30,
                'aforo_ocupado' => 12,
                'porcentaje' => 40,
                'reserva_info' => '18 módulos libres con instrumental · Osciloscopios y fuentes en línea',
                'asignatura' => 'Sistemas Embebidos',
                'docente' => 'Dr. Arturo Vega',
                'grupo' => 'Bancos asignados: módulos A1 a B6',
                'hardware' => 'Intel Core i5-11400, 16GB RAM, Osciloscopios Rigol DS1054Z, Fuentes DC triples.',
                'software' => 'STM32CubeIDE, Arduino IDE 2.3, Raspberry Pi 4 B, ESP32, Keil uVision, Wireshark IoT.',
                'proximo_cambio' => 'Próxima práctica: 12:00 h',
            ],
        ];

        return view('public.inicio', compact('laboratorios', 'laboratoriosDetallados', 'totalEstaciones', 'softwareCount'));
    }

    /**
     * Portal Público · Monitoreo de Salas y Disponibilidad en Tiempo Real.
     */
    public function salas(Request $request): View
    {
        $busqueda = $request->query('q');
        $edificio = $request->query('edificio');

        $query = Laboratorio::where('activo', true)
            ->with(['estaciones']);

        if ($busqueda) {
            $query->where(function ($q) use ($busqueda) {
                $q->where('nombre', 'like', "%{$busqueda}%")
                  ->orWhere('ubicacion', 'like', "%{$busqueda}%")
                  ->orWhere('descripcion', 'like', "%{$busqueda}%");
            });
        }

        if ($edificio) {
            $query->where('ubicacion', 'like', "%{$edificio}%");
        }

        $laboratorios = $query->orderBy('nombre')->get();

        return view('public.salas', compact('laboratorios', 'busqueda', 'edificio'));
    }

    /**
     * Portal Público · Horarios Semanales y Prácticas de Laboratorio.
     */
    public function horarios(Request $request): View
    {
        $diasSemana = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
        $diaParam = $request->query('dia', 'Lunes');
        $mapDias = [
            'lunes' => 'Lunes',
            'martes' => 'Martes',
            'miercoles' => 'Miércoles',
            'miércoles' => 'Miércoles',
            'jueves' => 'Jueves',
            'viernes' => 'Viernes',
            'sabado' => 'Sábado',
            'sábado' => 'Sábado',
        ];
        $diaSeleccionado = $mapDias[strtolower(trim($diaParam))] ?? 'Lunes';
        $labSeleccionado = $request->query('laboratorio_id');

        $laboratorios = Laboratorio::where('activo', true)->orderBy('nombre')->get();

        $query = Horario::with('laboratorio')
            ->where('activo', true);

        if ($diaSeleccionado && in_array($diaSeleccionado, $diasSemana, true)) {
            $query->where('dia_semana', $diaSeleccionado);
        }

        if ($labSeleccionado) {
            $query->where('laboratorio_id', $labSeleccionado);
        }

        $horarios = $query->orderBy('hora_inicio')->get();

        // Mapeo curricular representativo de asignaturas
        $programacionAcademica = [
            'L-01' => [
                ['hora' => '07:00 - 09:00', 'materia' => 'Fundamentos de Redes', 'docente' => 'Mtro. Fernando Morales', 'grupo' => 'ITI-401'],
                ['hora' => '09:00 - 11:00', 'materia' => 'Redes WAN y Ruteo', 'docente' => 'Mtro. Fernando Morales', 'grupo' => 'ITI-801'],
                ['hora' => '11:00 - 13:00', 'materia' => 'Práctica Libre Cisco CCNA', 'docente' => 'Encargado de Sala', 'grupo' => 'General'],
                ['hora' => '13:00 - 15:00', 'materia' => 'Seguridad Perimetral y Firewalls', 'docente' => 'Ing. Carlos Mendoza', 'grupo' => 'ITI-901'],
                ['hora' => '15:00 - 17:00', 'materia' => 'Telefonía IP y VoIP', 'docente' => 'Mtro. Roberto Hernández', 'grupo' => 'ITI-602'],
                ['hora' => '17:00 - 19:00', 'materia' => 'Laboratorio Abierto Vespertino', 'docente' => 'Técnico de Turno', 'grupo' => 'Alumnos'],
            ],
            'L-02' => [
                ['hora' => '07:00 - 09:00', 'materia' => 'Arquitectura de Computadoras', 'docente' => 'Dr. Arturo Vega', 'grupo' => 'ITI-301'],
                ['hora' => '09:00 - 11:00', 'materia' => 'Sistemas Operativos y Virtualización', 'docente' => 'Dra. Laura Sánchez', 'grupo' => 'ITI-502'],
                ['hora' => '11:00 - 13:00', 'materia' => 'Diseño Lógico con VHDL/Verilog', 'docente' => 'Dr. Arturo Vega', 'grupo' => 'ITI-701'],
                ['hora' => '13:00 - 15:00', 'materia' => 'Mantenimiento Preventivo de Hardware', 'docente' => 'Ing. Carlos Mendoza', 'grupo' => 'ITI-201'],
                ['hora' => '15:00 - 18:00', 'materia' => 'Práctica Libre Tutelada', 'docente' => 'Encargado de Sala', 'grupo' => 'General'],
            ],
            'L-03' => [
                ['hora' => '07:00 - 09:00', 'materia' => 'Programación Orientada a Objetos', 'docente' => 'Mtra. Patricia Rivas', 'grupo' => 'ITI-202'],
                ['hora' => '09:00 - 11:00', 'materia' => 'Desarrollo Web Full Stack', 'docente' => 'Mtro. Daniel Cruz', 'grupo' => 'ITI-601'],
                ['hora' => '11:00 - 13:00', 'materia' => 'Proyectos Terminales de Software', 'docente' => 'Ing. Carlos Mendoza', 'grupo' => 'Abierto'],
                ['hora' => '13:00 - 15:00', 'materia' => 'Desarrollo Móvil Multiplataforma', 'docente' => 'Mtro. Daniel Cruz', 'grupo' => 'ITI-702'],
                ['hora' => '15:00 - 18:00', 'materia' => 'Bases de Datos NoSQL y Microservicios', 'docente' => 'Dra. Elena Ramírez', 'grupo' => 'ITI-802'],
            ],
            'L-04' => [
                ['hora' => '07:00 - 09:00', 'materia' => 'Inteligencia Artificial Clásica', 'docente' => 'Dra. Elena Ramírez', 'grupo' => 'ITI-701'],
                ['hora' => '09:00 - 11:00', 'materia' => 'Deep Learning & Visión Artificial', 'docente' => 'Dra. Elena Ramírez', 'grupo' => 'ITI-902'],
                ['hora' => '11:00 - 13:00', 'materia' => 'Minería de Datos y Analítica', 'docente' => 'Dr. Gonzalo Ortiz', 'grupo' => 'ITI-801'],
                ['hora' => '13:00 - 16:00', 'materia' => 'Entrenamiento de Modelos en Cluster GPU', 'docente' => 'Investigadores UPTex', 'grupo' => 'Posgrado'],
                ['hora' => '16:00 - 20:00', 'materia' => 'Cómputo Científico Paralelo', 'docente' => 'Dr. Gonzalo Ortiz', 'grupo' => 'Seminario'],
            ],
            'L-05' => [
                ['hora' => '07:00 - 09:00', 'materia' => 'Electrónica Digital', 'docente' => 'Dr. Arturo Vega', 'grupo' => 'ITI-302'],
                ['hora' => '09:00 - 11:00', 'materia' => 'Microcontroladores ARM y STM32', 'docente' => 'Dr. Arturo Vega', 'grupo' => 'ITI-601'],
                ['hora' => '11:00 - 13:00', 'materia' => 'Sistemas Embebidos e IoT', 'docente' => 'Dr. Arturo Vega', 'grupo' => 'ITI-801'],
                ['hora' => '13:00 - 16:00', 'materia' => 'Robótica Móvil y Sensores', 'docente' => 'Mtro. Juan Pablo Ruiz', 'grupo' => 'ITI-703'],
                ['hora' => '16:00 - 19:00', 'materia' => 'Taller Práctico de Prototipado', 'docente' => 'Técnico Instrumental', 'grupo' => 'General'],
            ],
            'L-06' => [
                ['hora' => '07:00 - 11:00', 'materia' => 'Práctica Libre de Cómputo General A', 'docente' => 'Encargado de Sala', 'grupo' => 'Alumnos UPTex'],
                ['hora' => '11:00 - 14:00', 'materia' => 'Simulación Numérica con MATLAB', 'docente' => 'Dra. Laura Sánchez', 'grupo' => 'ITI-402'],
                ['hora' => '14:00 - 17:00', 'materia' => 'Exámenes y Certificaciones Oficiales', 'docente' => 'Comité de Certificación', 'grupo' => 'Especial'],
                ['hora' => '17:00 - 20:00', 'materia' => 'Práctica Libre Vespertina', 'docente' => 'Encargado de Sala', 'grupo' => 'Alumnos UPTex'],
            ],
        ];

        return view('public.horarios', compact('laboratorios', 'horarios', 'diasSemana', 'diaSeleccionado', 'labSeleccionado', 'programacionAcademica'));
    }

    /**
     * Portal Público · Catálogo de Software Institucional y Licenciamiento.
     */
    public function software(Request $request): View
    {
        $busqueda = $request->query('q');
        $categoria = $request->query('categoria');

        $query = Software::with('licencias');

        if ($busqueda) {
            $query->where(function ($q) use ($busqueda) {
                $q->where('nombre', 'like', "%{$busqueda}%")
                  ->orWhere('fabricante', 'like', "%{$busqueda}%")
                  ->orWhere('descripcion', 'like', "%{$busqueda}%");
            });
        }

        $softwareList = $query->orderBy('nombre')->get();

        return view('public.software', compact('softwareList', 'busqueda', 'categoria'));
    }

    /**
     * Portal Público · Préstamos en Ventanilla y Catálogo de Pañol.
     */
    public function prestamos(): View
    {
        $equiposDisponibles = Equipo::with('categoria')
            ->where('estado', 'disponible')
            ->whereHas('categoria', function ($q) {
                $q->where('nombre', 'like', '%Periféricos%')
                  ->orWhere('nombre', 'like', '%Laptops%')
                  ->orWhere('nombre', 'like', '%Redes%');
            })
            ->orderBy('nombre')
            ->get();

        return view('public.prestamos', compact('equiposDisponibles'));
    }

    /**
     * Portal Público · Reglamento Oficial de Laboratorios de TI.
     */
    public function reglamento(): View
    {
        return view('public.reglamento');
    }

    /**
     * Portal Público · Reportar Avería Técnica (Formulario de Mesa de Ayuda).
     */
    public function reportarAveria(): View
    {
        $laboratorios = Laboratorio::where('activo', true)->orderBy('nombre')->get();

        return view('public.reportar-averia', compact('laboratorios'));
    }

    /**
     * Portal Público · Procesa y Guarda un Reporte de Avería / Incidencia.
     */
    public function guardarAveria(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'laboratorio_id' => ['required', 'exists:laboratorios,id'],
            'estacion_nombre' => ['nullable', 'string', 'max:50'],
            'titulo' => ['required', 'string', 'max:150'],
            'categoria' => ['required', 'string'],
            'prioridad' => ['required', 'in:baja,media,alta,critica'],
            'descripcion' => ['required', 'string', 'min:10', 'max:2000'],
            'reportante_nombre' => ['required', 'string', 'max:100'],
            'reportante_matricula' => ['required', 'string', 'max:50'],
            'reportante_correo' => ['nullable', 'email', 'max:100'],
        ], [
            'laboratorio_id.required' => 'Debe seleccionar el laboratorio donde ocurre la avería.',
            'titulo.required' => 'Indique un título o resumen del fallo.',
            'prioridad.required' => 'Seleccione la urgencia o prioridad técnica.',
            'descripcion.required' => 'Describa detalladamente el comportamiento anómalo o falla.',
            'descripcion.min' => 'La descripción debe tener al menos 10 caracteres.',
            'reportante_nombre.required' => 'Indique su nombre completo.',
            'reportante_matricula.required' => 'Indique su matrícula institucional o número de nómina.',
        ]);

        // Determinar usuario reportante: el autenticado, o usuario correspondiente a la matrícula, o usuario de soporte
        $userId = Auth::id();
        if (!$userId) {
            $userFound = User::where('matricula', trim($validated['reportante_matricula']))->first();
            if ($userFound) {
                $userId = $userFound->id;
            } else {
                $soporteUser = User::where('email', 'soporte.laboratorios@uptex.edu.mx')->first();
                $userId = $soporteUser ? $soporteUser->id : User::first()->id;
            }
        }

        // Asociar a técnico asignado (Ing. Carlos Mendoza por defecto si existe)
        $tecnico = User::where('matricula', 'ADM-041')->first();

        // Encontrar estación si se proporcionó
        $estacionId = null;
        $equipoId = null;
        if (!empty($validated['estacion_nombre'])) {
            $estacion = Estacion::where('laboratorio_id', $validated['laboratorio_id'])
                ->where('nombre', trim($validated['estacion_nombre']))
                ->first();
            if ($estacion) {
                $estacionId = $estacion->id;
                $equipo = Equipo::where('estacion_id', $estacionId)->first();
                if ($equipo) {
                    $equipoId = $equipo->id;
                }
            }
        }

        $categoriaLabels = [
            'hardware' => 'HARDWARE',
            'periferico' => 'PERIFÉRICO',
            'red' => 'RED Y CONECTIVIDAD',
            'software' => 'SOFTWARE',
            'proyector' => 'PROYECTOR',
            'instalacion' => 'INSTALACIÓN ELÉCTRICA',
        ];
        $catTag = $categoriaLabels[$validated['categoria']] ?? strtoupper($validated['categoria']);

        $incidencia = Incidencia::create([
            'laboratorio_id' => $validated['laboratorio_id'],
            'equipo_id' => $equipoId,
            'reportado_por' => $userId,
            'asignado_a' => $tecnico ? $tecnico->id : null,
            'titulo' => '[' . $catTag . '] ' . $validated['titulo'],
            'descripcion' => "Reportado por: {$validated['reportante_nombre']} (Matrícula: {$validated['reportante_matricula']})\n" .
                             "Estación indicada: " . ($validated['estacion_nombre'] ?? 'Sin especificar') . "\n\n" .
                             $validated['descripcion'],
            'prioridad' => $validated['prioridad'],
            'estado' => 'abierta',
        ]);

        $ticket = '#INC-' . date('Ymd') . '-' . sprintf('%04d', $incidencia->id);

        return redirect()->route('averia.create')->with(
            'success',
            "Reporte de incidencia registrado exitosamente con folio institucional {$ticket}. El personal técnico de turno acudirá a revisar la estación."
        );
    }
}
