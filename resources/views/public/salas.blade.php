@extends('layouts.public')
@section('title', 'Laboratorios de Cómputo · ' . config('app.institution_name', 'MetalNet'))

@section('content')

@php
    $totalPcs     = array_sum(array_column($laboratorios, 'total'));
    $totalLibres  = array_sum(array_column($laboratorios, 'libres'));
    $totalOcup    = array_sum(array_column($laboratorios, 'ocupadas'));
    $totalMant    = array_sum(array_column($laboratorios, 'mantenimiento'));
    $totalConCanon = count(array_filter($laboratorios, fn($l) => !empty($l['canon'])));
@endphp

{{-- ── ENCABEZADO EDITORIAL METALNET (PALETA OFICIAL STITCH) ─────────────────── --}}
<div class="border border-surface-container-highest bg-surface-container-lowest rounded-2xl mb-6 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-5">
            <div>
                <div class="flex items-center gap-2 mb-2">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded text-[10px] font-bold uppercase tracking-widest border border-[#b3f1be] bg-[#e8f5ec] text-[#16512c]">
                        <span class="w-1.5 h-1.5 rounded-full bg-primary animate-pulse"></span>
                        Monitoreo en Tiempo Real
                    </span>
                    <span class="text-[11px] font-semibold uppercase tracking-widest text-outline">
                        {{ config('app.institution_full', 'MetalNet · Laboratorios de Ingeniería en Sistemas') }}
                    </span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-on-surface">
                    Disponibilidad de Laboratorios
                </h1>
                <p class="text-xs sm:text-sm mt-1 max-w-2xl text-on-surface-variant">
                    Consulta el estado de las 20 estaciones por sala. Selecciona cualquier computadora para apartarla, reportar incidencias o inspeccionar especificaciones técnicas en tiempo real.
                </p>
            </div>

            {{-- Resumen global con los 3 colores semánticos --}}
            <div class="grid grid-cols-2 sm:grid-cols-5 gap-2.5">
                <div class="px-3.5 py-2.5 rounded-xl border border-surface-container-highest bg-surface-container-low text-center">
                    <p class="text-xl font-black leading-none font-mono text-on-surface">{{ count($laboratorios) }}</p>
                    <p class="text-[10px] font-bold uppercase tracking-wider mt-1 text-outline">Salas</p>
                </div>
                <div class="px-3.5 py-2.5 rounded-xl border border-[#b3f1be] bg-[#e8f5ec] text-center">
                    <p class="text-xl font-black leading-none font-mono text-[#16512c]">{{ $totalLibres }}</p>
                    <p class="text-[10px] font-bold uppercase tracking-wider mt-1 text-[#16512c]">Disponibles</p>
                </div>
                <div class="px-3.5 py-2.5 rounded-xl border border-[#fde68a] bg-[#fef3c7] text-center">
                    <p class="text-xl font-black leading-none font-mono text-[#92400e]">{{ $totalOcup }}</p>
                    <p class="text-[10px] font-bold uppercase tracking-wider mt-1 text-[#92400e]">Ocupadas</p>
                </div>
                <div class="px-3.5 py-2.5 rounded-xl border border-[#fca5a5] bg-[#ffdad6] text-center">
                    <p class="text-xl font-black leading-none font-mono text-[#991b1b]">{{ $totalMant }}</p>
                    <p class="text-[10px] font-bold uppercase tracking-wider mt-1 text-[#991b1b]">No disp.</p>
                </div>
                <div class="col-span-2 sm:col-span-1 px-3.5 py-2.5 rounded-xl border border-surface-container-highest bg-surface-container-low text-center">
                    <p class="text-xl font-black leading-none font-mono text-primary">{{ $totalConCanon }}/{{ count($laboratorios) }}</p>
                    <p class="text-[10px] font-bold uppercase tracking-wider mt-1 text-outline">Cañón Activo</p>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ── BARRA DE FILTROS Y SIMBOLOGÍA DE ESTADOS ────────────────────────────── --}}
