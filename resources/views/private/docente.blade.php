@extends('layouts.private')

@section('title', 'Panel de Docente · ' . config('app.institution_name', 'MetalNet'))

@section('content')
@php
    $labsList = $laboratorios ?? $bmLabs ?? [];
    $misClasesList = $misClases ?? collect();
    $misReservasSalaList = $misReservasSala ?? collect();
    $misSolicitudesList = $misSolicitudes ?? collect();
@endphp

<div class="space-y-6">
    {{-- Cabecera del Docente · Paleta Oficial Stitch --}}
    <div class="p-5 rounded-2xl border border-surface-container-highest flex flex-wrap items-center justify-between gap-4 bg-surface-container-lowest shadow-sm">
        <div class="flex items-center gap-3.5">
            <div class="w-12 h-12 rounded-2xl border border-[#b3f1be] flex items-center justify-center bg-[#e8f5ec] text-primary">
                <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8M12 17v4"/></svg>
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <span class="px-2.5 py-0.5 rounded-md border border-[#b3f1be] text-[10px] font-extrabold uppercase bg-[#e8f5ec] text-[#16512c]">
                        Rol: Docente Titular
                    </span>
                    <span class="text-xs font-mono text-outline">{{ $usuario->matricula ?? $usuario->email }}</span>
                </div>
                <h1 class="text-xl font-black mt-0.5 text-on-surface">
                    Consola Académica MetalNet · {{ $usuario->name }}
                </h1>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('software.index') }}"
               class="px-4 py-2.5 rounded-xl text-xs font-extrabold inline-flex items-center gap-1.5 transition hover:bg-surface-container border border-surface-container-highest bg-surface-container-low text-on-surface">
                <svg class="w-4 h-4 text-primary" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" x2="12" y1="15" y2="3"/></svg>
                Catálogo de Software
            </a>
            <button type="button"
                    data-open-map="0"
                    class="px-4 py-2.5 rounded-xl text-xs font-extrabold inline-flex items-center gap-1.5 cursor-pointer transition hover:bg-primary-container bg-primary text-white">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" x2="16" y1="21" y2="21"/><line x1="12" x2="12" y1="17" y2="21"/></svg>
                Inspeccionar Estaciones (200 PCs)
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        {{-- Columna Izquierda (6 cols): Reservar Laboratorio Completo por Bloque Horario --}}
        <div class="lg:col-span-6 space-y-5">
            <div class="p-5 rounded-2xl border border-surface-container-highest bg-surface-container-lowest shadow-sm">
                <div class="pb-3 mb-4 border-b border-surface-container-highest flex items-center justify-between">
                    <div>
                        <span class="text-[10px] font-extrabold uppercase tracking-wider block text-primary">Programación Académica</span>
                        <h2 class="text-base font-extrabold text-on-surface">Reservar Laboratorio Completo por Bloque o Fecha</h2>
                    </div>
                    <span class="px-2.5 py-1 rounded border border-surface-container-highest font-mono text-[11px] font-bold bg-surface-container-low text-primary">
                        20 Estaciones / Sala
                    </span>
                </div>

                <div class="p-3 mb-4 rounded-xl text-xs border border-surface-container-highest bg-surface-container-low text-on-surface-variant">
                    <strong class="text-[#16512c]">Regla de Aforo Total:</strong> Al reservar en modalidad <span class="text-[#92400e] font-bold">Clase</span> o <span class="text-[#92400e] font-bold">Examen</span>, las 20 computadoras del laboratorio quedan automáticamente asignadas en estado ocupado (amarillo), respetando equipos en mantenimiento.
                </div>

                <form method="POST" action="{{ route('panel.reservar-sala') }}" class="space-y-4">
                    @csrf
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                        <div>
                            <label class="block text-xs font-bold uppercase mb-1 text-on-surface-variant">Laboratorio (L-01 a L-10) *</label>
                            <select name="laboratorio_id"
                                    class="w-full h-10 px-3 rounded-xl border border-surface-container-highest text-xs font-bold focus:outline-none focus:border-primary bg-surface-container-lowest text-on-surface">
                                @foreach($labsList as $l)
                                    <option value="{{ $l['id'] }}">{{ $l['codigo'] }} · {{ $l['nombre_corto'] }} ({{ $l['canon'] ? 'Con Cañón' : 'Sin Cañón' }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase mb-1 text-on-surface-variant">Modalidad de Sesión *</label>
                            <select name="modalidad"
                                    class="w-full h-10 px-3 rounded-xl border border-surface-container-highest text-xs font-bold focus:outline-none focus:border-primary bg-surface-container-lowest text-primary">
                                <option value="Clase">Clase Regular (Ocupa 20 PCs)</option>
                                <option value="Práctica" selected>Práctica de Laboratorio</option>
                                <option value="Examen">Aplicación de Examen (Ocupa 20 PCs)</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5">
                        <div>
                            <label class="block text-xs font-bold uppercase mb-1 text-on-surface-variant">Día / Fecha *</label>
                            <select name="dia"
                                    class="w-full h-10 px-3 rounded-xl border border-surface-container-highest text-xs font-bold focus:outline-none focus:border-primary bg-surface-container-lowest text-on-surface">
                                @foreach(['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'] as $d)
                                    <option value="{{ $d }}">{{ $d }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase mb-1 text-on-surface-variant">Bloque Horario *</label>
                            <select name="bloque"
                                    class="w-full h-10 px-3 rounded-xl border border-surface-container-highest text-xs font-mono font-bold focus:outline-none focus:border-primary bg-surface-container-lowest text-on-surface">
                                <option value="07:00 - 09:00">07:00 - 09:00</option>
                                <option value="09:00 - 11:00">09:00 - 11:00</option>
                                <option value="11:00 - 13:00" selected>11:00 - 13:00</option>
                                <option value="13:00 - 15:00">13:00 - 15:00</option>
                                <option value="15:00 - 17:00">15:00 - 17:00</option>
                                <option value="17:00 - 19:00">17:00 - 19:00</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase mb-1 text-on-surface-variant">Grupo *</label>
                            <input type="text" name="grupo" required value="MN-TI-801"
                                   class="w-full h-10 px-3 rounded-xl border border-surface-container-highest text-xs font-mono font-bold focus:outline-none focus:border-primary bg-surface-container-lowest text-on-surface">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase mb-1 text-on-surface-variant">Asignatura o Tema de Sesión *</label>
                        <input type="text" name="materia" required placeholder="Ej. Redes Neuronales Profundas con PyTorch y CUDA"
                               class="w-full h-10 px-3 rounded-xl border border-surface-container-highest text-xs focus:outline-none focus:border-primary bg-surface-container-lowest text-on-surface">
                    </div>

                    <button type="submit"
                            class="w-full py-2.5 rounded-xl text-xs font-black cursor-pointer transition hover:bg-primary-container shadow-xs bg-primary text-white">
                        Confirmar Reserva de Laboratorio Completo
                    </button>
                </form>
            </div>

            {{-- Solicitar Software, Configuración de Red o Equipamiento Grupal --}}
            <div class="p-5 rounded-2xl border border-surface-container-highest bg-surface-container-lowest shadow-sm">
                <h3 class="text-sm font-extrabold uppercase tracking-wider mb-3 text-primary">
                    Requerimiento Grupal Pre-Sesión (Software / VLAN / Hardware)
                </h3>
                <form method="POST" action="{{ route('panel.solicitar-recurso') }}" class="space-y-3">
                    @csrf
                    <div class="grid grid-cols-2 gap-3">
                        <select name="tipo"
                                class="h-9 px-3 rounded-lg border border-surface-container-highest text-xs font-bold focus:outline-none focus:border-primary bg-surface-container-lowest text-on-surface">
                            <option value="Software">Despliegue de Software Grupal</option>
                            <option value="Hardware">Equipamiento / Kits para Grupo</option>
                            <option value="Mesa">Configuración VLAN / Topología Red</option>
                        </select>
                        <select name="sala"
                                class="h-9 px-3 rounded-lg border border-surface-container-highest text-xs font-bold focus:outline-none focus:border-primary bg-surface-container-lowest text-on-surface">
                            @foreach($labsList as $l)
                                <option value="{{ $l['codigo'] }}">{{ $l['codigo'] }} · {{ $l['nombre_corto'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <input type="text" name="recurso" required placeholder="Ej. Imagen Docker PostgreSQL 17 + PostGIS en las 20 estaciones"
                           class="w-full h-9 px-3 rounded-lg border border-surface-container-highest text-xs focus:outline-none focus:border-primary bg-surface-container-lowest text-on-surface">
                    <input type="text" name="descripcion" required placeholder="Indica fecha de la práctica y requerimientos..."
                           class="w-full h-9 px-3 rounded-lg border border-surface-container-highest text-xs focus:outline-none focus:border-primary bg-surface-container-lowest text-on-surface">
                    <button type="submit"
                            class="w-full py-2 rounded-lg text-xs font-bold cursor-pointer transition hover:bg-surface-container border border-surface-container-highest bg-surface-container-low text-on-surface">
                        Enviar Requerimiento al Administrador
                    </button>
                </form>
            </div>
        </div>

        {{-- Columna Derecha (6 cols): Calendario de Salas Reservadas y Lista Rápida de Asistencia --}}
        <div class="lg:col-span-6 space-y-5">
            <div class="p-5 rounded-2xl border border-surface-container-highest bg-surface-container-lowest shadow-sm">
                <div class="flex items-center justify-between pb-3 mb-3.5 border-b border-surface-container-highest">
                    <div>
                        <span class="text-[10px] font-extrabold uppercase tracking-wider block text-primary">Agenda Docente</span>
                        <h2 class="text-base font-extrabold text-on-surface">Calendario de Salas Reservadas</h2>
                    </div>
                    <span class="px-2.5 py-1 rounded border border-surface-container-highest font-mono text-xs font-bold bg-surface-container-low text-primary">
                        {{ $misClasesList->count() + $misReservasSalaList->count() }} bloques
                    </span>
                </div>

                <div class="space-y-2.5 max-h-72 overflow-y-auto">
                    @foreach($misReservasSalaList as $res)
                        @php
                            $mod = $res->modalidad ?? 'Práctica';
                            $badgeClass = $mod === 'Examen'
                                ? 'bg-[#fef3c7] text-[#92400e] border-[#fde68a]'
                                : 'bg-[#e8f5ec] text-[#16512c] border-[#b3f1be]';
                        @endphp
                        <div class="p-3.5 rounded-xl border border-surface-container-highest flex items-center justify-between gap-3 text-xs bg-surface-container-low">
                            <div>
                                <div class="flex items-center gap-2">
                                    <span class="px-2 py-0.5 rounded border border-surface-container-highest font-mono font-extrabold bg-surface-container-lowest text-primary">
                                        {{ $res->laboratorio?->codigo ?? 'LAB' }}
                                    </span>
                                    <span class="px-2 py-0.5 rounded border text-[10px] font-extrabold uppercase {{ $badgeClass }}">
                                        {{ $mod }}
                                    </span>
                                    <span class="font-mono text-outline">{{ optional($res->inicio_en)->format('d/m H:i') }} - {{ optional($res->fin_en)->format('H:i') }}</span>
                                </div>
                                <p class="font-bold mt-1 text-on-surface">{{ $res->materia }} · Grupo {{ $res->grupo }}</p>
                            </div>
                            <span class="px-2.5 py-1 rounded-md border border-[#b3f1be] font-mono text-[10px] font-bold uppercase bg-[#e8f5ec] text-[#16512c]">
                                {{ $res->estado }}
                            </span>
                        </div>
                    @endforeach

                    @foreach($misClasesList as $cl)
                        <div class="p-3.5 rounded-xl border border-surface-container-highest flex items-center justify-between gap-3 text-xs bg-surface-container-low">
                            <div>
                                <div class="flex items-center gap-2">
                                    <span class="px-2 py-0.5 rounded border border-surface-container-highest font-mono font-extrabold bg-surface-container-lowest text-primary">
                                        {{ $cl->laboratorio?->codigo ?? 'L-01' }}
                                    </span>
                                    <span class="px-2 py-0.5 rounded border border-[#fde68a] text-[10px] font-extrabold uppercase bg-[#fef3c7] text-[#92400e]">
                                        {{ $cl->tipo ?? 'Clase' }}
                                    </span>
                                    <span class="font-mono text-outline">{{ $cl->dia }} · {{ substr((string)$cl->hora_inicio, 0, 5) }} - {{ substr((string)$cl->hora_fin, 0, 5) }}</span>
                                </div>
                                <p class="font-bold mt-1 text-on-surface">{{ $cl->materia }} · Grupo {{ $cl->grupo }}</p>
                            </div>
                            <button type="button"
                                    data-open-map="{{ max(0, ((int)($cl->laboratorio_id ?? 1)) - 1) }}"
                                    class="px-3 py-1 rounded-lg border border-surface-container-highest text-[11px] font-bold cursor-pointer transition hover:bg-surface-container bg-surface-container-lowest text-primary">
                                Ver Sala
                            </button>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Lista Rápida de Asistencia y Equipos Asignados en Sesión --}}
            <div class="p-5 rounded-2xl border border-surface-container-highest bg-surface-container-lowest shadow-sm">
                <div class="flex items-center justify-between pb-3 mb-3 border-b border-surface-container-highest">
                    <div>
                        <span class="text-[10px] font-extrabold uppercase tracking-wider block text-primary">Control de Grupo en Vivo</span>
                        <h3 class="text-sm font-extrabold text-on-surface">Lista Rápida de Asistencia y Estaciones Asignadas (L-01)</h3>
                    </div>
                    <span class="text-xs font-mono font-bold text-[#16512c]">18 / 20 Presentes</span>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 text-xs">
                    @foreach(array_slice($labsList[0]['pcs'] ?? [], 0, 8) as $idxPc => $pcItem)
                        <button type="button"
                                onclick="window.MetalNetPC && window.MetalNetPC.abrirFicha('{{ $pcItem['id'] }}')"
                                class="p-2.5 rounded-xl border border-surface-container-highest text-left transition cursor-pointer hover:bg-surface-container bg-surface-container-low">
                            <div class="flex items-center justify-between">
                                <span class="font-mono font-extrabold text-primary">{{ $pcItem['codigo'] }}</span>
                                <span class="w-2.5 h-2.5 rounded-full {{ $pcItem['estado'] === 'mantenimiento' ? 'bg-[#EF4444]' : ($pcItem['estado'] === 'ocupado' || $pcItem['estado'] === 'ocupada' ? 'bg-[#F59E0B]' : 'bg-[#2F664F]') }}"></span>
                            </div>
                            <span class="text-[10px] font-semibold block truncate mt-1 text-on-surface">Alu #{{ 101 + $idxPc }}</span>
                            <span class="text-[9.5px] font-mono block text-outline">Asistencia OK</span>
                        </button>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

@foreach($labsList as $idx => $lab)
    <template id="lab-map-tpl-{{ $idx }}">
        @include('partials.lab-sala', ['lab' => $lab])
    </template>
@endforeach

@include('partials.bm-interactivo')
@endsection
