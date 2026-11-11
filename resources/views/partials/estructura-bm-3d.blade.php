@php
    $labs3d = $labs ?? $bmLabs ?? [];
    $plantaBajaLabs = array_values(array_filter($labs3d, fn($l) => ($l['planta'] ?? 'Planta Baja') === 'Planta Baja'));
    $plantaAltaLabs = array_values(array_filter($labs3d, fn($l) => ($l['planta'] ?? '') === 'Planta Alta'));
    $totales3d = $bmTotales ?? ['disponible' => 88, 'ocupado' => 96, 'mantenimiento' => 16];
@endphp

<style>
    /* ===================================================================== */
    /* MOTOR ARQUITECTÓNICO 3D · METALNET · MATERIALES REALES DE CONSTRUCCIÓN */
    /* Cero Verde Arquitectónico · Alto Contraste · Monitores Emisivos       */
    /* ===================================================================== */

    /* Entorno exterior oscuro (#111827 / #1E242B) que hace resaltar la maqueta flotante */
    .bm-3d-stage {
        perspective: 1550px;
        perspective-origin: 50% 44%;
        background: radial-gradient(circle at 50% 35%, #1E242B 0%, #111827 70%, #0B0F17 100%);
        position: relative;
        overflow: hidden;
        user-select: none;
    }

    /* Rejilla técnica de fondo con líneas principales en gris neutro (#374151 / #4B5563) */
    .bm-3d-ground-grid {
        position: absolute;
        inset: -35%;
        background-image:
            linear-gradient(to right, rgba(75, 85, 99, 0.25) 1px, transparent 1px),
            linear-gradient(to bottom, rgba(75, 85, 99, 0.25) 1px, transparent 1px);
        background-size: 38px 38px;
        transform: rotateX(60deg) translateZ(-42px);
        pointer-events: none;
    }

    .bm-3d-rig {
        transform-style: preserve-3d;
        will-change: transform;
        position: relative;
        width: 1060px;
        height: 420px;
        margin: 0 auto;
    }

    /* Cimentación / Losa estructural de concreto basalto oscuro (#1F2937 con borde #374151) */
    .bm-3d-terracotta {
        position: absolute;
        inset: -18px -22px -20px -22px;
        background: linear-gradient(145deg, #1F2937 0%, #111827 100%);
        border: 2px solid #374151;
        border-radius: 6px;
        transform: translateZ(-14px);
        transform-style: preserve-3d;
        box-shadow:
            0 45px 70px -10px rgba(0, 0, 0, 0.75),
            0 20px 30px -8px rgba(0, 0, 0, 0.6);
    }
    .bm-3d-terracotta::after {
        content: '';
        position: absolute;
        left: -2px;
        right: -2px;
        bottom: -14px;
        height: 14px;
        background: linear-gradient(to bottom, #1F2937, #0B0F17);
        transform-origin: top;
        transform: rotateX(-90deg);
    }
    .bm-3d-terracotta::before {
        content: '';
        position: absolute;
        top: -2px;
        bottom: -2px;
        right: -14px;
        width: 14px;
        background: linear-gradient(to right, #1F2937, #0B0F17);
        transform-origin: left;
        transform: rotateY(90deg);
    }

    /* Pasillo central de circulación y zona frontal: Concreto pulido / porcelánico gris suave (#D5D8DC / #CFD4D9) con brillo satinado */
    .bm-3d-concrete-pad {
        position: absolute;
        inset: -8px -10px -10px -10px;
        background: linear-gradient(180deg, #D5D8DC 0%, #CFD4D9 100%);
        border: 1px solid #B0B7BD;
        border-radius: 4px;
        transform: translateZ(-5px);
        transform-style: preserve-3d;
        box-shadow: 0 10px 22px rgba(0, 0, 0, 0.35);
    }
    .bm-3d-concrete-pad::after {
        content: '';
        position: absolute;
        left: 0;
        right: 0;
        bottom: -8px;
        height: 8px;
        background: #B0B7BD;
        transform-origin: top;
        transform: rotateX(-90deg);
    }

    /* Estructura envolvente del edificio en concreto arquitectónico gris neutro */
    .bm-3d-building-shell {
        position: absolute;
        inset: 0;
        background: #D5D8DC;
        transform-style: preserve-3d;
        border-radius: 4px;
        border: 1px solid #B0B7BD;
    }

    /* Muro posterior en yeso institucional blanco humo / gris perla (#ECEFF1 / #E2E6EA) con remate grafito oscuro (#374151) */
    .bm-3d-wall-back {
        position: absolute;
        left: 0;
        right: 0;
        top: 0;
        height: 46px;
        background: linear-gradient(to bottom, #FFFFFF 0%, #ECEFF1 100%);
        border-top: 4px solid #374151;
        border-bottom: 1px solid #9CA3AF;
        transform-origin: top;
        transform: rotateX(-90deg) translateY(-46px);
        pointer-events: none;
    }

    /* Muro lateral izquierdo */
    .bm-3d-wall-left {
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 46px;
        background: linear-gradient(to right, #FFFFFF 0%, #ECEFF1 100%);
        border-left: 4px solid #374151;
        transform-origin: left;
        transform: rotateY(-90deg) translateX(-46px);
        pointer-events: none;
    }

    /* Muro lateral derecho */
    .bm-3d-wall-right {
        position: absolute;
        right: 0;
        top: 0;
        bottom: 0;
        width: 46px;
        background: linear-gradient(to left, #FFFFFF 0%, #ECEFF1 100%);
        border-right: 4px solid #374151;
        transform-origin: right;
        transform: rotateY(90deg) translateX(46px);
        pointer-events: none;
    }

    /* Fachada frontal arquitectónica en gris perla con remate grafito #374151 */
    .bm-3d-wall-front {
        position: absolute;
        left: 0;
        right: 0;
        bottom: 0;
        height: 42px;
        background: linear-gradient(to bottom, #ECEFF1 0%, #D5D8DC 100%);
        border-top: 5px solid #374151;
        border-bottom: 2px solid #9CA3AF;
        transform-origin: bottom;
        transform: rotateX(90deg) translateY(42px);
        pointer-events: none;
        display: flex;
        align-items: center;
        justify-content: space-around;
        padding: 0 24px;
        box-shadow: 0 8px 18px rgba(0, 0, 0, 0.35);
    }

    /* Ventanales: Cristal arquitectónico transparente con marco metálico (color: 0x88A0B0, opacity: 0.4) */
    .bm-3d-facade-window {
        width: 46px;
        height: 16px;
        background: rgba(136, 160, 176, 0.38);
        border: 1.5px solid #88A0B0;
        border-radius: 2px;
        box-shadow: inset 0 0 6px rgba(255, 255, 255, 0.4), 0 0 8px rgba(136, 160, 176, 0.25);
    }

    /* Puertas: Madera roble claro / nogal suave (#8D6E63 / #A1887F) con manija metálica */
    .bm-3d-facade-door {
        position: relative;
        width: 22px;
        height: 26px;
        margin-top: 8px;
        background: linear-gradient(180deg, #8D6E63 0%, #6D4C41 100%);
        border: 1.5px solid #4E342E;
        border-bottom: none;
        border-radius: 2px 2px 0 0;
    }
    .bm-3d-facade-door::after {
        content: '';
        position: absolute;
        right: 3px;
        top: 12px;
        width: 2px;
        height: 4px;
        background: #E0E0E0;
        border-radius: 1px;
    }

    /* Muro divisorio interior extruido en 3D en yeso blanco humo (#ECEFF1) con borde grafito (#374151) */
    .bm-3d-partition-wall {
        position: absolute;
        right: 0;
        top: 0;
        bottom: 0;
        width: 38px;
        background: linear-gradient(to left, #FFFFFF 0%, #ECEFF1 100%);
        border-right: 2.5px solid #374151;
        transform-origin: right;
        transform: rotateY(90deg) translateX(38px);
        pointer-events: none;
    }

    /* Piso interior de los 5 laboratorios: Piso técnico elevado / vinílico industrial antiestático (#B0B7BD / #9EA7AD) con juntas en #8A9399 */
    .bm-3d-room {
        position: relative;
        height: 100%;
        transform-style: preserve-3d;
        transition: background-color 0.22s ease, box-shadow 0.22s ease;
        cursor: pointer;
        border-right: 2px solid #8A9399;
        background-color: #B0B7BD;
        background-image:
            linear-gradient(to right, rgba(138, 147, 153, 0.45) 1px, transparent 1px),
            linear-gradient(to bottom, rgba(138, 147, 153, 0.45) 1px, transparent 1px);
        background-size: 26px 26px;
        box-shadow: inset 0 10px 20px rgba(0, 0, 0, 0.12), inset 4px 0 10px rgba(0, 0, 0, 0.08);
    }
    .bm-3d-room:hover {
        background-color: #BAC2C8;
    }
    .bm-3d-room.is-selected {
        background-color: #C5CDD3;
        box-shadow:
            inset 0 0 0 3px #374151,
            inset 0 12px 24px rgba(0, 0, 0, 0.18);
    }

    /* Rótulos superiores flotantes: Placa base en gris grafito sobrio (#1F2937) con borde fino plateado (#9CA3AF) y texto blanco nítido (#FFFFFF), sin fondos verdes */
    .bm-3d-billboard {
        position: absolute;
        top: 20px;
        left: 50%;
        transform: translateX(-50%) translateZ(56px) rotateX(-22deg);
        background: #1F2937;
        color: #FFFFFF;
        font-weight: 800;
        font-size: 11px;
        letter-spacing: 0.04em;
        padding: 4px 11px;
        border-radius: 6px;
        border: 1.5px solid #9CA3AF;
        box-shadow: 0 10px 20px -4px rgba(0, 0, 0, 0.6);
        white-space: nowrap;
        pointer-events: none;
        transition: transform 0.2s ease, background 0.2s ease, border-color 0.2s ease;
    }
    .bm-3d-room:hover .bm-3d-billboard,
    .bm-3d-room.is-selected .bm-3d-billboard {
        transform: translateX(-50%) translateZ(68px) rotateX(-22deg) scale(1.08);
        background: #111827;
        border-color: #E5E7EB;
        color: #FFFFFF;
        box-shadow: 0 14px 26px -4px rgba(0, 0, 0, 0.75);
    }

    /* Mobiliario: Mesas con tableros de melamina blanca / madera clara (#E5E7EB / #D1D5DB) y estructura/patas en metal gris oscuro / antracita (#374151) */
    .bm-3d-desk-unit {
        position: relative;
        width: 28px;
        height: 21px;
        border-radius: 3px;
        background: linear-gradient(180deg, #E5E7EB 0%, #D1D5DB 100%);
        border: 1px solid #374151;
        transform: translateZ(10px);
        transform-style: preserve-3d;
        box-shadow:
            0 3px 0 #374151,
            0 6px 10px rgba(0, 0, 0, 0.28);
        display: flex;
        align-items: center;
        justify-content: flex-start;
        padding-left: 2px;
        gap: 2px;
        transition: transform 0.16s ease, box-shadow 0.16s ease, border-color 0.16s ease;
    }
    .bm-3d-desk-unit:hover,
    .bm-3d-desk-unit.is-pc-active {
        transform: translateZ(24px) scale(1.22);
        z-index: 30;
        border-color: #1F2937;
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.45);
    }

    .bm-3d-workstation-main {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 1.5px;
    }

    /* Carcasa de monitores en negro mate (#1F2937) para que las pantallas encendidas resalten con alto contraste */
    .bm-3d-monitor {
        width: 17px;
        height: 11px;
        border-radius: 2px;
        border: 1.5px solid #1F2937;
        font-family: ui-monospace, SFMono-Regular, Menlo, monospace;
        font-size: 7px;
        font-weight: 800;
        display: flex;
        align-items: center;
        justify-content: center;
        line-height: 1;
        transform: translateZ(4px) rotateX(-14deg);
        transition: background 0.2s ease, box-shadow 0.2s ease;
    }

    /* ÚNICOS ELEMENTOS CON COLOR EN EL 3D: PANTALLAS DE COMPUTADORAS */
    /* Verde (#10B981 / #99DDC8): PC Disponible */
    .bm-3d-monitor.is-disponible,
    .bm-3d-monitor.is-libre {
        background: #10B981;
        color: #064E3B;
        box-shadow: 0 0 10px rgba(16, 185, 129, 0.95);
    }

    /* Amarillo / Ámbar (#F59E0B): PC Ocupada */
    .bm-3d-monitor.is-ocupado,
    .bm-3d-monitor.is-ocupada {
        background: #F59E0B;
        color: #451A03;
        box-shadow: 0 0 10px rgba(245, 158, 11, 0.95);
    }

    /* Rojo (#EF4444): PC No disponible / Avería */
    .bm-3d-monitor.is-mantenimiento {
        background: #EF4444;
        color: #FFFFFF;
        box-shadow: 0 0 10px rgba(239, 68, 68, 0.95);
    }

    .bm-3d-keyboard {
        width: 13px;
        height: 3px;
        border-radius: 1px;
        background: #374151;
        opacity: 0.9;
    }

    /* Gabinete / Torre de computadora en negro mate (#1F2937) */
    .bm-3d-tower {
        width: 4.5px;
        height: 13px;
        border-radius: 1px;
        background: #1F2937;
        border: 1px solid #374151;
        position: relative;
        transform: translateZ(3px);
    }
</style>

<section class="max-w-[1440px] mx-auto px-3 sm:px-6 lg:px-8 pt-3 pb-6">
    <div class="rounded-3xl border border-surface-container-highest overflow-hidden relative shadow-lg bg-surface-container-lowest">

        {{-- ── BARRA DE CONTROLES SUPERIOR (UBICADA DEBAJO DEL NAVBAR, SIN TRASLAPE) ── --}}
        <div class="relative z-20 px-4 sm:px-6 py-3.5 border-b border-surface-container-highest flex flex-wrap items-center justify-between gap-3 bg-surface-container-lowest">
            {{-- Selector de Nivel (Izquierda) --}}
            <div class="flex flex-wrap items-center gap-2">
                <button type="button"
                        id="btn-3d-pb"
                        onclick="window.MetalNet3D.setFloor('pb')"
                        class="h-9 px-4 rounded-xl text-xs font-extrabold transition cursor-pointer text-white border shadow-xs bg-primary border-primary">
                    Planta Baja (LAB 1 – LAB 5)
                </button>
                <button type="button"
                        id="btn-3d-pa"
                        onclick="window.MetalNet3D.setFloor('pa')"
                        class="h-9 px-4 rounded-xl text-xs font-bold transition cursor-pointer border bg-surface-container-low text-on-surface-variant border-surface-container-highest hover:bg-surface-container hover:text-on-surface">
                    Planta Alta (LAB 6 – LAB 10)
                </button>
                <button type="button"
                        id="btn-3d-both"
                        onclick="window.MetalNet3D.setFloor('both')"
                        class="h-9 px-4 rounded-xl text-xs font-bold transition cursor-pointer border bg-surface-container-low text-on-surface-variant border-surface-container-highest hover:bg-surface-container hover:text-on-surface">
                    Vista Doble (10 Labs)
                </button>
            </div>

            {{-- Leyenda Contextual de 3 Colores + Controles de Cámara 3D (Derecha) --}}
            <div class="flex flex-wrap items-center gap-3">
                <div class="mn-legend-capsule" aria-label="Simbología de disponibilidad 3D">
                    <span class="mn-legend-item">
                        <span class="mn-legend-dot mn-legend-dot--free"></span>
                        Disponible (<strong class="font-mono text-status-available" data-total-count="disponible">{{ $totales3d['disponible'] ?? 88 }}</strong>)
                    </span>
                    <span class="mn-legend-item">
                        <span class="mn-legend-dot mn-legend-dot--busy"></span>
                        Ocupado (<strong class="font-mono text-status-occupied" data-total-count="ocupado">{{ $totales3d['ocupado'] ?? 96 }}</strong>)
                    </span>
                    <span class="mn-legend-item">
                        <span class="mn-legend-dot mn-legend-dot--down"></span>
                        No disponible (<strong class="font-mono text-status-unavailable" data-total-count="mantenimiento">{{ $totales3d['mantenimiento'] ?? 16 }}</strong>)
                    </span>
                </div>

                <div class="flex items-center gap-1 p-1 rounded-xl border bg-surface-container-low border-surface-container-highest">
                    <button type="button" onclick="window.MetalNet3D.setPreset(54, -16, 0.95, 0)" title="Vista Isométrica 3D"
                            class="px-3 py-1 rounded-lg text-[11px] font-bold cursor-pointer transition bg-primary text-white border border-primary">
                        3D
                    </button>
                    <button type="button" onclick="window.MetalNet3D.setPreset(64, 0, 0.96, 0)" title="Vista Frontal"
                            class="px-3 py-1 rounded-lg text-[11px] font-bold cursor-pointer transition text-on-surface-variant hover:text-on-surface hover:bg-surface-container">
                        Frontal
                    </button>
                    <button type="button" onclick="window.MetalNet3D.setPreset(20, 0, 0.95, 0)" title="Vista en Planta"
                            class="px-3 py-1 rounded-lg text-[11px] font-bold cursor-pointer transition text-on-surface-variant hover:text-on-surface hover:bg-surface-container">
                        Planta
                    </button>
                    <button type="button" onclick="window.MetalNet3D.zoom(0.08)" title="Acercar cámara"
                            class="w-7 h-7 rounded-lg flex items-center justify-center font-bold cursor-pointer text-primary hover:bg-surface-container">+</button>
                    <button type="button" onclick="window.MetalNet3D.zoom(-0.08)" title="Alejar cámara"
                            class="w-7 h-7 rounded-lg flex items-center justify-center font-bold cursor-pointer text-primary hover:bg-surface-container">−</button>
                </div>
            </div>
        </div>

        {{-- ── LIENZO 3D INTERACTIVO ────────────────────────────────────────── --}}
        <div id="bm-3d-viewport" class="bm-3d-stage min-h-[550px] sm:min-h-[600px] flex flex-col items-center justify-center pt-14 pb-10 px-4 cursor-grab active:cursor-grabbing">
            <div class="bm-3d-ground-grid"></div>

            {{-- Píldora técnica de guía --}}
            <div class="absolute top-3.5 left-4 z-10 inline-flex items-center gap-2 px-3.5 py-1.5 rounded-xl border border-surface-container-highest text-[11px] font-semibold shadow-md pointer-events-none bg-surface-container-lowest/90 text-on-surface-variant">
                <svg class="w-4 h-4 shrink-0 text-primary" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/></svg>
                <span>Arrastra para orbitar · Haz clic en un <strong class="text-primary">LAB</strong> para enfocar o en una <strong class="text-status-occupied">PC (01-20)</strong> para inspeccionarla</span>
            </div>

            {{-- ESCENA 3D --}}
            <div id="bm-3d-scene-container" class="space-y-16">
                @foreach([
                    ['key' => 'pb', 'titulo' => 'PLANTA BAJA', 'labs' => $plantaBajaLabs, 'util' => 'C.E / S.E'],
                    ['key' => 'pa', 'titulo' => 'PLANTA ALTA', 'labs' => $plantaAltaLabs, 'util' => 'SITE IDF'],
                ] as $idxFloor => $piso)
                    <div id="bm-3d-floor-{{ $piso['key'] }}"
                         class="bm-3d-rig {{ $piso['key'] === 'pa' ? 'hidden' : '' }}"
                         style="transform: translate3d(0px, 0px, 0px) rotateX(54deg) rotateZ(-16deg) scale(0.95);">

                        <div class="bm-3d-terracotta"></div>
                        <div class="bm-3d-concrete-pad"></div>

                        <div class="bm-3d-building-shell flex">
                            <div class="bm-3d-wall-back"></div>
                            <div class="bm-3d-wall-left"></div>
                            <div class="bm-3d-wall-right"></div>

                            @foreach($piso['labs'] as $labIdx => $lab)
                                <div class="bm-3d-room flex-1 p-3 flex flex-col justify-between"
                                     data-3d-lab-id="{{ $lab['id'] }}"
                                     data-3d-lab-idx="{{ $labIdx }}"
                                     onclick="event.stopPropagation(); window.MetalNet3D.openLabCard('{{ $lab['id'] }}')">

                                     <div class="bm-3d-partition-wall"></div>

                                    <div class="bm-3d-billboard">
                                        {{ $lab['etiqueta_mapa'] }} · {{ $lab['codigo'] }}
                                    </div>

                                    {{-- Hilera Superior de Estaciones (PCs 1 a 10) --}}
                                    <div class="grid grid-cols-5 gap-2.5 pt-12">
                                        @foreach(array_slice($lab['pcs'] ?? [], 0, 10) as $pc)
                                            <div class="bm-3d-desk-unit cursor-pointer"
                                                 data-pc-id="{{ $pc['id'] }}"
                                                 title="{{ $pc['codigo'] }} ({{ $pc['estado'] }})"
                                                 onclick="event.stopPropagation(); window.MetalNet3D.inspectPc('{{ $pc['id'] }}', '{{ $lab['id'] }}')">
                                                <div class="bm-3d-workstation-main">
                                                    <span class="bm-3d-monitor is-{{ $pc['estado'] }}">{{ $pc['numero'] }}</span>
                                                    <span class="bm-3d-keyboard"></span>
                                                </div>
                                                <span class="bm-3d-tower"></span>
                                            </div>
                                        @endforeach
                                    </div>

                                    {{-- Pasillo interior entre mesas --}}
                                    <div class="h-5 my-1 flex items-center justify-center opacity-40">
                                        <span class="w-full border-t border-dashed border-[#c0c9c2]"></span>
                                    </div>

                                    {{-- Hilera Inferior de Estaciones (PCs 11 a 20) --}}
                                    <div class="grid grid-cols-5 gap-2.5 pb-2">
                                        @foreach(array_slice($lab['pcs'] ?? [], 10, 10) as $pc)
                                            <div class="bm-3d-desk-unit cursor-pointer"
                                                 data-pc-id="{{ $pc['id'] }}"
                                                 title="{{ $pc['codigo'] }} ({{ $pc['estado'] }})"
                                                 onclick="event.stopPropagation(); window.MetalNet3D.inspectPc('{{ $pc['id'] }}', '{{ $lab['id'] }}')">
                                                <div class="bm-3d-workstation-main">
                                                    <span class="bm-3d-monitor is-{{ $pc['estado'] }}">{{ $pc['numero'] }}</span>
                                                    <span class="bm-3d-keyboard"></span>
                                                </div>
                                                <span class="bm-3d-tower"></span>
                                            </div>
                                        @endforeach
                                    </div>

                                    {{-- Pie de Sala con Telemetría --}}
                                    <div class="pt-2 border-t border-[#8A9399]/40 flex items-center justify-between text-[10px] font-mono">
                                        @if(!empty($lab['en_sesion_docente']))
                                            <span class="font-bold text-status-occupied truncate">Sesión en Curso</span>
                                            <span class="font-bold text-status-occupied shrink-0">{{ $lab['conteo']['ocupado'] }} Ocup</span>
                                        @else
                                            <span class="font-bold text-status-available">{{ $lab['conteo']['disponible'] }} Libres</span>
                                            <span class="text-status-occupied font-bold">{{ $lab['conteo']['ocupado'] }} Ocup</span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach

                            {{-- ÁREA DE SERVICIO 1: ESCALERAS CENTRALES (Granito/basalto gris oscuro con barandal plateado) --}}
                            <div class="relative w-[110px] h-full bg-[#2D3748] p-2 flex flex-col justify-between border-l-2 border-[#4A5568]"
                                 style="transform-style: preserve-3d;">
                                <div class="bm-3d-billboard" style="background: #1F2937; border-color: #9CA3AF; font-size: 9px; color: #FFFFFF;">
                                    ESCALERAS
                                </div>
                                <div class="space-y-1.5 my-auto pt-6" style="transform: translateZ(10px);">
                                    @for($s = 0; $s < 6; $s++)
                                        <div class="h-2 rounded-xs bg-[#4A5568] border border-[#64748B] shadow-xs"></div>
                                    @endfor
                                </div>
                                <div class="text-[9px] font-mono font-bold text-center text-[#9CA3AF]">NIVEL {{ $piso['key'] === 'pb' ? '1' : '2' }}</div>
                            </div>

                            {{-- ÁREA DE SERVICIO 2: SANITARIOS Y SERVICIOS (Loseta cerámica gris claro #CBD5E1) --}}
                            <div class="relative w-[95px] h-full bg-[#CBD5E1] p-2 flex flex-col justify-between border-l-2 border-[#94A3B8]"
                                 style="transform-style: preserve-3d;">
                                <div class="bm-3d-billboard" style="background: #1F2937; border-color: #9CA3AF; font-size: 9px; color: #FFFFFF;">
                                    WC / ASEO
                                </div>
                                <div class="grid grid-cols-2 gap-2 my-auto pt-6" style="transform: translateZ(10px);">
                                    <div class="h-10 rounded bg-[#475569] border border-[#64748B] flex items-center justify-center text-[9px] font-bold text-white">H</div>
                                    <div class="h-10 rounded bg-[#475569] border border-[#64748B] flex items-center justify-center text-[9px] font-bold text-white">M</div>
                                </div>
                                <div class="text-[9px] font-mono text-center text-[#475569] font-bold">SERVICIOS</div>
                            </div>

                            {{-- ÁREA DE SERVICIO 3: SITE IDF / SERVIDORES (Piso técnico gris oscuro #4A5568 y racks negro mate #1F2937) --}}
                            <div class="relative w-[170px] h-full bg-[#4A5568] p-3 flex flex-col items-center justify-center border-l-2 border-[#334155]"
                                 style="transform-style: preserve-3d;">
                                <div class="bm-3d-billboard" style="background: #1F2937; border-color: #9CA3AF; font-size: 9px; color: #FFFFFF;">
                                    {{ $piso['util'] }}
                                </div>
                                <div class="grid grid-cols-2 gap-4 mb-5 mt-4" style="transform: translateZ(12px);">
                                    <div class="w-11 h-7 rounded bg-[#111827] border border-[#374151] shadow-md flex items-center justify-center">
                                        <span class="w-2 h-2 rounded-full bg-[#9CA3AF] shadow-[0_0_6px_#9CA3AF]"></span>
                                    </div>
                                    <div class="w-11 h-7 rounded bg-[#111827] border border-[#374151] shadow-md flex items-center justify-center">
                                        <span class="w-2 h-2 rounded-full bg-[#9CA3AF] shadow-[0_0_6px_#9CA3AF]"></span>
                                    </div>
                                </div>
                                <div class="w-20 h-11 rounded-md bg-[#111827] border-2 border-[#374151] shadow-lg flex items-center justify-center"
                                     style="transform: translateZ(16px);">
                                    <span class="w-12 h-3 rounded-xs bg-[#374151] border border-[#4B5563]"></span>
                                </div>
                            </div>

                            <div class="bm-3d-wall-front">
                                <div class="bm-3d-facade-window"></div>
                                <div class="bm-3d-facade-door"></div>
                                <div class="bm-3d-facade-window"></div>
                                <div class="bm-3d-facade-window"></div>
                                <div class="bm-3d-facade-door"></div>
                                <div class="bm-3d-facade-window"></div>
                                <div class="bm-3d-facade-door"></div>
                                <div class="bm-3d-facade-window"></div>
                                <div class="bm-3d-facade-window"></div>
                            </div>
                        </div>

                        <div class="absolute -right-6 bottom-6 px-5 py-2 rounded-xl bg-[#1F2937] text-white font-extrabold text-xs tracking-wider uppercase shadow-xl border-2 border-[#9CA3AF]"
                             style="transform: translateZ(48px) rotateX(-20deg);">
                            {{ $piso['titulo'] }}
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- ── TARJETA LATERAL DE RESUMEN EN VIVO (ALTO CONTRASTE STITCH) ── --}}
            <aside id="bm-3d-floating-card"
                   class="hidden absolute top-4 right-4 bottom-4 w-[92%] sm:w-[400px] z-30 rounded-2xl border border-surface-container-highest p-5 flex flex-col justify-between overflow-y-auto transition-all bg-surface-container-lowest/98 backdrop-blur-md shadow-2xl text-on-surface"
                   onclick="event.stopPropagation();">

                <div>
                    <div class="flex items-start justify-between gap-2 pb-3 border-b border-surface-container-highest">
                        <div>
                            <div class="flex flex-wrap items-center gap-1.5">
                                <span id="fc-lab-code" class="px-2.5 py-0.5 rounded-md border font-mono text-xs font-extrabold bg-[#e8f5ec] text-[#16512c] border-[#b3f1be]">
                                    LAB 1 · L-01
                                </span>
                                <span id="fc-lab-floor" class="px-2 py-0.5 rounded-md text-[11px] font-bold border bg-surface-container-low text-on-surface-variant border-surface-container-highest">
                                    Planta Baja
                                </span>
                                <span id="fc-lab-canon" class="px-2.5 py-0.5 rounded-md text-[10px] font-extrabold border bg-surface-container text-primary border-surface-container-highest">
                                    Cañón Activo
                                </span>
                            </div>
                            <h3 id="fc-lab-name" class="text-base font-extrabold mt-1.5 text-on-surface">
                                Redes Cisco y Telecomunicaciones
                            </h3>
                        </div>

                        <button type="button"
                                onclick="window.MetalNet3D.closeLabCard()"
                                class="w-8 h-8 rounded-lg border border-surface-container-highest flex items-center justify-center cursor-pointer shrink-0 bg-surface-container-low text-on-surface-variant hover:text-on-surface hover:bg-surface-container"
                                title="Restablecer cámara y cerrar">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" x2="6" y1="6" y2="18"/><line x1="6" x2="18" y1="6" y2="18"/></svg>
                        </button>
                    </div>

                    {{-- Sesión Actual --}}
                    <div class="my-3 p-3 rounded-xl border border-surface-container-highest space-y-1 text-xs bg-surface-container-low">
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] font-extrabold uppercase tracking-wider text-primary">Sesión Actual</span>
                            <span id="fc-lab-grupo" class="font-mono text-[11px] font-bold text-[#92400e]">MN-8A</span>
                        </div>
                        <p id="fc-lab-materia" class="font-bold truncate text-on-surface">Conmutación y Enrutamiento</p>
                        <p id="fc-lab-docente" class="text-[11px] truncate text-on-surface-variant">Mtro. Fernando Morales</p>
                    </div>

                    {{-- Métricas de Estaciones (3 Colores) --}}
                    <div class="grid grid-cols-4 gap-2 my-3 text-center">
                        <div class="p-2 rounded-xl border bg-surface-container-lowest border-surface-container-highest shadow-xs">
                            <span id="fc-num-cap" class="text-xl font-black font-mono block leading-none text-on-surface">20</span>
                            <span class="text-[9px] font-bold uppercase mt-1 block text-outline">Total</span>
                        </div>
                        <div class="p-2 rounded-xl border bg-[#e8f5ec] border-[#b3f1be] shadow-xs">
                            <span id="fc-num-free" class="text-xl font-black font-mono block leading-none text-[#16512c]">5</span>
                            <span class="text-[9px] font-bold uppercase mt-1 block text-[#16512c]">Disp.</span>
                        </div>
                        <div class="p-2 rounded-xl border bg-[#fef3c7] border-[#fde68a] shadow-xs">
                            <span id="fc-num-busy" class="text-xl font-black font-mono block leading-none text-[#92400e]">13</span>
                            <span class="text-[9px] font-bold uppercase mt-1 block text-[#92400e]">Ocup.</span>
                        </div>
                        <div class="p-2 rounded-xl border bg-[#ffdad6] border-[#fca5a5] shadow-xs">
                            <span id="fc-num-down" class="text-xl font-black font-mono block leading-none text-[#991b1b]">2</span>
                            <span class="text-[9px] font-bold uppercase mt-1 block text-[#991b1b]">No disp.</span>
                        </div>
                    </div>

                    {{-- Horas Libres hoy --}}
                    <div class="p-3 rounded-xl border border-surface-container-highest mb-3 bg-surface-container-low">
                        <div class="flex items-center justify-between text-xs font-extrabold mb-2">
                            <span class="text-[#16512c]"><strong id="fc-hrs-free" class="font-mono">4h</strong> Libres hoy</span>
                            <span class="text-[#92400e]"><strong id="fc-hrs-busy" class="font-mono">8h</strong> Ocupadas</span>
                        </div>
                        <div id="fc-hours-strip" class="grid grid-cols-6 gap-1 text-center"></div>
                    </div>

                    {{-- Cuadrícula PC-01 a PC-20 --}}
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <span class="text-[10px] font-extrabold uppercase tracking-wider text-outline">Inspeccionar Estación (01-20)</span>
                        </div>
                        <div id="fc-pc-grid" class="grid grid-cols-5 gap-1.5 p-2.5 rounded-xl border bg-surface-container-lowest border-surface-container-highest shadow-xs"></div>
                    </div>
                </div>

                {{-- Botones de Acción --}}
                <div class="mt-4 pt-3 border-t border-surface-container-highest grid grid-cols-2 gap-2">
                    <button type="button"
                            id="fc-btn-reservar"
                            class="h-10 rounded-xl text-white text-xs font-black flex items-center justify-center gap-1.5 cursor-pointer transition hover:bg-primary-container bg-primary shadow-xs">
                        <svg class="w-4 h-4 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                        Inspeccionar PC
                    </button>
                    <a href="{{ route('public.horarios') }}"
                       id="fc-btn-horarios"
                       class="h-10 rounded-xl border text-xs font-bold flex items-center justify-center gap-1.5 cursor-pointer transition hover:bg-surface-container bg-surface-container-low text-on-surface border-surface-container-highest">
                        <svg class="w-4 h-4 text-primary" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                        Ver Horarios
                    </a>
                </div>
            </aside>
        </div>

        {{-- ── BARRA INFERIOR DE ACCESO DIRECTO A LOS 10 LABS ─────────────── --}}
        <div class="px-4 py-3 border-t border-surface-container-highest flex items-center gap-2 overflow-x-auto bg-surface-container-lowest">
            <span class="text-[10px] font-extrabold uppercase tracking-wider mr-1 shrink-0 text-outline">Enfoque rápido:</span>
            @foreach($labs3d as $pillLab)
                <button type="button"
                        data-bottom-lab-pill="{{ $pillLab['id'] }}"
                        onclick="window.MetalNet3D.openLabCard('{{ $pillLab['id'] }}')"
                        class="px-3 py-1.5 rounded-xl text-xs font-bold transition cursor-pointer shrink-0 inline-flex items-center gap-1.5 border bg-surface-container-low text-on-surface-variant border-surface-container-highest hover:bg-surface-container hover:text-on-surface hover:border-primary">
                    <span class="font-mono font-extrabold text-primary">{{ $pillLab['etiqueta_mapa'] }}</span>
                    <span class="text-[10px] font-mono text-outline">{{ $pillLab['conteo']['disponible'] }}L</span>
                    @if(!$pillLab['canon'])
                        <span class="w-2 h-2 rounded-full bg-status-unavailable" title="Sin Cañón"></span>
                    @endif
                </button>
            @endforeach
        </div>
    </div>
</section>

{{-- Integración WebGL Three.js con materiales realistas PBR, luces suaves y monitores emisivos --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
<script>
    (function () {
        const viewport = document.getElementById('bm-3d-viewport');
        const pbEl = document.getElementById('bm-3d-floor-pb');
        const paEl = document.getElementById('bm-3d-floor-pa');
        const card = document.getElementById('bm-3d-floating-card');

        let cur = { rotX: 54, rotZ: -16, scale: 0.95, panX: 0 };
        let tgt = { rotX: 54, rotZ: -16, scale: 0.95, panX: 0 };
        let animId = null;

        let dragging = false;
        let startX = 0;
        let startY = 0;

        function renderRig() {
            const t = `translate3d(${cur.panX.toFixed(1)}px, 0px, 0px) rotateX(${cur.rotX.toFixed(2)}deg) rotateZ(${cur.rotZ.toFixed(2)}deg) scale(${cur.scale.toFixed(3)})`;
            if (pbEl) pbEl.style.transform = t;
            if (paEl) paEl.style.transform = t;
        }

        function animateCamera() {
            const dx = tgt.rotX - cur.rotX;
            const dz = tgt.rotZ - cur.rotZ;
            const ds = tgt.scale - cur.scale;
            const dp = tgt.panX - cur.panX;

            if (Math.abs(dx) < 0.05 && Math.abs(dz) < 0.05 && Math.abs(ds) < 0.002 && Math.abs(dp) < 0.4) {
                cur.rotX = tgt.rotX;
                cur.rotZ = tgt.rotZ;
                cur.scale = tgt.scale;
                cur.panX = tgt.panX;
                renderRig();
                animId = null;
                return;
            }

            cur.rotX += dx * 0.14;
            cur.rotZ += dz * 0.14;
            cur.scale += ds * 0.14;
            cur.panX += dp * 0.14;
            renderRig();
            animId = requestAnimationFrame(animateCamera);
        }

        function startLerp() {
            if (!animId) animId = requestAnimationFrame(animateCamera);
        }

        if (viewport) {
            viewport.addEventListener('mousedown', (e) => {
                if (e.target.closest('#bm-3d-floating-card') || e.target.closest('button')) return;
                dragging = true;
                startX = e.clientX;
                startY = e.clientY;
            });
            window.addEventListener('mousemove', (e) => {
                if (!dragging) return;
                const dx = e.clientX - startX;
                const dy = e.clientY - startY;
                startX = e.clientX;
                startY = e.clientY;
                tgt.rotZ = Math.max(-55, Math.min(55, tgt.rotZ + dx * 0.32));
                tgt.rotX = Math.max(20, Math.min(72, tgt.rotX - dy * 0.25));
                startLerp();
            });
            window.addEventListener('mouseup', () => { dragging = false; });

            viewport.addEventListener('wheel', (e) => {
                if (e.target.closest('#bm-3d-floating-card')) return;
                e.preventDefault();
                tgt.scale = Math.max(0.65, Math.min(1.3, tgt.scale + (e.deltaY < 0 ? 0.06 : -0.06)));
                startLerp();
            }, { passive: false });
        }

        function setFloor(mode) {
            if (!pbEl || !paEl) return;
            pbEl.classList.toggle('hidden', mode === 'pa');
            paEl.classList.toggle('hidden', mode === 'pb');

            const activeStyle = 'background: #2F664F; color: #ffffff; border-color: #2F664F;';
            const idleStyle = 'background: #f5f4ec; color: #404944; border-color: #e4e3db;';

            const pbBtn = document.getElementById('btn-3d-pb');
            const paBtn = document.getElementById('btn-3d-pa');
            const bothBtn = document.getElementById('btn-3d-both');
            if (pbBtn) pbBtn.style.cssText = mode === 'pb' ? activeStyle : idleStyle;
            if (paBtn) paBtn.style.cssText = mode === 'pa' ? activeStyle : idleStyle;
            if (bothBtn) bothBtn.style.cssText = mode === 'both' ? activeStyle : idleStyle;
        }

        function openLabCard(labId) {
            const labsData = (window.BM_DATA && window.BM_DATA.labs) || [];
            const lab = labsData.find(l => String(l.id) === String(labId) || String(l.codigo) === String(labId));
            if (!lab || !card) return;

            if (lab.planta === 'Planta Alta' && paEl && paEl.classList.contains('hidden')) {
                setFloor('pa');
            } else if (lab.planta === 'Planta Baja' && pbEl && pbEl.classList.contains('hidden')) {
                setFloor('pb');
            }

            let roomIdx = 0;
            document.querySelectorAll('.bm-3d-room').forEach(r => {
                const match = String(r.getAttribute('data-3d-lab-id')) === String(lab.id);
                r.classList.toggle('is-selected', match);
                if (match) roomIdx = parseInt(r.getAttribute('data-3d-lab-idx') || '0', 10);
            });

            const offsets = [160, 70, -20, -110, -195];
            tgt.panX = offsets[roomIdx] !== undefined ? offsets[roomIdx] : -40;
            tgt.rotX = 50;
            tgt.rotZ = -8;
            tgt.scale = 1.04;
            startLerp();

            document.getElementById('fc-lab-code').textContent = `${lab.etiqueta} · ${lab.codigo}`;
            document.getElementById('fc-lab-floor').textContent = lab.planta;
            document.getElementById('fc-lab-name').textContent = lab.nombre || lab.corto;
            document.getElementById('fc-lab-materia').textContent = lab.asignatura || 'Práctica Abierta';
            document.getElementById('fc-lab-docente').textContent = lab.docente || 'Coordinación de Sistemas';
            document.getElementById('fc-lab-grupo').textContent = lab.grupo || 'Abierto';

            const canonEl = document.getElementById('fc-lab-canon');
            if (lab.canon) {
                canonEl.style.cssText = 'background: #e8f5ec; color: #16512c; border-color: #b3f1be;';
                canonEl.textContent = lab.canon_estado || 'Cañón Activo';
            } else {
                canonEl.style.cssText = 'background: #ffdad6; color: #991b1b; border-color: #fca5a5;';
                canonEl.textContent = 'SIN CAÑÓN';
            }

            document.getElementById('fc-num-cap').textContent = lab.pcs.length;
            document.getElementById('fc-num-free').textContent = lab.conteo.disponible;
            document.getElementById('fc-num-busy').textContent = lab.conteo.ocupado;
            document.getElementById('fc-num-down').textContent = lab.conteo.mantenimiento;

            document.getElementById('fc-hrs-free').textContent = (lab.horas_libres || 4) + 'h';
            document.getElementById('fc-hrs-busy').textContent = (lab.horas_ocupadas || 8) + 'h';

            const strip = document.getElementById('fc-hours-strip');
            strip.innerHTML = '';
            (lab.horarios || []).forEach(slot => {
                const free = slot.estado === 'libre';
                const div = document.createElement('div');
                div.className = 'p-1.5 rounded-lg border text-[10px] font-mono font-bold';
                div.style.cssText = free
                    ? 'background: #e8f5ec; border-color: #b3f1be; color: #16512c;'
                    : 'background: #fef3c7; border-color: #fde68a; color: #92400e;';
                div.innerHTML = `<span class="block">${slot.hora}</span><span class="text-[8px] uppercase">${free ? 'Disp' : 'Ocup'}</span>`;
                strip.appendChild(div);
            });

            const grid = document.getElementById('fc-pc-grid');
            grid.innerHTML = '';
            lab.pcs.forEach(pc => {
                const btn = document.createElement('button');
                btn.type = 'button';
                const isFree = pc.estado === 'disponible' || pc.estado === 'libre';
                const isBusy = pc.estado === 'ocupado' || pc.estado === 'ocupada';
                btn.className = 'h-8 rounded-lg border font-mono text-[11px] font-extrabold transition cursor-pointer';
                btn.style.cssText = isFree
                    ? 'background: #e8f5ec; color: #16512c; border-color: #b3f1be;'
                    : (isBusy
                        ? 'background: #fef3c7; color: #92400e; border-color: #fde68a;'
                        : 'background: #ffdad6; color: #991b1b; border-color: #fca5a5;');
                btn.textContent = pc.numero;
                btn.title = `${pc.codigo} (${pc.estado})`;
                btn.onclick = () => inspectPc(pc.id, lab.id);
                grid.appendChild(btn);
            });

            const firstFree = lab.pcs.find(p => p.estado === 'disponible' || p.estado === 'libre');
            const btnRes = document.getElementById('fc-btn-reservar');
            if (btnRes) {
                if (lab.en_sesion_docente || !firstFree) {
                    btnRes.className = 'h-10 rounded-xl border text-xs font-bold flex items-center justify-center gap-1.5 cursor-pointer bg-surface-container text-on-surface-variant border-surface-container-highest shadow-xs';
                    btnRes.innerHTML = `
                        <svg class="w-4 h-4 text-status-occupied shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                        <span class="truncate">En Clase (${lab.grupo || 'Docente'})</span>
                    `;
                    btnRes.onclick = () => {
                        const firstPc = lab.pcs[0];
                        if (firstPc) inspectPc(firstPc.id, lab.id);
                    };
                } else {
                    btnRes.className = 'h-10 rounded-xl text-white text-xs font-black flex items-center justify-center gap-1.5 cursor-pointer transition hover:bg-primary-container bg-primary shadow-xs';
                    btnRes.innerHTML = `
                        <svg class="w-4 h-4 text-white shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                        <span>Inspeccionar PC</span>
                    `;
                    btnRes.onclick = () => {
                        inspectPc(firstFree.id, lab.id);
                    };
                }
            }
            const btnHor = document.getElementById('fc-btn-horarios');
            if (btnHor) btnHor.href = `{{ route('public.horarios') }}?sala=${encodeURIComponent(lab.codigo)}`;

            card.classList.remove('hidden');
        }

        function inspectPc(pcId, labId) {
            document.querySelectorAll('.bm-3d-desk-unit.is-pc-active').forEach(el => el.classList.remove('is-pc-active'));
            document.querySelectorAll(`.bm-3d-desk-unit[data-pc-id="${pcId}"]`).forEach(el => el.classList.add('is-pc-active'));
            if (window.MetalNetPC && window.MetalNetPC.abrirFicha) {
                window.MetalNetPC.abrirFicha(pcId);
            } else if (window.BM && window.BM.openPcById) {
                window.BM.openPcById(pcId);
            }
        }

        function closeLabCard() {
            if (card) card.classList.add('hidden');
            document.querySelectorAll('.bm-3d-room').forEach(r => r.classList.remove('is-selected'));
            document.querySelectorAll('.bm-3d-desk-unit.is-pc-active').forEach(el => el.classList.remove('is-pc-active'));
            tgt.panX = 0;
            tgt.rotX = 54;
            tgt.rotZ = -16;
            tgt.scale = 0.95;
            startLerp();
        }

        // Configuración y Exposición del Motor WebGL Three.js con materiales de edificio real
        let threeScene = null;
        let threeRenderer = null;
        let threeMaterials = {};

        function initThreeEngine() {
            if (typeof THREE === 'undefined') return;
            try {
                // Materiales PBR de arquitectura real (cero tinte verde arquitectónico)
                threeMaterials = {
                    floorLab: new THREE.MeshStandardMaterial({
                        color: 0xB0B7BD,
                        roughness: 0.5,
                        metalness: 0.05
                    }),
                    floorHallway: new THREE.MeshStandardMaterial({
                        color: 0xD5D8DC,
                        roughness: 0.35,
                        metalness: 0.1
                    }),
                    wallPlaster: new THREE.MeshStandardMaterial({
                        color: 0xECEFF1,
                        roughness: 0.65,
                        metalness: 0.0
                    }),
                    wallTrim: new THREE.MeshStandardMaterial({
                        color: 0x374151,
                        roughness: 0.4,
                        metalness: 0.3
                    }),
                    door: new THREE.MeshStandardMaterial({
                        color: 0x8D6E63,
                        roughness: 0.55,
                        metalness: 0.05
                    }),
                    windowGlass: new THREE.MeshStandardMaterial({
                        color: 0x88A0B0,
                        transparent: true,
                        opacity: 0.4,
                        roughness: 0.1,
                        metalness: 0.8
                    }),
                    deskSurface: new THREE.MeshStandardMaterial({
                        color: 0xE5E7EB,
                        roughness: 0.4,
                        metalness: 0.05
                    }),
                    deskLegs: new THREE.MeshStandardMaterial({
                        color: 0x374151,
                        roughness: 0.5,
                        metalness: 0.4
                    }),
                    chassis: new THREE.MeshStandardMaterial({
                        color: 0x1F2937,
                        roughness: 0.5,
                        metalness: 0.2
                    }),
                    stairs: new THREE.MeshStandardMaterial({
                        color: 0x2D3748,
                        roughness: 0.6,
                        metalness: 0.1
                    }),
                    stairsRailing: new THREE.MeshStandardMaterial({
                        color: 0x9CA3AF,
                        roughness: 0.25,
                        metalness: 0.85
                    }),
                    restroom: new THREE.MeshStandardMaterial({
                        color: 0xCBD5E1,
                        roughness: 0.4,
                        metalness: 0.05
                    }),
                    serverFloor: new THREE.MeshStandardMaterial({
                        color: 0x4A5568,
                        roughness: 0.55,
                        metalness: 0.1
                    }),
                    serverRacks: new THREE.MeshStandardMaterial({
                        color: 0x1F2937,
                        roughness: 0.4,
                        metalness: 0.4
                    }),
                    signBoard: new THREE.MeshStandardMaterial({
                        color: 0x1F2937,
                        roughness: 0.3,
                        metalness: 0.2
                    }),

                    // ÚNICOS elementos con color permitido en toda la maqueta 3D:
                    monitorFree: new THREE.MeshStandardMaterial({
                        color: 0x10B981,
                        emissive: 0x10B981,
                        emissiveIntensity: 0.95,
                        roughness: 0.2
                    }),
                    monitorBusy: new THREE.MeshStandardMaterial({
                        color: 0xF59E0B,
                        emissive: 0xF59E0B,
                        emissiveIntensity: 0.95,
                        roughness: 0.2
                    }),
                    monitorDown: new THREE.MeshStandardMaterial({
                        color: 0xEF4444,
                        emissive: 0xEF4444,
                        emissiveIntensity: 0.95,
                        roughness: 0.2
                    }),

                    // Mapeos de compatibilidad con referencias anteriores
                    floor: new THREE.MeshStandardMaterial({ color: 0xB0B7BD, roughness: 0.5, metalness: 0.05 }),
                    wall: new THREE.MeshStandardMaterial({ color: 0xECEFF1, roughness: 0.65, metalness: 0.0 }),
                    desk: new THREE.MeshStandardMaterial({ color: 0xE5E7EB, roughness: 0.4, metalness: 0.05 }),
                    servers: new THREE.MeshStandardMaterial({ color: 0x1F2937, roughness: 0.4, metalness: 0.4 })
                };

                threeScene = new THREE.Scene();
                threeScene.background = new THREE.Color(0x111827);

                // Cuadrícula arquitectónica gris neutro en suelo exterior flotante
                const gridHelper = new THREE.GridHelper(120, 40, 0x4B5563, 0x374151);
                gridHelper.position.y = -2;
                threeScene.add(gridHelper);

                // Iluminación arquitectónica equilibrada sin tinte verde
                // Luz ambiental blanco neutro puro (0.85)
                const ambientLight = new THREE.AmbientLight(0xffffff, 0.85);
                threeScene.add(ambientLight);

                // Luz cenital frontal con sombras volumétricas
                const directionalLight = new THREE.DirectionalLight(0xfff8f0, 1.2);
                directionalLight.position.set(35, 65, 45);
                directionalLight.castShadow = true;
                directionalLight.shadow.mapSize.width = 1024;
                directionalLight.shadow.mapSize.height = 1024;
                directionalLight.shadow.camera.near = 0.5;
                directionalLight.shadow.camera.far = 150;
                threeScene.add(directionalLight);

                // Luz de relleno posterior para contornos definidos
                const fillLight = new THREE.DirectionalLight(0xe2e6ea, 0.5);
                fillLight.position.set(-35, 30, -35);
                threeScene.add(fillLight);

                window.MetalNet3DMaterials = threeMaterials;
                window.MetalNet3DScene = threeScene;
            } catch (err) {
                // Modo fallback CSS 3D siempre disponible
            }
        }

        initThreeEngine();

        window.MetalNet3D = {
            setFloor,
            openLabCard,
            closeLabCard,
            inspectPc,
            materials: threeMaterials,
            setPreset(rx, rz, sc, px = 0) {
                tgt.rotX = rx;
                tgt.rotZ = rz;
                tgt.scale = sc;
                tgt.panX = px;
                startLerp();
            },
            zoom(delta) {
                tgt.scale = Math.max(0.65, Math.min(1.3, tgt.scale + delta));
                startLerp();
            }
        };
    })();
</script>