<div class="sticky top-16 z-20 border border-surface-container-highest bg-surface-container-lowest/95 backdrop-blur-md mb-6 py-3 px-4 rounded-xl shadow-sm">
    <div class="max-w-7xl mx-auto flex flex-wrap items-center justify-between gap-3">
        {{-- Filtros rápidos --}}
        <div class="flex flex-wrap items-center gap-1.5">
            <span class="text-[11px] font-bold uppercase tracking-wider mr-1 text-outline">Filtrar salas:</span>
            <button type="button" onclick="filtrarSalas('todos')" id="btn-todos"
                    class="filtro-btn active text-xs font-bold px-3 py-1.5 rounded-lg border transition">
                Todos ({{ count($laboratorios) }})
            </button>
            <button type="button" onclick="filtrarSalas('disponible')" id="btn-disponible"
                    class="filtro-btn text-xs font-semibold px-3 py-1.5 rounded-lg border transition">
                Con lugares libres
            </button>
            <button type="button" onclick="filtrarSalas('clase')" id="btn-clase"
                    class="filtro-btn text-xs font-semibold px-3 py-1.5 rounded-lg border transition">
                En sesión activa
            </button>
            <button type="button" onclick="filtrarSalas('canon')" id="btn-canon"
                    class="filtro-btn text-xs font-semibold px-3 py-1.5 rounded-lg border transition">
                Con cañón ({{ $totalConCanon }})
            </button>
        </div>

        {{-- Cápsula compacta de simbología de 3 colores (Verde, Amarillo, Rojo) --}}
        <div class="mn-legend-capsule" aria-label="Simbología de disponibilidad">
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
</div>

