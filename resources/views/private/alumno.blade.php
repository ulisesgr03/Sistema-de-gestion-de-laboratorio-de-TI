@extends('layouts.private')

@section('title', 'Panel de Alumno · ' . config('app.institution_name', 'MetalNet'))

@section('content')
@php
    $labsList = $laboratorios ?? $bmLabs ?? [];
    $misReservasList = $misReservas ?? collect();
    $misSolicitudesList = $misSolicitudes ?? collect();
    $misIncidenciasList = $misIncidencias ?? collect();
@endphp

<div class="space-y-6">
    {{-- Encabezado y Acciones Rápidas del Alumno · Paleta Oficial Stitch --}}
    <div class="p-5 rounded-2xl border border-surface-container-highest flex flex-wrap items-center justify-between gap-4 bg-surface-container-lowest shadow-sm">
        <div class="flex items-center gap-3.5">
            <div class="w-12 h-12 rounded-2xl border border-[#b3f1be] flex items-center justify-center bg-[#e8f5ec] text-primary">
                <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <span class="px-2.5 py-0.5 rounded-md border border-[#b3f1be] text-[10px] font-extrabold uppercase bg-[#e8f5ec] text-[#16512c]">
                        Rol: Alumno
                    </span>
                    <span class="text-xs font-mono text-outline">{{ $usuario->matricula ?? $usuario->email }}</span>
                </div>
                <h1 class="text-xl font-black mt-0.5 text-on-surface">
                    Estación de Prácticas Libres MetalNet · {{ $usuario->name }}
                </h1>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('software.index') }}"
               class="px-4 py-2.5 rounded-xl text-xs font-extrabold inline-flex items-center gap-1.5 transition hover:bg-surface-container border border-surface-container-highest bg-surface-container-low text-on-surface">
                <svg class="w-4 h-4 text-primary" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="4 17 10 11 4 5"/><line x1="12" x2="20" y1="19" y2="19"/></svg>
                Catálogo de Software
            </a>
            <a href="{{ route('public.averia') }}"
               class="px-4 py-2.5 rounded-xl border border-[#fca5a5] text-xs font-extrabold inline-flex items-center gap-1.5 transition hover:bg-[#ffdad6] bg-[#fff8f7] text-[#991b1b]">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><line x1="12" x2="12" y1="9" y2="13"/><line x1="12" x2="12.01" y1="17" y2="17"/></svg>
                Reportar Falla en PC
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        {{-- Columna Izquierda (7 cols): Mapa Visual de Selección de Computadora para Práctica Libre --}}
        <div class="lg:col-span-7 space-y-4">
            <div class="p-5 rounded-2xl border border-surface-container-highest bg-surface-container-lowest shadow-sm">
                <div class="flex flex-wrap items-center justify-between gap-2 pb-3 mb-4 border-b border-surface-container-highest">
                    <div>
                        <span class="text-[10px] font-extrabold uppercase tracking-wider block text-primary">Selección Visual Interactiva</span>
                        <h2 class="text-base font-extrabold text-on-surface">Apartar Computadora Individual en Mapa de Laboratorio</h2>
                    </div>
                    <div class="mn-legend-capsule">
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

                {{-- Pestañas de los 10 Laboratorios --}}
                <div class="flex items-center gap-1.5 overflow-x-auto pb-2 mb-4">
                    @foreach($labsList as $i => $labItem)
                        <button type="button"
                                onclick="window.switchAluTab('alu-lab-{{ $labItem['id'] }}', this)"
                                class="alu-tab-btn px-3 py-1.5 rounded-lg border text-xs font-bold flex items-center gap-1.5 shrink-0 cursor-pointer transition {{ $i === 0 ? 'bg-primary text-white border-primary' : 'bg-surface-container-low text-on-surface-variant border-surface-container-highest hover:bg-surface-container hover:text-on-surface' }}">
                            <span class="font-mono font-extrabold">{{ $labItem['codigo'] }}</span>
                            <span class="text-[10px] font-mono {{ $i === 0 ? 'text-[#b3f1be]' : 'text-primary' }}">{{ $labItem['conteo']['disponible'] }}L</span>
                        </button>
                    @endforeach
                </div>

                {{-- Planos de Estaciones por Sala --}}
                <div id="alumno-lab-tabs">
                    @foreach($labsList as $i => $labItem)
                        <div data-tab-panel="alu-lab-{{ $labItem['id'] }}" class="{{ $i === 0 ? '' : 'hidden' }} space-y-3">
                            <div class="p-3 rounded-xl border border-surface-container-highest flex flex-wrap items-center justify-between gap-2 text-xs bg-surface-container-low">
                                <div>
                                    <strong class="text-on-surface">{{ $labItem['nombre'] }}</strong>
                                    <span class="block text-[11px] text-on-surface-variant">{{ $labItem['ubicacion'] }} · {{ $labItem['hardware_corto'] }}</span>
                                </div>
                                <span class="px-2.5 py-1 rounded-md border border-[#b3f1be] font-mono text-[11px] font-bold bg-[#e8f5ec] text-[#16512c]">
                                    Haz clic en cualquier PC verde para apartarla
                                </span>
                            </div>

                            <div class="p-4 rounded-xl border border-surface-container-highest bg-surface-container-lowest">
                                @include('partials.pc-grid', ['lab' => $labItem, 'variant' => 'full'])
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Columna Derecha (5 cols): Estado en Tiempo Real de Mis Reservas, Solicitudes y Reportes --}}
        <div class="lg:col-span-5 space-y-5">
            <div class="p-5 rounded-2xl border border-surface-container-highest bg-surface-container-lowest shadow-sm">
                <h3 class="text-sm font-extrabold uppercase tracking-wider mb-3 flex items-center gap-2 text-primary">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/></svg>
                    Solicitar Software, Hardware o Mesa de Trabajo
                </h3>
                <form method="POST" action="{{ route('panel.solicitar-recurso') }}" class="space-y-3">
                    @csrf
                    <div class="grid grid-cols-2 gap-2.5">
                        <div>
                            <label class="block text-[10px] font-bold uppercase mb-1 text-on-surface-variant">Categoría</label>
                            <select name="tipo"
                                    class="w-full h-9 px-2.5 rounded-lg border border-surface-container-highest text-xs font-bold focus:outline-none focus:border-primary bg-surface-container-lowest text-on-surface">
                                <option value="Software">Instalación / Update Software</option>
                                <option value="Hardware">Préstamo Kit Hardware</option>
                                <option value="Mesa">Asignación Mesa Trabajo</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold uppercase mb-1 text-on-surface-variant">Sala</label>
                            <select name="sala"
                                    class="w-full h-9 px-2.5 rounded-lg border border-surface-container-highest text-xs font-bold focus:outline-none focus:border-primary bg-surface-container-lowest text-on-surface">
                                @foreach($labsList as $l)
                                    <option value="{{ $l['codigo'] }}">{{ $l['codigo'] }} · {{ $l['nombre_corto'] }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div>
                        <input type="text" name="recurso" required placeholder="Ej. Kit Arduino Mega + Analizador Lógico o PyTorch 2.6"
                               class="w-full h-9 px-3 rounded-lg border border-surface-container-highest text-xs focus:outline-none focus:border-primary bg-surface-container-lowest text-on-surface">
                    </div>
                    <div>
                        <input type="text" name="descripcion" required placeholder="Detalle o justificación académica..."
                               class="w-full h-9 px-3 rounded-lg border border-surface-container-highest text-xs focus:outline-none focus:border-primary bg-surface-container-lowest text-on-surface">
                    </div>
                    <button type="submit"
                            class="w-full py-2 rounded-lg text-xs font-black cursor-pointer transition hover:bg-primary-container shadow-xs bg-primary text-white">
                        Enviar Solicitud en Tiempo Real
                    </button>
                </form>
            </div>

            {{-- Estado en Tiempo Real de Mis Solicitudes y Reservas --}}
            <div class="p-5 rounded-2xl border border-surface-container-highest bg-surface-container-lowest shadow-sm">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-sm font-extrabold uppercase tracking-wider text-primary">Estado de Mis Solicitudes</h3>
                    <span class="px-2 py-0.5 rounded border border-[#b3f1be] font-mono text-[11px] font-bold bg-[#e8f5ec] text-[#16512c]">
                        {{ $misSolicitudesList->count() }} activas
                    </span>
                </div>
                <div class="space-y-2.5 max-h-64 overflow-y-auto">
                    @forelse($misSolicitudesList as $sol)
                        @php
                            $est = strtolower($sol->estado ?? 'pendiente');
                            $badgeClass = ($est === 'aprobado' || $est === 'completado')
                                ? 'bg-[#e8f5ec] text-[#16512c] border-[#b3f1be]'
                                : ($est === 'rechazado'
                                    ? 'bg-[#ffdad6] text-[#991b1b] border-[#fca5a5]'
                                    : 'bg-[#fef3c7] text-[#92400e] border-[#fde68a]');
                        @endphp
                        <div class="p-3 rounded-xl border border-surface-container-highest flex items-start justify-between gap-2 text-xs bg-surface-container-low">
                            <div class="min-w-0">
                                <span class="font-mono text-[10px] font-bold text-primary">{{ $sol->folio }}</span>
                                <p class="font-bold truncate text-on-surface">{{ $sol->titulo }}</p>
                                <p class="text-[11px] truncate text-on-surface-variant">{{ $sol->descripcion }}</p>
                            </div>
                            <span class="px-2 py-0.5 rounded border text-[10px] font-extrabold uppercase shrink-0 {{ $badgeClass }}">
                                {{ $sol->estado }}
                            </span>
                        </div>
                    @empty
                        <p class="text-xs text-outline">Aún no tienes solicitudes registradas.</p>
                    @endforelse
                </div>
            </div>

            {{-- Reportes de Falla Levantados --}}
            <div class="p-5 rounded-2xl border border-surface-container-highest bg-surface-container-lowest shadow-sm">
                <h3 class="text-sm font-extrabold uppercase tracking-wider mb-3 text-[#991b1b]">Reportes de Falla en Seguimiento</h3>
                <div class="space-y-2 max-h-48 overflow-y-auto">
                    @forelse($misIncidenciasList as $inc)
                        <div class="p-2.5 rounded-xl border border-[#fca5a5] flex items-center justify-between gap-2 text-xs bg-[#fff8f7]">
                            <div class="min-w-0">
                                <span class="font-mono font-bold text-[#991b1b]">{{ $inc->estacion?->codigo ?? 'PC' }}</span>
                                <p class="font-semibold truncate text-on-surface">{{ $inc->titulo }}</p>
                            </div>
                            <span class="px-2 py-0.5 rounded border border-[#fca5a5] text-[10px] font-bold uppercase bg-[#ffdad6] text-[#991b1b]">
                                {{ $inc->estado }}
                            </span>
                        </div>
                    @empty
                        <p class="text-xs text-outline">Sin reportes de falla pendientes.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

@include('partials.bm-interactivo')

<script>
window.switchAluTab = function(panelKey, btnEl) {
    const container = document.getElementById('alumno-lab-tabs');
    if (!container) return;
    container.querySelectorAll('[data-tab-panel]').forEach(p => {
        p.classList.toggle('hidden', p.getAttribute('data-tab-panel') !== panelKey);
    });
    if (btnEl && btnEl.parentElement) {
        btnEl.parentElement.querySelectorAll('button').forEach(b => {
            const isTarget = (b === btnEl);
            b.className = 'alu-tab-btn px-3 py-1.5 rounded-lg border text-xs font-bold flex items-center gap-1.5 shrink-0 cursor-pointer transition ' +
                (isTarget ? 'bg-primary text-white border-primary' : 'bg-surface-container-low text-on-surface-variant border-surface-container-highest hover:bg-surface-container hover:text-on-surface');
        });
    }
};
</script>
@endsection
