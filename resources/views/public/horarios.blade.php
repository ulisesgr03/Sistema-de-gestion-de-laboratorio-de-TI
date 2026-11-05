@extends('layouts.public')

@section('title', 'Horarios y Prácticas Académicas')

@section('header')
<div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-4 border-b border-[#e4e3db]">
    <div>
        <div class="flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-[#675a4e]">
            <span class="material-symbols-outlined text-[16px] text-[#2f664f]">calendar_month</span>
            <span>Programación Curricular Cuatrimestral · 2026-1</span>
        </div>
        <h1 class="text-2xl sm:text-3xl font-extrabold text-[#1b1c18] mt-1 tracking-tight">
            Horarios Oficiales y Prácticas Académicas
        </h1>
        <p class="text-xs sm:text-sm text-[#404944] mt-1">
            Distribución horaria por sala, asignaturas curriculares, docentes titulares y espacios asignados para práctica libre.
        </p>
    </div>

    <!-- Indicador de Jornada Continua -->
    <div class="flex items-center gap-2 px-3 py-1.5 rounded-lg bg-[#ffffff] border border-[#e4e3db] text-xs font-mono">
        <span class="w-2 h-2 rounded-full bg-[#316a42]"></span>
        <span class="text-[#1b1c18] font-bold">Jornada 07:00 a 20:00 hrs</span>
    </div>
</div>
@endsection

@section('content')
<!-- Barra de Selector de Día de la Semana -->
<div class="bg-[#ffffff] border border-[#e4e3db] rounded-xl p-2 mb-6 shadow-sm overflow-x-auto">
    <div class="flex items-center gap-1.5 min-w-max">
        @foreach($diasSemana as $dia)
            <a href="{{ route('horarios.index', ['dia' => $dia, 'laboratorio_id' => $labSeleccionado]) }}"
               class="px-4 py-2 rounded-lg text-xs font-bold transition-all flex items-center gap-1.5 {{ $diaSeleccionado === $dia ? 'bg-[#2f664f] text-white shadow-sm' : 'text-[#404944] hover:bg-[#efeee7] hover:text-[#1b1c18]' }}">
                <span class="material-symbols-outlined text-[16px]">{{ $diaSeleccionado === $dia ? 'today' : 'calendar_today' }}</span>
                <span>{{ $dia }}</span>
            </a>
        @endforeach
    </div>
</div>

<!-- Filtro por Laboratorio Específico -->
<div class="bg-[#ffffff] border border-[#e4e3db] rounded-xl p-4 mb-6 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div class="flex items-center gap-2">
        <span class="material-symbols-outlined text-[#707973] text-[20px]">filter_alt</span>
        <span class="text-xs font-bold text-[#1b1c18]">Filtrar por Sala Específica:</span>
    </div>

    <form method="GET" action="{{ route('horarios.index') }}" class="flex items-center gap-2 flex-1 max-w-md">
        <input type="hidden" name="dia" value="{{ $diaSeleccionado }}">
        <div class="relative w-full">
            <select name="laboratorio_id"
                    onchange="this.form.submit()"
                    class="w-full h-10 pl-3 pr-8 bg-[#efeee7] rounded-lg border border-[#c0c9c2] text-xs text-[#1b1c18] appearance-none focus:outline-none focus:border-[#2f664f] focus:bg-[#ffffff] cursor-pointer">
                <option value="">Todos los Laboratorios (L-01 a L-06)</option>
                @foreach($laboratorios as $lab)
                    <option value="{{ $lab->id }}" {{ $labSeleccionado == $lab->id ? 'selected' : '' }}>
                        {{ $lab->nombre }}
                    </option>
                @endforeach
            </select>
            <span class="material-symbols-outlined absolute right-2.5 top-2.5 text-[#707973] pointer-events-none text-[18px]">expand_more</span>
        </div>
        @if(!empty($labSeleccionado))
            <a href="{{ route('horarios.index', ['dia' => $diaSeleccionado]) }}"
               class="h-10 px-3 bg-[#efeee7] hover:bg-[#e4e3db] text-[#1b1c18] rounded-lg text-xs font-semibold flex items-center justify-center">
                Todos
            </a>
        @endif
    </form>
</div>

<!-- Matriz de Horarios por Laboratorio -->
<div class="space-y-6">
    @foreach($laboratorios as $lab)
        @if(empty($labSeleccionado) || $labSeleccionado == $lab->id)
            @php
                $codigo = substr($lab->nombre, strpos($lab->nombre, 'L-'), 4);
                $sesiones = $programacionAcademica[$codigo] ?? [
                    ['hora' => '07:00 - 11:00', 'materia' => 'Práctica Curricular Tutelada', 'docente' => 'Claustro Docente TI', 'grupo' => 'ITI Matutino'],
                    ['hora' => '11:00 - 14:00', 'materia' => 'Práctica Libre Asistida', 'docente' => 'Encargado de Sala', 'grupo' => 'Alumnos'],
                    ['hora' => '14:00 - 18:00', 'materia' => 'Desarrollo de Proyectos Integradores', 'docente' => 'Docente Asesor', 'grupo' => 'ITI Vespertino'],
                    ['hora' => '18:00 - 20:00', 'materia' => 'Mantenimiento y Respaldo de Imágenes', 'docente' => 'Técnico de Turno', 'grupo' => 'Mantenimiento'],
                ];
            @endphp

            <div class="bg-[#ffffff] border border-[#e4e3db] rounded-2xl overflow-hidden shadow-sm">
                <!-- Barra de Título del Laboratorio -->
                <div class="px-5 py-4 bg-[#f5f4ec] border-b border-[#e4e3db] flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-[#2f664f]"></span>
                            <span class="font-extrabold text-[#1b1c18] text-sm">{{ $lab->nombre }}</span>
                        </div>
                        <span class="text-[11px] text-[#707973]">{{ $lab->ubicacion }} · Capacidad {{ $lab->capacidad }} estaciones</span>
                    </div>
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded bg-[#ffffff] border border-[#e4e3db] text-xs font-bold text-[#2f664f]">
                        <span class="material-symbols-outlined text-[16px]">event_available</span>
                        <span>Día: {{ $diaSeleccionado }}</span>
                    </span>
                </div>

                <!-- Tabla de Bloques Horarios -->
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-[#efeee7] text-[#1b1c18] uppercase tracking-wider font-bold h-9 border-b border-[#e4e3db]">
                            <tr>
                                <th class="px-5 w-40">Bloque Horario</th>
                                <th class="px-4">Asignatura / Actividad</th>
                                <th class="px-4">Docente Titular</th>
                                <th class="px-4">Grupo / Matrícula</th>
                                <th class="px-5 text-right">Modalidad</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#e4e3db]">
                            @foreach($sesiones as $s)
                                @php
                                    $esLibre = str_contains(strtolower($s['materia']), 'libre') || str_contains(strtolower($s['grupo']), 'abierto');
                                @endphp
                                <tr class="h-12 hover:bg-[#fbf9f2] transition-colors {{ $esLibre ? 'bg-[#b3f1be]/10' : '' }}">
                                    <td class="px-5 font-mono font-bold text-[#1b1c18] whitespace-nowrap">
                                        <div class="flex items-center gap-2">
                                            <span class="material-symbols-outlined text-[16px] {{ $esLibre ? 'text-[#316a42]' : 'text-[#707973]' }}">schedule</span>
                                            <span>{{ $s['hora'] }}</span>
                                        </div>
                                    </td>
                                    <td class="px-4 font-semibold text-[#1b1c18]">
                                        {{ $s['materia'] }}
                                    </td>
                                    <td class="px-4 text-[#404944]">
                                        {{ $s['docente'] }}
                                    </td>
                                    <td class="px-4 font-mono text-[#675a4e]">
                                        {{ $s['grupo'] }}
                                    </td>
                                    <td class="px-5 text-right">
                                        @if($esLibre)
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-[#b3f1be]/40 text-[#16512c] font-bold text-[11px]">
                                                <span class="w-1.5 h-1.5 rounded-full bg-[#316a42]"></span>
                                                Acceso Libre
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-[#efeee7] text-[#1b1c18] font-semibold text-[11px]">
                                                <span class="material-symbols-outlined text-[13px] text-[#707973]">lock</span>
                                                Clase Exclusiva
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    @endforeach
</div>

<!-- Normativa sobre Cumplimiento de Horarios -->
<div class="mt-8 bg-[#f5f4ec] border border-[#e4e3db] rounded-2xl p-6 flex items-start gap-4 shadow-sm">
    <div class="w-10 h-10 rounded-xl bg-[#efeee7] border border-[#e4e3db] flex items-center justify-center text-[#2f664f] shrink-0 mt-0.5">
        <span class="material-symbols-outlined text-[24px]">info</span>
    </div>
    <div class="text-xs text-[#404944] space-y-1 leading-relaxed">
        <h4 class="font-bold text-[#1b1c18] text-sm">Disposición Oficial sobre Tolerancia y Desalojo de Salas</h4>
        <p>
            Los docentes y alumnos cuentan con 10 minutos de tolerancia al inicio de la sesión. Es responsabilidad de los usuarios guardar sus avances en repositorios externos (GitHub, USB) y cerrar sesión en las terminales 5 minutos antes del término del bloque horario para permitir el ingreso puntual del siguiente grupo académico.
        </p>
    </div>
</div>
@endsection