{{-- ── CUADRÍCULA DE LOS 10 LABORATORIOS (SIN IMÁGENES) ────────────────────── --}}
<div class="max-w-7xl mx-auto pb-12">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6" id="grid-salas">

        @foreach($laboratorios as $idx => $lab)
        @php
            $enClase = ($lab['en_sesion_docente'] ?? false) || in_array($lab['tipo'] ?? '', ['clase', 'taller']) || str_contains(strtolower($lab['estado_badge'] ?? ''), 'clase') || str_contains(strtolower($lab['estado_general'] ?? ''), 'clase');
            $esDisponible = ($lab['libres'] ?? 0) > 0 && !$enClase;
            $tieneCanon   = !empty($lab['canon']);
            $labNumero    = $idx + 1;

            if ($enClase) {
                $badgeBg = '#fef3c7';
                $badgeBorder = '#fde68a';
                $badgeText = '#92400e';
                $dotColor = '#f59e0b';
            } elseif ($lab['estado_general'] === 'Mantenimiento') {
                $badgeBg = '#ffdad6';
                $badgeBorder = '#fca5a5';
                $badgeText = '#991b1b';
                $dotColor = '#ba1a1a';
            } else {
                $badgeBg = '#e8f5ec';
                $badgeBorder = '#b3f1be';
                $badgeText = '#16512c';
                $dotColor = '#2f664f';
            }
        @endphp

        <article class="sala-card rounded-2xl border border-surface-container-highest bg-surface-container-lowest overflow-hidden transition-all duration-200 flex flex-col justify-between shadow-sm hover:shadow-md"
                 data-disponible="{{ $esDisponible ? '1' : '0' }}"
                 data-clase="{{ $enClase ? '1' : '0' }}"
                 data-canon="{{ $tieneCanon ? '1' : '0' }}">

            <div>
                {{-- 1. Cabecera limpia y técnica sin imágenes --}}
                <div class="p-5 border-b border-surface-container-highest bg-surface-container-low flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div class="min-w-0 flex-1">
                        <div class="flex flex-wrap items-center gap-2 mb-2">
                            <span class="text-[11px] font-mono font-bold px-2.5 py-0.5 rounded border border-surface-container-highest bg-surface-container text-primary">
                                {{ $lab['codigo'] }}
                            </span>

                            {{-- Estado general en 3 colores semánticos --}}
                            <span class="inline-flex items-center gap-1.5 text-[11px] font-bold px-2.5 py-0.5 rounded-full border shadow-xs"
                                  style="background: {{ $badgeBg }}; border-color: {{ $badgeBorder }}; color: {{ $badgeText }};">
                                <span class="w-2 h-2 rounded-full" style="background: {{ $dotColor }};"></span>
                                {{ $lab['estado_badge'] ?? $lab['estado_general'] }}
                            </span>

                            {{-- Estado del Cañón --}}
                            @if($tieneCanon)
                            <span class="inline-flex items-center gap-1 text-[11px] font-semibold px-2.5 py-0.5 rounded border border-[#b3f1be] bg-[#e8f5ec] text-[#16512c]"
                                  title="Proyector / Cañón operativo">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                </svg>
                                Cañón Activo
                            </span>
                            @else
                            <span class="inline-flex items-center gap-1 text-[11px] font-semibold px-2.5 py-0.5 rounded border border-surface-container-highest bg-surface-container text-outline"
                                  title="Sala sin proyector">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                </svg>
                                Sin Cañón
                            </span>
                            @endif
                        </div>

                        <h2 class="text-base sm:text-lg font-extrabold leading-snug truncate text-on-surface">
                            {{ $lab['nombre'] }}
                        </h2>
                        <p class="text-xs mt-0.5 truncate text-on-surface-variant font-medium">
                            {{ $lab['especialidad'] }}
                        </p>
                    </div>

                    {{-- Acceso rápido a ficha y ubicación técnica (sin imágenes) --}}
                    <div class="flex sm:flex-col items-center sm:items-end justify-between sm:justify-center gap-2 shrink-0">
                        <button type="button"
                                data-open-map="{{ $idx }}"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-surface-container-highest bg-surface-container-lowest hover:bg-surface-container text-primary text-xs font-bold transition shadow-xs cursor-pointer"
                                title="Ver detalles técnicos y plano de {{ $lab['nombre'] }}">
                            <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                            <span>Ficha y Plano</span>
                        </button>
                        <span class="text-[10.5px] font-mono text-outline font-semibold">
                            {{ $lab['planta'] ?? 'Planta Baja' }}
                        </span>
                    </div>
                </div>

                {{-- 2. Indicadores resumidos (Capacidad, Libres, Ocupadas, No disp.) --}}
                <div class="px-5 py-3.5 grid grid-cols-5 gap-2 border-b border-surface-container-highest bg-surface-container-lowest">
                    <div class="rounded-xl p-2 text-center border border-surface-container-highest bg-surface-container-low">
                        <p class="text-base font-black font-mono leading-none text-on-surface">{{ $lab['total'] }}</p>
                        <p class="text-[9px] font-bold uppercase tracking-wider mt-1 text-outline">Total</p>
                    </div>
                    <div class="rounded-xl p-2 text-center border border-[#b3f1be] bg-[#e8f5ec]">
                        <p class="text-base font-black font-mono leading-none text-[#16512c]">{{ $lab['libres'] }}</p>
                        <p class="text-[9px] font-bold uppercase tracking-wider mt-1 text-[#16512c]">Libres</p>
                    </div>
                    <div class="rounded-xl p-2 text-center border border-[#fde68a] bg-[#fef3c7]">
                        <p class="text-base font-black font-mono leading-none text-[#92400e]">{{ $lab['ocupadas'] }}</p>
                        <p class="text-[9px] font-bold uppercase tracking-wider mt-1 text-[#92400e]">En uso</p>
                    </div>
                    <div class="rounded-xl p-2 text-center border border-[#fca5a5] bg-[#ffdad6]">
                        <p class="text-base font-black font-mono leading-none text-[#991b1b]">{{ $lab['mantenimiento'] }}</p>
                        <p class="text-[9px] font-bold uppercase tracking-wider mt-1 text-[#991b1b]">No disp.</p>
                    </div>
                    <div class="rounded-xl p-2 text-center border border-surface-container-highest bg-surface-container-low flex flex-col justify-center">
                        <p class="text-base font-black font-mono leading-none text-on-surface">{{ $lab['horas_libres'] ?? 6 }}h</p>
                        <p class="text-[9px] font-bold uppercase tracking-wider mt-1 text-outline">Libres hoy</p>
                    </div>
                </div>

                {{-- 3. Cuadrícula limpia de las 20 estaciones --}}
                <div class="p-5">
                    <div class="flex flex-wrap items-center justify-between gap-2 mb-3">
                        <span class="text-[11px] font-bold uppercase tracking-wider text-primary">
                            Estaciones de Cómputo (01–20)
                        </span>
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

                    <div class="p-3.5 rounded-xl border border-surface-container-highest bg-surface-container-low">
                        @include('partials.pc-grid', ['lab' => $lab, 'variant' => 'mini'])
                    </div>
                </div>
            </div>

            {{-- 4. Pie con botones de acción ("Reservar ahora", "Horarios", "Detalles") --}}
            <div class="px-5 py-3.5 border-t border-surface-container-highest flex flex-wrap items-center justify-between gap-3 bg-surface-container-lowest">
                <div class="flex items-center gap-2 text-xs truncate text-outline">
                    <span class="font-semibold text-on-surface">Horarios libres:</span>
                    @if(!empty($lab['horarios_libres']))
                        <span class="font-mono text-[11px] truncate text-primary font-bold">{{ implode(', ', array_slice($lab['horarios_libres'], 0, 2)) }}{{ count($lab['horarios_libres']) > 2 ? ' +' . (count($lab['horarios_libres']) - 2) : '' }}</span>
                    @else
                        <span class="text-[11px] text-status-occupied font-semibold">Jornada completa ocupada</span>
                    @endif
                </div>

                <div class="flex items-center gap-2 shrink-0">
                    @if($enClase)
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold border border-[#fde68a] bg-[#fef3c7] text-[#92400e] cursor-not-allowed select-none shadow-xs"
                              title="Sala en sesión activa con docente ({{ $lab['asignatura'] ?? 'Clase' }} · {{ $lab['docente'] ?? 'Docente' }}). Apartado individual inhabilitado.">
                            <svg class="w-3.5 h-3.5 text-[#f59e0b] shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                            <span class="truncate max-w-[220px]">{{ $lab['titulo_sesion_docente'] ?? ('En Clase: ' . ($lab['asignatura'] ?? 'Sesión') . ' · ' . ($lab['docente'] ?? 'Docente')) }}</span>
                        </span>
                    @else
                        <button type="button"
                                onclick="window.abrirReservaContextual('{{ $lab['id'] }}', '{{ $lab['codigo'] }}')"
                                class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg text-xs font-black text-white transition hover:bg-primary-container cursor-pointer bg-primary shadow-sm">
                            <svg class="w-3.5 h-3.5 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                            <span>Reservar ahora</span>
                        </button>
                    @endif

                    <a href="{{ route('public.horarios', ['laboratorio_id' => $lab['id']]) }}"
                       class="px-3 py-1.5 rounded-lg text-xs font-semibold border border-surface-container-highest transition hover:bg-surface-container bg-surface-container-low text-on-surface">
                        Horarios
                    </a>

                    <button type="button"
                            data-open-map="{{ $idx }}"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold text-primary border border-surface-container-highest bg-surface-container-low transition hover:bg-surface-container cursor-pointer">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                        </svg>
                        Detalles
                    </button>
                </div>
            </div>

        </article>

        {{-- Plantilla oculta para el modal de Plano --}}
        <template id="lab-map-tpl-{{ $idx }}">
            @include('partials.lab-sala', ['lab' => $lab])
        </template>
        @endforeach

    </div>
</div>

@include('partials.bm-interactivo')

<style>
.filtro-btn {
    background: #f5f4ec;
    color: #404944;
    border-color: #e4e3db;
}
.filtro-btn:hover {
    background: #efeee7;
    color: #1b1c18;
    border-color: #c0c9c2;
}
.filtro-btn.active {
    background: #2f664f;
    color: #ffffff;
    border-color: #2f664f;
}
</style>

<script>
function filtrarSalas(tipo) {
    document.querySelectorAll('.filtro-btn').forEach(b => b.classList.remove('active'));
    const activeBtn = document.getElementById('btn-' + tipo);
    if (activeBtn) activeBtn.classList.add('active');

    document.querySelectorAll('.sala-card').forEach(card => {
        if (tipo === 'todos') {
            card.style.display = '';
        } else if (tipo === 'disponible') {
            card.style.display = card.dataset.disponible === '1' ? '' : 'none';
        } else if (tipo === 'clase') {
            card.style.display = card.dataset.clase === '1' ? '' : 'none';
        } else if (tipo === 'canon') {
            card.style.display = card.dataset.canon === '1' ? '' : 'none';
        }
    });
}

window.abrirReservaContextual = function(labId, labCodigo) {
    if (!window.BM_DATA.loggedIn) {
        if (window.MetalNetUI && window.MetalNetUI.openModal) {
            const lf = document.getElementById('modal-login-forzoso');
            if (lf) {
                const el = document.getElementById('login-forzoso-pc');
                if (el) el.textContent = labCodigo || ('LAB ' + labId);
                window.MetalNetUI.openModal('modal-login-forzoso');
                return;
            }
        }
        window.location.href = window.BM_DATA.urls.login + '?motivo=reservar&sala=' + encodeURIComponent(labCodigo || labId);
        return;
    }

    const role = window.BM_DATA.userRole || 'alumno';
    if (role === 'docente' || role === 'admin') {
        const rbLabId = document.getElementById('rb-lab-id');
        if (rbLabId) rbLabId.value = labId;
        if (window.MetalNetUI) {
            window.MetalNetUI.openModal('modal-reservar-bloque-libre');
        }
    } else {
        const labs = window.BM_DATA.labs || [];
        const currentLab = labs.find(l => String(l.id) === String(labId) || String(l.codigo) === String(labCodigo));
        if (currentLab && (currentLab.en_sesion_docente || (currentLab.conteo && currentLab.conteo.disponible === 0))) {
            if (window.MetalNetUI && window.MetalNetUI.toast) {
                window.MetalNetUI.toast('Esta sala está ocupada por una sesión docente en curso (' + (currentLab.asignatura || 'Clase') + ' · Prof. ' + (currentLab.docente || 'Docente') + '). No se permiten apartados individuales.', 'warning');
            }
            return;
        }
        if (currentLab && currentLab.pcs) {
            const freePc = currentLab.pcs.find(p => p.estado === 'disponible' || p.estado === 'libre');
            if (freePc && window.MetalNetPC && window.MetalNetPC.abrirFicha) {
                window.MetalNetPC.abrirFicha(freePc.id);
                return;
            }
        }
        if (window.MetalNetPC && window.MetalNetPC.abrirMapa) {
            window.MetalNetPC.abrirMapa(labId);
        }
    }
};
</script>

@endsection
