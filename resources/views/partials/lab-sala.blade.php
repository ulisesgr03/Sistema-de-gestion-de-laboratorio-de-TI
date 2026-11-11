{{--
    Vista detallada de aula de un laboratorio con su cuadrícula interactiva PC-01 a PC-20,
    detalles de sesión, docente, subred IP, hardware y software · MetalNet.
--}}
@php
    $uid = ($uid ?? 'sala') . '-' . $lab['id'];
@endphp
<div class="space-y-4">
    <!-- Barra superior de la sala con metadatos técnicos completos -->
    <div class="p-4 bg-surface-container-low rounded-xl border border-surface-container-highest flex flex-col lg:flex-row lg:items-center justify-between gap-3">
        <div class="flex items-center gap-3.5 min-w-0">
            <div class="w-12 h-12 rounded-xl bg-primary text-on-primary flex items-center justify-center font-extrabold text-sm shrink-0 font-mono">
                {{ $lab['codigo'] }}
            </div>
            <div class="min-w-0">
                <div class="flex flex-wrap items-center gap-2">
                    <h3 class="text-base font-bold text-on-surface truncate">{{ $lab['nombre'] }}</h3>
                    <span class="px-2.5 py-0.5 text-[11px] rounded-md font-bold {{ $lab['badge_color'] }}">
                        {{ $lab['estado_badge'] }}
                    </span>
                    <span class="px-2 py-0.5 rounded bg-surface-container text-on-surface-variant font-mono text-[11px] font-bold border border-surface-container-highest">
                        Subred {{ $lab['red'] }}
                    </span>
                </div>
                <div class="text-xs text-on-surface-variant flex flex-wrap items-center gap-x-3 gap-y-1 mt-1">
                    <span class="inline-flex items-center gap-1">
                        <svg class="w-3.5 h-3.5 text-primary" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
                        {{ $lab['ubicacion'] }} ({{ $lab['planta'] }})
                    </span>
                    <span class="text-outline-variant">·</span>
                    <span>Docente: <strong class="text-on-surface">{{ $lab['docente'] }}</strong> ({{ $lab['asignatura'] }} · {{ $lab['grupo'] }})</span>
                </div>
            </div>
        </div>

        <!-- Simbología contextual de 3 estados -->
        <div class="mn-legend-capsule shrink-0">
            <span class="mn-legend-item">
                <span class="mn-legend-dot mn-legend-dot--free"></span>
                <span>Disponible (<strong class="font-mono text-status-available" data-lab-count="{{ $lab['id'] }}:disponible">{{ $lab['conteo']['disponible'] }}</strong>)</span>
            </span>
            <span class="mn-legend-item">
                <span class="mn-legend-dot mn-legend-dot--busy"></span>
                <span>Ocupado (<strong class="font-mono text-status-occupied" data-lab-count="{{ $lab['id'] }}:ocupado">{{ $lab['conteo']['ocupado'] }}</strong>)</span>
            </span>
            <span class="mn-legend-item">
                <span class="mn-legend-dot mn-legend-dot--down"></span>
                <span>No disponible (<strong class="font-mono text-status-unavailable" data-lab-count="{{ $lab['id'] }}:mantenimiento">{{ $lab['conteo']['mantenimiento'] }}</strong>)</span>
            </span>
        </div>
    </div>

    <!-- Especificaciones completas de Hardware, Software y Sesión (movidas aquí para desaturar /salas) -->
    <div class="p-4 bg-surface-container-low border border-surface-container-highest rounded-xl text-xs grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
            <span class="flex items-center gap-1.5 text-[10px] font-bold uppercase tracking-wider text-tertiary mb-1.5">
                <svg class="w-3.5 h-3.5 text-primary" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                Sesión Actual y Red
            </span>
            <p class="font-bold text-on-surface">{{ $lab['asignatura'] }}</p>
            <p class="text-on-surface-variant mt-0.5">{{ $lab['docente'] }} · Grupo {{ $lab['grupo'] }}</p>
            <p class="font-mono text-[11px] text-primary font-semibold mt-1">IPv4 VLAN: {{ $lab['red'] }} · Próximo cambio: {{ $lab['proximo_cambio'] }}</p>
        </div>
        <div>
            <span class="flex items-center gap-1.5 text-[10px] font-bold uppercase tracking-wider text-tertiary mb-1.5">
                <svg class="w-3.5 h-3.5 text-primary" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="4" y="4" width="16" height="16" rx="2"/><rect x="9" y="9" width="6" height="6"/></svg>
                Hardware Instalado por Estación
            </span>
            <div class="flex flex-wrap gap-1.5">
                @foreach($lab['hardware_lista'] as $hw)
                    <span class="bm-sw-chip">{{ $hw }}</span>
                @endforeach
            </div>
        </div>
        <div>
            <span class="flex items-center gap-1.5 text-[10px] font-bold uppercase tracking-wider text-tertiary mb-1.5">
                <svg class="w-3.5 h-3.5 text-primary" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="4 17 10 11 4 5"/><line x1="12" x2="20" y1="19" y2="19"/></svg>
                Software Homologado en Sala
            </span>
            <div class="flex flex-wrap gap-1.5">
                @foreach($lab['software_lista'] as $sw)
                    <span class="bm-sw-chip">{{ $sw }}</span>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Plano visual del laboratorio con cuadrícula PC-01 a PC-20 -->
    <div class="relative bg-surface-container-lowest border border-surface-container-highest rounded-2xl p-4 sm:p-6">
        <div class="flex flex-wrap items-center justify-between gap-2 pb-3 mb-5 border-b border-surface-container-highest text-xs font-bold text-on-surface-variant">
            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-surface-container-low border border-surface-container-highest text-on-surface">
                <svg class="w-4 h-4 text-primary" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8M12 17v4"/></svg>
                <span>Estrado Docente y Pizarrón ({{ $lab['canon_estado'] }})</span>
            </div>
            <button type="button"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-surface-container-low hover:bg-surface-container border border-surface-container-highest text-on-surface-variant hover:text-on-surface transition-colors cursor-help"
                    data-tip="Switch Core Catalyst Gigabit · VLAN {{ $lab['id'] }}0 · Subred {{ $lab['red'] }} · Enlace 10 Gbps Fibra.">
                <svg class="w-4 h-4 text-primary" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="2" width="20" height="8" rx="2"/><rect x="2" y="14" width="20" height="8" rx="2"/><line x1="6" x2="6.01" y1="6" y2="6"/><line x1="6" x2="6.01" y1="18" y2="18"/></svg>
                <span>Rack IDF · SW-{{ $lab['codigo'] }} ({{ $lab['red'] }})</span>
            </button>
        </div>

        @include('partials.pc-grid', ['lab' => $lab, 'variant' => 'full'])

        <div class="mt-5 pt-3 border-t border-surface-container-highest flex flex-wrap items-center justify-between gap-2 text-[11px] text-on-surface-variant">
            <span class="inline-flex items-center gap-1.5">
                <svg class="w-4 h-4 text-primary" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" x2="3" y1="12" y2="12"/></svg>
                Haz clic en cualquier estación PC-01 a PC-20 para inspeccionar su hardware/software, apartarla o reportar fallo
            </span>
            <span class="font-mono font-bold text-primary">Subred {{ $lab['red'] }} · 20 estaciones</span>
        </div>
    </div>
</div>
