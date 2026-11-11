@extends('layouts.public')

@section('title', 'Horarios y Disponibilidad de Salas · ' . config('app.institution_name', 'MetalNet'))

@section('content')
@php
    $labs = $laboratorios ?? $bmLabs ?? [];
    $dias = $diasSemana ?? ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
    $diaActual = $diaSeleccionado ?? 'Lunes';
    $labFiltro = $labSeleccionado ?? request('sala') ?? null;
    $totalHorasLibres = collect($labs)->sum('horas_libres');
    $totalHorasOcupadas = collect($labs)->sum('horas_ocupadas');
    $userRole = Auth::check() ? Auth::user()->primaryRoleSlug() : null;
@endphp

{{-- ── ENCABEZADO EDITORIAL METALNET (PALETA OFICIAL STITCH) ─────────────────── --}}
<div class="border border-surface-container-highest bg-surface-container-lowest rounded-2xl mb-6 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 mb-2">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded text-[10px] font-bold uppercase tracking-widest border border-[#b3f1be] bg-[#e8f5ec] text-[#16512c]">
                        Planificador Académico
                    </span>
                    <span class="text-[11px] font-semibold uppercase tracking-widest text-outline">
                        {{ config('app.institution_full', 'MetalNet · Laboratorios de Ingeniería en Sistemas') }}
                    </span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-on-surface">
                    Horarios y Reserva de Bloques ({{ $diaActual }})
                </h1>
                <p class="text-xs sm:text-sm mt-1 max-w-2xl text-on-surface-variant">
                    Consulta la programación por franja horaria (07:00 a 19:00), identifica bloques disponibles en verde o sesiones ocupadas en amarillo y reserva salas al instante.
                </p>
            </div>

            {{-- Indicadores Globales de Horas Libres (Verde) vs Ocupadas (Amarillo) --}}
            <div class="flex flex-wrap items-center gap-3">
                <div class="px-4 py-2.5 rounded-xl border border-[#b3f1be] bg-[#e8f5ec] flex items-center gap-2.5 shadow-xs">
                    <span class="w-2.5 h-2.5 rounded-full bg-primary"></span>
                    <div>
                        <span class="text-xl font-black font-mono leading-none block text-[#16512c]">{{ $totalHorasLibres }}h</span>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-[#16512c]">Horas Disponibles</span>
                    </div>
                </div>
                <div class="px-4 py-2.5 rounded-xl border border-[#fde68a] bg-[#fef3c7] flex items-center gap-2.5 shadow-xs">
                    <span class="w-2.5 h-2.5 rounded-full bg-status-occupied"></span>
                    <div>
                        <span class="text-xl font-black font-mono leading-none block text-[#92400e]">{{ $totalHorasOcupadas }}h</span>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-[#92400e]">Horas Ocupadas</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<section class="max-w-7xl mx-auto pb-12">

    {{-- ── BARRA SUPERIOR DE FILTROS (DÍAS, SALAS Y LEYENDA DE 3 COLORES) ── --}}
    <div class="rounded-2xl border border-surface-container-highest p-4 sm:p-5 mb-6 bg-surface-container-lowest shadow-sm">
        <div class="flex flex-wrap items-center justify-between gap-3">
            {{-- Selector de Día (Lunes a Sábado) --}}
            <div class="flex flex-wrap items-center gap-1.5">
                <span class="text-[11px] font-bold uppercase tracking-wider mr-1 text-outline">Día:</span>
                @foreach($dias as $dia)
                    <a href="{{ route('public.horarios', array_filter(['dia' => $dia, 'laboratorio_id' => $labFiltro])) }}"
                       class="px-3.5 py-1.5 rounded-lg text-xs font-bold border transition {{ $diaActual === $dia ? 'bg-primary text-white border-primary shadow-xs font-black' : 'bg-surface-container-low text-on-surface-variant border-surface-container-highest hover:bg-surface-container hover:text-on-surface' }}">
                        {{ $dia }}
                    </a>
                @endforeach
            </div>

            {{-- Leyenda Contextual Compacta (Verde, Amarillo, Rojo) --}}
            <div class="mn-legend-capsule" aria-label="Simbología de bloques horarios">
                <span class="text-[10px] font-bold uppercase tracking-wider mr-1 text-outline">Estado:</span>
                <span class="mn-legend-item">
                    <span class="mn-legend-dot mn-legend-dot--free"></span>
                    Disponible
                </span>
                <span class="mn-legend-item">
                    <span class="mn-legend-dot mn-legend-dot--busy"></span>
                    Ocupado
                </span>
                <span class="mn-legend-item">
                    <span class="mn-legend-dot mn-legend-dot--down"></span>
                    No disponible
                </span>
            </div>
        </div>

        {{-- Filtros Rápidos de Salas: Todas, L-01 a L-10 --}}
        <div class="mt-3.5 pt-3.5 border-t border-surface-container-highest flex flex-wrap items-center gap-1.5">
            <span class="text-[11px] font-bold uppercase tracking-wider mr-1 text-outline">Sala:</span>
            <a href="{{ route('public.horarios', ['dia' => $diaActual]) }}"
               class="px-3 py-1 rounded-lg text-xs font-bold border transition {{ !$labFiltro ? 'bg-primary text-white border-primary shadow-xs font-black' : 'bg-surface-container-low text-on-surface-variant border-surface-container-highest hover:bg-surface-container hover:text-on-surface' }}">
                Todas ({{ count($labs) }})
            </a>
            @foreach($labs as $l)
                @php
                    $isCurrentLab = (string)$labFiltro === (string)$l['id'] || (string)$labFiltro === (string)$l['codigo'];
                @endphp
                <a href="{{ route('public.horarios', ['dia' => $diaActual, 'laboratorio_id' => $l['id']]) }}"
                   class="px-2.5 py-1 rounded-lg text-xs font-mono font-bold border transition {{ $isCurrentLab ? 'bg-primary text-white border-primary shadow-xs font-black' : 'bg-surface-container-low text-on-surface-variant border-surface-container-highest hover:bg-surface-container hover:text-on-surface' }}">
                    {{ $l['codigo'] }}
                </a>
            @endforeach
        </div>
    </div>

    {{-- ── LÍNEA DE TIEMPO INTERACTIVA POR LABORATORIO ─────────────────────── --}}
    <div class="space-y-5">
        @foreach($labs as $lab)
            @if(!$labFiltro || (string)$labFiltro === (string)$lab['id'] || (string)$labFiltro === (string)$lab['codigo'])
                <article class="rounded-2xl border border-surface-container-highest p-4 sm:p-5 bg-surface-container-lowest shadow-sm">
                    {{-- Cabecera del Laboratorio --}}
                    <div class="flex flex-wrap items-center justify-between gap-3 pb-3.5 mb-4 border-b border-surface-container-highest">
                        <div class="flex flex-wrap items-center gap-2.5">
                            <span class="px-3 py-1 rounded-lg font-mono text-xs font-bold border border-surface-container-highest bg-surface-container text-primary">
                                {{ $lab['etiqueta_mapa'] }} · {{ $lab['codigo'] }}
                            </span>
                            <div>
                                <h2 class="text-base font-extrabold leading-tight text-on-surface">{{ $lab['nombre'] }}</h2>
                                <p class="text-xs text-on-surface-variant mt-0.5">
                                    {{ $lab['planta'] }} · {{ $lab['hardware'] }}
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center gap-2">
                            <span class="px-2.5 py-1 rounded-lg text-xs font-mono font-bold border {{ $lab['canon'] ? 'border-[#b3f1be] bg-[#e8f5ec] text-[#16512c]' : 'border-surface-container-highest bg-surface-container text-outline' }}">
                                {{ $lab['canon'] ? 'Cañón Activo' : 'Sin Cañón' }}
                            </span>
                            <a href="{{ route('public.salas') }}" class="text-xs font-bold text-primary hover:underline">
                                Ver plano de 20 PCs
                            </a>
                        </div>
                    </div>

                    {{-- Bloques Horarios (07:00 a 19:00) --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-3">
                        @foreach(($lab['horarios_horas'] ?? []) as $slot)
                            @php
                                $isFree = ($slot['estado'] ?? 'ocupado') === 'libre';
                            @endphp

                            @if($isFree)
                                {{-- Bloque DISPONIBLE / LIBRE (Verde Stitch) --}}
                                <div class="rounded-xl border border-[#b3f1be] p-3 flex flex-col justify-between transition hover:border-primary bg-[#f5fbf7] shadow-xs">
                                    <div>
                                        <div class="flex items-center justify-between gap-1 pb-1.5 border-b border-[#b3f1be]">
                                            <span class="font-mono text-xs font-extrabold text-[#16512c]">
                                                {{ $slot['rango'] ?? $slot['hora'] }}
                                            </span>
                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold border border-[#b3f1be] bg-[#e8f5ec] text-[#16512c]">
                                                Disponible
                                            </span>
                                        </div>
                                        <div class="mt-2.5 space-y-1">
                                            <p class="text-xs font-extrabold leading-snug text-on-surface">
                                                {{ $slot['titulo'] ?? 'Práctica Libre Abierta' }}
                                            </p>
                                            <p class="text-[11px] text-outline truncate">
                                                Guardia Técnica · Sin Clase
                                            </p>
                                            <p class="text-[10px] font-mono text-primary pt-1">
                                                20 PCs listas para práctica
                                            </p>
                                        </div>
                                    </div>

                                    {{-- Botón de Acción en Bloque Libre --}}
                                    <div class="mt-3 pt-2 border-t border-[#b3f1be]">
                                        <button type="button"
                                                onclick="window.HorariosDecision.abrirReservaAhora({{ $lab['id'] }}, '{{ $slot['hora'] }}', '{{ $slot['rango'] ?? $slot['hora'] }}', '{{ $diaActual }}')"
                                                class="w-full py-1.5 px-2.5 rounded-lg text-white font-black text-xs flex items-center justify-center gap-1.5 cursor-pointer transition hover:bg-primary-container bg-primary shadow-xs">
                                            <svg class="w-3.5 h-3.5 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/></svg>
                                            <span>Reservar ahora</span>
                                        </button>
                                    </div>
                                </div>
                            @else
                                {{-- Bloque OCUPADO / EN CLASE O EXAMEN (Amarillo) --}}
                                <div class="rounded-xl border border-[#fde68a] p-3 flex flex-col justify-between transition bg-[#fffdf5] shadow-xs">
                                    <div>
                                        <div class="flex items-center justify-between gap-1 pb-1.5 border-b border-[#fde68a]">
                                            <span class="font-mono text-xs font-extrabold text-[#92400e]">
                                                {{ $slot['rango'] ?? $slot['hora'] }}
                                            </span>
                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold border border-[#fde68a] bg-[#fef3c7] text-[#92400e]">
                                                {{ $slot['modalidad'] ?? 'En Clase' }}
                                            </span>
                                        </div>

                                        <div class="mt-2.5 space-y-1">
                                            <p class="text-xs font-extrabold leading-snug line-clamp-2 text-on-surface" title="{{ $slot['titulo'] }}">
                                                {{ $slot['titulo'] }}
                                            </p>
                                            <p class="text-[11px] font-semibold flex items-center gap-1 truncate text-[#92400e]" title="{{ $slot['docente'] ?? $lab['docente'] }}">
                                                <svg class="w-3 h-3 shrink-0 text-[#92400e]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                                                <span class="truncate">{{ $slot['docente'] ?? $lab['docente'] }}</span>
                                            </p>
                                            <div class="flex items-center justify-between pt-1 text-[10px] font-mono">
                                                <span class="px-1.5 py-0.5 rounded border border-[#fde68a] bg-[#fef3c7] text-[#92400e] font-bold">
                                                    Grupo {{ $slot['grupo'] ?? $lab['grupo'] }}
                                                </span>
                                                <span class="font-bold text-[#92400e]">
                                                    <strong>{{ $slot['pcs_uso'] ?? 20 }}</strong>/20 PCs
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Acciones Rápidas en Bloque Ocupado --}}
                                    <div class="mt-3 pt-2 border-t border-[#fde68a] space-y-1.5">
                                        <button type="button"
                                                onclick="window.HorariosDecision.verOtroLabLibre('{{ $slot['hora'] }}', '{{ $slot['rango'] ?? $slot['hora'] }}', {{ $lab['id'] }})"
                                                class="w-full py-1.5 px-2.5 rounded-lg border border-surface-container-highest text-[10.5px] font-bold flex items-center justify-center gap-1 cursor-pointer transition hover:bg-surface-container bg-surface-container-low text-primary">
                                            <svg class="w-3 h-3 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                                            Ver otro lab libre
                                        </button>
                                        <button type="button"
                                                onclick="window.HorariosDecision.abrirOtroDiaHorario({{ $lab['id'] }})"
                                                class="w-full py-1.5 px-2.5 rounded-lg border border-surface-container-highest text-[10.5px] font-semibold flex items-center justify-center gap-1 cursor-pointer transition hover:bg-surface-container bg-surface-container-lowest text-on-surface-variant hover:text-on-surface">
                                            <svg class="w-3 h-3 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/></svg>
                                            Apartar en otro día / horario
                                        </button>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </article>
            @endif
        @endforeach
    </div>
</section>

{{-- ── MODAL 1: VER OTROS LABORATORIOS LIBRES A ESTA MISMA HORA ──────────── --}}
<div id="modal-labs-libres-hora" class="fixed inset-0 z-50 hidden items-center justify-center p-4 bg-[#1b1c18]/60 backdrop-blur-sm">
    <div class="w-full max-w-2xl rounded-2xl border border-surface-container-highest p-6 shadow-2xl bg-surface-container-lowest text-on-surface">
        <div class="flex items-center justify-between pb-3 border-b border-surface-container-highest">
            <div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider block text-primary">Alternativas Disponibles en Tiempo Real</span>
                <h3 class="text-lg font-extrabold text-on-surface">
                    Laboratorios disponibles a las <span id="alt-hora-label" class="font-mono text-primary">09:00</span> ({{ $diaActual }})
                </h3>
            </div>
            <button type="button" onclick="window.HorariosDecision.closeModal('modal-labs-libres-hora')"
                    class="w-8 h-8 rounded-lg border border-surface-container-highest flex items-center justify-center cursor-pointer bg-surface-container-low text-on-surface-variant hover:bg-surface-container hover:text-on-surface">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" x2="6" y1="6" y2="18"/><line x1="6" x2="18" y1="6" y2="18"/></svg>
            </button>
        </div>
        <div id="alt-labs-container" class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-3 max-h-[60vh] overflow-y-auto"></div>
    </div>
</div>

{{-- ── MODAL 2: SELECTOR RÁPIDO DE OTRO DÍA / HORARIO ───────────────────── --}}
<div id="modal-otro-dia-horario" class="fixed inset-0 z-50 hidden items-center justify-center p-4 bg-[#1b1c18]/60 backdrop-blur-sm">
    <div class="w-full max-w-xl rounded-2xl border border-surface-container-highest p-6 shadow-2xl bg-surface-container-lowest text-on-surface">
        <div class="flex items-center justify-between pb-3 border-b border-surface-container-highest">
            <div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider block text-primary">Planificador de Sala · MetalNet</span>
                <h3 class="text-lg font-extrabold text-on-surface" id="od-lab-titulo">L-01 · Redes y Telemetría</h3>
            </div>
            <button type="button" onclick="window.HorariosDecision.closeModal('modal-otro-dia-horario')"
                    class="w-8 h-8 rounded-lg border border-surface-container-highest flex items-center justify-center cursor-pointer bg-surface-container-low text-on-surface-variant hover:bg-surface-container hover:text-on-surface">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" x2="6" y1="6" y2="18"/><line x1="6" x2="18" y1="6" y2="18"/></svg>
            </button>
        </div>

        <div class="mt-4 space-y-4">
            <div>
                <span class="block text-xs font-bold uppercase mb-2 text-outline">1. Selecciona otro día de la semana</span>
                <div class="grid grid-cols-3 sm:grid-cols-6 gap-1.5" id="od-dias-grid">
                    @foreach($dias as $d)
                        <button type="button"
                                onclick="window.HorariosDecision.seleccionarDiaModal('{{ $d }}', this)"
                                class="od-dia-btn py-2 px-2 rounded-lg text-xs font-bold border transition cursor-pointer {{ $d === $diaActual ? 'bg-primary text-white border-primary shadow-xs font-black' : 'bg-surface-container-low text-on-surface-variant border-surface-container-highest hover:bg-surface-container hover:text-on-surface' }}"
                                data-dia="{{ $d }}">
                            {{ $d }}
                        </button>
                    @endforeach
                </div>
            </div>

            <div>
                <span class="block text-xs font-bold uppercase mb-2 text-outline">2. Bloques horarios de este laboratorio</span>
                <div id="od-slots-grid" class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 max-h-60 overflow-y-auto"></div>
            </div>
        </div>
    </div>
</div>

{{-- ── MODAL 3: RESERVAR AHORA EN BLOQUE LIBRE (SEGÚN ROL DEL USUARIO) ──── --}}
<div id="modal-reservar-bloque-libre" class="fixed inset-0 z-50 hidden items-center justify-center p-4 bg-[#1b1c18]/60 backdrop-blur-sm">
    <div class="w-full max-w-lg rounded-2xl border border-surface-container-highest p-6 shadow-2xl bg-surface-container-lowest text-on-surface">
        <div class="flex items-center justify-between pb-3 border-b border-surface-container-highest">
            <div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider block text-primary">Reserva Inmediata de Horario</span>
                <h3 class="text-lg font-extrabold text-on-surface" id="rb-titulo">Reservar L-01 · 11:00 - 13:00</h3>
            </div>
            <button type="button" onclick="window.HorariosDecision.closeModal('modal-reservar-bloque-libre')"
                    class="w-8 h-8 rounded-lg border border-surface-container-highest flex items-center justify-center cursor-pointer bg-surface-container-low text-on-surface-variant hover:bg-surface-container hover:text-on-surface">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" x2="6" y1="6" y2="18"/><line x1="6" x2="18" y1="6" y2="18"/></svg>
            </button>
        </div>

        @auth
            @if(in_array($userRole, ['docente', 'admin'], true))
                {{-- Formulario para Docente / Admin: Reserva de sala completa --}}
                <form method="POST" action="{{ route('panel.reservar-sala') }}" class="mt-4 space-y-3.5">
                    @csrf
                    <input type="hidden" name="laboratorio_id" id="rb-lab-id" value="1">
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[11px] font-bold uppercase mb-1 text-outline">Día / Fecha *</label>
                            <input type="text" name="dia" id="rb-dia-input" required value="{{ $diaActual }}"
                                   class="w-full h-10 px-3 rounded-lg border border-outline-variant bg-surface-container-low text-xs font-bold text-on-surface focus:outline-none focus:border-primary">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold uppercase mb-1 text-outline">Bloque Horario *</label>
                            <input type="text" name="bloque" id="rb-bloque-input" required value="11:00 - 13:00"
                                   class="w-full h-10 px-3 rounded-lg border border-outline-variant bg-surface-container-low text-xs font-mono font-bold text-primary focus:outline-none focus:border-primary">
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[11px] font-bold uppercase mb-1 text-outline">Modalidad de Sesión *</label>
                            <select name="modalidad"
                                    class="w-full h-10 px-3 rounded-lg border border-outline-variant bg-surface-container-low text-xs font-bold text-on-surface focus:outline-none focus:border-primary">
                                <option value="Clase">Clase Regular (Ocupa 20 PCs)</option>
                                <option value="Examen">Examen Departamental (Ocupa 20 PCs)</option>
                                <option value="Práctica" selected>Práctica de Laboratorio</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold uppercase mb-1 text-outline">Grupo Académico *</label>
                            <input type="text" name="grupo" required value="MN-8A"
                                   class="w-full h-10 px-3 rounded-lg border border-outline-variant bg-surface-container-low text-xs font-mono text-on-surface focus:outline-none focus:border-primary">
                        </div>
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold uppercase mb-1 text-outline">Asignatura / Nombre de la Sesión *</label>
                        <input type="text" name="materia" required placeholder="Ej. Redes y Seguridad Informática"
                               class="w-full h-10 px-3 rounded-lg border border-outline-variant bg-surface-container-low text-xs text-on-surface focus:outline-none focus:border-primary">
                    </div>
                    <div class="pt-2 flex items-center justify-end gap-2">
                        <button type="button" onclick="window.HorariosDecision.closeModal('modal-reservar-bloque-libre')"
                                class="px-4 py-2 rounded-lg text-xs font-semibold border border-surface-container-highest bg-surface-container-low text-on-surface-variant hover:bg-surface-container hover:text-on-surface cursor-pointer">
                            Cancelar
                        </button>
                        <button type="submit"
                                class="px-5 py-2 text-white font-black rounded-lg text-xs cursor-pointer bg-primary hover:bg-primary-container transition shadow-sm">
                            Confirmar Reserva de Sala
                        </button>
                    </div>
                </form>
            @else
                {{-- Formulario para Alumno: Apartar computadora libre en esa sala --}}
                <div class="mt-4 space-y-4">
                    <p class="text-xs text-on-surface-variant leading-relaxed">
                        Este bloque horario está disponible para práctica libre. Como <strong class="text-primary font-bold">Alumno</strong>, puedes apartar una estación de cómputo inmediatamente:
                    </p>
                    <form method="POST" action="{{ route('panel.reservar-pc') }}" class="space-y-3">
                        @csrf
                        <input type="hidden" name="laboratorio_id" id="rb-alu-lab-id" value="1">
                        <div>
                            <label class="block text-[11px] font-bold uppercase mb-1 text-outline">Selecciona Estación (PC-01 a PC-20) *</label>
                            <select name="estacion_id" class="w-full h-10 px-3 rounded-lg border border-outline-variant bg-surface-container-low text-xs font-mono font-bold text-primary focus:outline-none focus:border-primary">
                                @for($p = 1; $p <= 20; $p++)
                                    <option value="PC-{{ str_pad($p, 2, '0', STR_PAD_LEFT) }}">PC-{{ str_pad($p, 2, '0', STR_PAD_LEFT) }}</option>
                                @endfor
                            </select>
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold uppercase mb-1 text-outline">Duración de Práctica *</label>
                            <select name="duracion" class="w-full h-10 px-3 rounded-lg border border-outline-variant bg-surface-container-low text-xs font-bold text-on-surface focus:outline-none focus:border-primary">
                                <option value="1">1 hora</option>
                                <option value="2" selected>2 horas</option>
                                <option value="3">3 horas</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold uppercase mb-1 text-outline">Objetivo de Práctica *</label>
                            <input type="text" name="actividad" required value="Práctica libre individual" class="w-full h-10 px-3 rounded-lg border border-outline-variant bg-surface-container-low text-xs text-on-surface focus:outline-none focus:border-primary">
                        </div>
                        <div class="pt-2 flex items-center justify-end gap-2">
                            <button type="button" onclick="window.HorariosDecision.closeModal('modal-reservar-bloque-libre')"
                                    class="px-4 py-2 rounded-lg text-xs font-semibold border border-surface-container-highest bg-surface-container-low text-on-surface-variant hover:bg-surface-container hover:text-on-surface cursor-pointer">
                                Cancelar
                            </button>
                            <button type="submit"
                                    class="px-5 py-2 text-white font-black rounded-lg text-xs cursor-pointer bg-primary hover:bg-primary-container transition shadow-sm">
                                Apartar Computadora
                            </button>
                        </div>
                    </form>
                </div>
            @endif
        @else
            {{-- Usuario no autenticado (Visitante) --}}
            <div class="mt-4 space-y-4 text-xs text-on-surface-variant">
                <div class="p-3.5 rounded-xl border border-surface-container-highest bg-surface-container-low">
                    <p class="font-bold text-on-surface mb-1">Inicia sesión para reservar este laboratorio o estación</p>
                    <p class="leading-relaxed">
                        Para programar salas completas como <strong class="text-primary">Docente</strong> (Clase, Práctica o Examen) o apartar estaciones individuales como <strong class="text-primary">Alumno</strong>, debes autenticarte con tu cuenta institucional.
                    </p>
                </div>
                <div class="flex items-center justify-end gap-2">
                    <button type="button" onclick="window.HorariosDecision.closeModal('modal-reservar-bloque-libre')"
                            class="px-4 py-2 rounded-lg text-xs font-semibold border border-surface-container-highest bg-surface-container-low text-on-surface-variant hover:bg-surface-container hover:text-on-surface cursor-pointer">
                        Cancelar
                    </button>
                    <a href="{{ route('login') }}"
                       class="px-5 py-2 text-white font-black rounded-lg text-xs bg-primary hover:bg-primary-container transition shadow-sm">
                        Ir a Iniciar Sesión
                    </a>
                </div>
            </div>
        @endauth
    </div>
</div>

<script>
    window.HorariosDecision = (function () {
        const LABS = (window.BM_DATA && window.BM_DATA.labs) || [];
        let selectedLabForDay = null;
        let currentDayModal = @json($diaActual);

        function openModal(id) {
            const el = document.getElementById(id);
            if (!el) return;
            el.classList.remove('hidden');
            el.classList.add('flex');
        }

        function closeModal(id) {
            const el = document.getElementById(id);
            if (!el) return;
            el.classList.add('hidden');
            el.classList.remove('flex');
        }

        function verOtroLabLibre(horaStr, rangoStr, currentLabId) {
            const label = document.getElementById('alt-hora-label');
            if (label) label.textContent = rangoStr || horaStr;

            const container = document.getElementById('alt-labs-container');
            container.innerHTML = '';

            const libres = LABS.filter(l => {
                if (String(l.id) === String(currentLabId)) return false;
                const slot = (l.horarios || []).find(s => s.hora === horaStr);
                return slot && slot.estado === 'libre';
            });

            if (!libres.length) {
                container.innerHTML = '<div class="col-span-2 p-4 rounded-xl border border-surface-container-highest bg-surface-container-low text-xs text-outline">Todos los laboratorios tienen sesión programada en este bloque. Usa el botón "Apartar en otro día / horario".</div>';
            } else {
                libres.forEach(l => {
                    const card = document.createElement('div');
                    card.className = 'p-4 rounded-xl border border-surface-container-highest bg-surface-container-low flex flex-col justify-between gap-3 shadow-xs';
                    card.innerHTML = `
                        <div>
                            <div class="flex items-center justify-between gap-2">
                                <span class="px-2.5 py-0.5 rounded font-mono text-xs font-bold border border-surface-container-highest bg-surface-container text-primary">${l.codigo} · ${l.etiqueta}</span>
                                <span class="text-xs font-mono font-bold text-primary">${l.conteo.disponible} PCs libres</span>
                            </div>
                            <h4 class="text-sm font-extrabold mt-2 text-on-surface">${l.nombre}</h4>
                            <p class="text-[11px] mt-0.5 text-outline">${l.planta} · ${l.canon ? 'Cañón Activo' : 'Sin Cañón'}</p>
                        </div>
                        <button type="button" class="w-full py-2 px-3 rounded-lg text-white font-black text-xs cursor-pointer transition bg-primary hover:bg-primary-container shadow-xs">
                            Reservar ${l.codigo} (${rangoStr})
                        </button>
                    `;
                    card.querySelector('button').onclick = () => {
                        closeModal('modal-labs-libres-hora');
                        abrirReservaAhora(l.id, horaStr, rangoStr, currentDayModal);
                    };
                    container.appendChild(card);
                });
            }

            openModal('modal-labs-libres-hora');
        }

        function abrirOtroDiaHorario(labId) {
            const lab = LABS.find(l => String(l.id) === String(labId));
            if (!lab) return;
            selectedLabForDay = lab;
            document.getElementById('od-lab-titulo').textContent = `${lab.codigo} · ${lab.nombre}`;
            renderSlotsForLabDay(lab, currentDayModal);
            openModal('modal-otro-dia-horario');
        }

        function seleccionarDiaModal(dia, btnEl) {
            currentDayModal = dia;
            document.querySelectorAll('.od-dia-btn').forEach(b => {
                const active = b === btnEl;
                b.className = 'od-dia-btn py-2 px-2 rounded-lg text-xs font-bold border transition cursor-pointer ' +
                    (active ? 'bg-primary text-white border-primary shadow-xs font-black' : 'bg-surface-container-low text-on-surface-variant border-surface-container-highest hover:bg-surface-container hover:text-on-surface');
            });
            if (selectedLabForDay) renderSlotsForLabDay(selectedLabForDay, dia);
        }

        function renderSlotsForLabDay(lab, dia) {
            const grid = document.getElementById('od-slots-grid');
            grid.innerHTML = '';
            (lab.horarios || []).forEach(slot => {
                const isFree = slot.estado === 'libre';
                const div = document.createElement('div');
                div.className = 'p-3 rounded-xl border flex items-center justify-between gap-2 shadow-xs ' +
                    (isFree ? 'border-[#b3f1be] bg-[#f5fbf7]' : 'border-[#fde68a] bg-[#fffdf5]');
                div.innerHTML = `
                    <div>
                        <span class="font-mono text-xs font-extrabold block ${isFree ? 'text-[#16512c]' : 'text-[#92400e]'}">${slot.rango || slot.hora}</span>
                        <span class="text-[11px] block truncate max-w-[170px] text-outline">${isFree ? 'Disponible para reserva' : slot.titulo}</span>
                    </div>
                    ${isFree
                        ? '<button type="button" class="px-3 py-1.5 rounded-lg text-white font-black text-xs cursor-pointer bg-primary hover:bg-primary-container transition shadow-xs">Reservar</button>'
                        : '<span class="text-[10px] font-bold uppercase px-2 py-0.5 rounded border border-[#fde68a] bg-[#fef3c7] text-[#92400e]">Ocupado</span>'}
                `;
                if (isFree) {
                    div.querySelector('button').onclick = () => {
                        closeModal('modal-otro-dia-horario');
                        abrirReservaAhora(lab.id, slot.hora, slot.rango || slot.hora, dia);
                    };
                }
                grid.appendChild(div);
            });
        }

        function abrirReservaAhora(labId, horaStr, rangoStr, diaStr) {
            const lab = LABS.find(l => String(l.id) === String(labId));
            if (!lab) return;
            document.getElementById('rb-titulo').textContent = `Reservar ${lab.codigo} (${lab.corto}) · ${rangoStr}`;
            const labInput = document.getElementById('rb-lab-id');
            const aluLabInput = document.getElementById('rb-alu-lab-id');
            const diaInput = document.getElementById('rb-dia-input');
            const bloqueInput = document.getElementById('rb-bloque-input');
            if (labInput) labInput.value = lab.id;
            if (aluLabInput) aluLabInput.value = lab.id;
            if (diaInput) diaInput.value = diaStr;
            if (bloqueInput) bloqueInput.value = rangoStr;

            openModal('modal-reservar-bloque-libre');
        }

        return { verOtroLabLibre, abrirOtroDiaHorario, seleccionarDiaModal, abrirReservaAhora, openModal, closeModal };
    })();
</script>
@endsection
