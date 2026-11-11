{{--
    Capa interactiva global · MetalNet · Laboratorios de Ingeniería en Sistemas.
    - Tarjeta flotante (hover) y ficha técnica interactiva (modal) de cada computadora (PC-01 a PC-20).
    - Apartar computadora / Reportar fallo en equipo / Cambiar estado o Reparar avería (Admin/Encargado).
    - Modal interactivo de Solicitud de Software / Actualización / Hardware al Administrador.
    - Modal "Mapa de laboratorios" con detalles técnicos completos de cada sala.
--}}
@php
    $bmLabsList = $bmLabs ?? [];
    $userRoleSlug = Auth::check() ? Auth::user()->primaryRoleSlug() : null;
    $isStaff = in_array($userRoleSlug, ['admin', 'encargado'], true);
@endphp
<style>
    @keyframes bmPulse { 0%,100% { box-shadow: 0 0 0 0 rgba(22,163,74,.35); } 70% { box-shadow: 0 0 0 5px rgba(22,163,74,0); } }
    @keyframes bmPop { from { opacity: 0; transform: translateY(6px) scale(.98); } to { opacity: 1; transform: none; } }
    @keyframes bmReveal { from { opacity: 0; transform: translateY(-4px); } to { opacity: 1; transform: none; } }
    @keyframes bmHighlight { 0%,100% { box-shadow: 0 0 0 0 rgba(47,102,79,0); } 50% { box-shadow: 0 0 0 5px rgba(47,102,79,.35); } }

    .bm-reveal { animation: bmReveal .22s ease both; }

    /* ---------- Cuadrícula de estaciones de cómputo (PC-01 a PC-20) ---------- */
    .pc-grid--full { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: .75rem; }
    @media (min-width: 640px) { .pc-grid--full { grid-template-columns: repeat(4, minmax(0, 1fr)); } }
    @media (min-width: 768px) { .pc-grid--full { grid-template-columns: repeat(5, minmax(0, 1fr)); gap: .85rem; } }

    .pc-grid--mini { display: grid; grid-template-columns: repeat(5, minmax(0, 1fr)); gap: .45rem; }
    @media (min-width: 520px) { .pc-grid--mini { grid-template-columns: repeat(10, minmax(0, 1fr)); } }

    .pc-tile {
        font-family: inherit;
        cursor: pointer;
        position: relative;
        transition: transform .16s ease, box-shadow .16s ease, background-color .16s ease, border-color .16s ease, opacity .2s ease;
    }
    .pc-tile:focus-visible { outline: 2px solid #2f664f; outline-offset: 2px; }

    /* Tarjeta completa en modal de sala */
    .pc-tile--full {
        width: 100%;
        padding: .65rem .6rem;
        border-radius: .75rem;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: .35rem;
        background: #ffffff;
        border: 1.5px solid #e4e3db;
        color: #1b1c18;
        text-align: left;
    }
    .pc-tile--full:hover, .pc-tile--full.is-active {
        transform: translateY(-2px);
        box-shadow: 0 8px 18px -6px rgba(27, 28, 24, .12);
    }
    .pc-tile__top { width: 100%; display: flex; align-items: center; justify-content: space-between; pointer-events: none; }
    .pc-tile__code { font: 800 11px/1 'JetBrains Mono', ui-monospace, monospace; letter-spacing: -.01em; color: #1b1c18; }
    .pc-tile__led { width: 9px; height: 9px; border-radius: 999px; background: #2f664f; }
    .pc-tile__status {
        width: 100%;
        padding-top: .3rem;
        border-top: 1px solid #e4e3db;
        font-size: 10px;
        font-weight: 700;
        text-align: center;
        color: #404944;
        pointer-events: none;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    /* 3 ÚNICOS COLORES DE ESTADO EN TARJETA COMPLETA: VERDE / AMARILLO / ROJO */
    .pc-tile--full[data-estado="disponible"],
    .pc-tile--full[data-estado="libre"] {
        background: #e8f5ec;
        border-color: #b3f1be;
    }
    .pc-tile--full[data-estado="disponible"]:hover,
    .pc-tile--full[data-estado="libre"]:hover {
        background: #dcfce7;
        border-color: #2f664f;
    }
    .pc-tile[data-estado="disponible"] .pc-tile__led,
    .pc-tile[data-estado="libre"] .pc-tile__led { background: #2f664f; animation: bmPulse 2s infinite; }
    .pc-tile[data-estado="disponible"] .pc-tile__status,
    .pc-tile[data-estado="libre"] .pc-tile__status { color: #16512c; }

    .pc-tile--full[data-estado="ocupado"],
    .pc-tile--full[data-estado="reservado"] {
        background: #fef3c7;
        border-color: #fde68a;
    }
    .pc-tile--full[data-estado="ocupado"]:hover,
    .pc-tile--full[data-estado="reservado"]:hover {
        background: #fef08a;
        border-color: #f59e0b;
    }
    .pc-tile[data-estado="ocupado"] .pc-tile__led,
    .pc-tile[data-estado="reservado"] .pc-tile__led { background: #f59e0b; }
    .pc-tile[data-estado="ocupado"] .pc-tile__status,
    .pc-tile[data-estado="reservado"] .pc-tile__status { color: #92400e; }

    .pc-tile--full[data-estado="mantenimiento"] {
        background: #ffdad6;
        border-color: #fca5a5;
    }
    .pc-tile--full[data-estado="mantenimiento"]:hover {
        background: #fee2e2;
        border-color: #ba1a1a;
    }
    .pc-tile[data-estado="mantenimiento"] .pc-tile__led { background: #ba1a1a; }
    .pc-tile[data-estado="mantenimiento"] .pc-tile__status { color: #991b1b; }

    /* Estados en tarjeta mini (usada en tarjetas de /salas y selectores) */
    .pc-tile--mini {
        height: 3.35rem;
        padding: .3rem .25rem;
        border-radius: .6rem;
        border: 1.5px solid #e4e3db;
        background: #ffffff;
        color: #1b1c18;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: .2rem;
    }
    .pc-tile__num {
        font: 800 10.5px/1 'JetBrains Mono', ui-monospace, monospace;
        letter-spacing: -0.02em;
        pointer-events: none;
    }
    .pc-tile--mini:hover {
        transform: translateY(-2px);
        z-index: 2;
        box-shadow: 0 6px 14px -4px rgba(27, 28, 24, .12);
    }
    /* VERDE: Disponible (Stitch Jungle Green) */
    .pc-tile--mini[data-estado="disponible"],
    .pc-tile--mini[data-estado="libre"] {
        background: #e8f5ec;
        color: #16512c;
        border-color: #b3f1be;
    }
    .pc-tile--mini[data-estado="disponible"]:hover,
    .pc-tile--mini[data-estado="libre"]:hover {
        background: #dcfce7;
        border-color: #2f664f;
        box-shadow: 0 0 10px rgba(47, 102, 79, 0.25);
    }
    /* AMARILLO: Ocupado */
    .pc-tile--mini[data-estado="ocupado"],
    .pc-tile--mini[data-estado="reservado"] {
        background: #fef3c7;
        color: #92400e;
        border-color: #fde68a;
    }
    .pc-tile--mini[data-estado="ocupado"]:hover,
    .pc-tile--mini[data-estado="reservado"]:hover {
        background: #fef08a;
        border-color: #f59e0b;
        box-shadow: 0 0 10px rgba(245, 158, 11, 0.25);
    }
    /* ROJO: No disponible */
    .pc-tile--mini[data-estado="mantenimiento"] {
        background: #ffdad6;
        color: #991b1b;
        border-color: #fca5a5;
    }
    .pc-tile--mini[data-estado="mantenimiento"]:hover {
        background: #fee2e2;
        border-color: #ba1a1a;
        box-shadow: 0 0 10px rgba(186, 26, 26, 0.25);
    }

    .pc-tile.is-dimmed { opacity: .25; transform: scale(.96); pointer-events: none; }
    .pc-tile.is-highlight { animation: bmHighlight 1s ease 3; border-color: #2f664f; }
    .pc-tile.is-picked { outline: 2.5px solid #2f664f; outline-offset: 2px; background: #e8f5ec !important; }

    /* ---------- Controles reutilizables (Paleta Oficial Stitch) ---------- */
    .bm-tab {
        display: inline-flex; align-items: center; gap: .5rem; padding: .5rem .9rem;
        border-radius: .65rem; font-weight: 700; font-size: .78rem;
        background: #f5f4ec; color: #404944; border: 1px solid #e4e3db;
        cursor: pointer; transition: all .16s; white-space: nowrap;
    }
    .bm-tab:hover { background: #efeee7; color: #1b1c18; border-color: #c0c9c2; }
    .bm-tab.is-active { background: #2f664f; color: #FFFFFF; border-color: #2f664f; }

    .bm-chip {
        display: inline-flex; align-items: center; gap: .375rem; padding: .3rem .65rem;
        border-radius: .5rem; font-size: .75rem; font-weight: 600; color: #404944;
        border: 1px solid transparent; background: transparent; cursor: pointer; transition: all .15s; white-space: nowrap;
    }
    .bm-chip:hover { background: #efeee7; color: #1b1c18; }
    .bm-chip.is-active { background: #2f664f; border-color: #2f664f; color: #FFFFFF; font-weight: 700; }

    .bm-dot { width: .55rem; height: .55rem; border-radius: 999px; display: inline-block; flex-shrink: 0; }
    .bm-pill { position: relative; display: inline-flex; cursor: pointer; }
    .bm-pill input { position: absolute; opacity: 0; pointer-events: none; }
    .bm-pill > span {
        display: inline-flex; align-items: center; gap: .35rem; padding: .45rem .75rem;
        border-radius: .55rem; border: 1px solid #c0c9c2; background: #ffffff;
        font-size: .75rem; font-weight: 600; color: #404944; transition: all .15s;
    }
    .bm-pill:hover > span { border-color: #2f664f; color: #1b1c18; }
    .bm-pill input:checked + span { border-color: #2f664f; background: #2f664f; color: #ffffff; }
    .bm-pill--danger input:checked + span { border-color: #ba1a1a; background: #ffdad6; color: #93000a; }

    .bm-sw-chip {
        display: inline-flex; align-items: center; padding: .22rem .55rem;
        border-radius: .45rem; background: #f5f4ec; border: 1px solid #e4e3db;
        font-size: 10.5px; font-weight: 600; color: #404944;
    }

    /* ---------- Tarjeta flotante de PC (Popover) ---------- */
    .pc-popover {
        position: fixed; z-index: 70; width: 20rem;
        background: #ffffff; border: 1px solid #e4e3db; border-radius: .95rem;
        padding: .95rem; box-shadow: 0 16px 36px -8px rgba(27, 28, 24, .16);
        opacity: 0; transform: translateY(4px) scale(.98);
        transition: opacity .14s ease, transform .14s ease; pointer-events: none;
        color: #1b1c18;
    }
    .pc-popover.is-open { opacity: 1; transform: none; pointer-events: auto; }
    .pc-popover[hidden] { display: none; }

    /* ---------- Modales ---------- */
    .bm-modal { position: fixed; inset: 0; z-index: 80; display: flex; align-items: center; justify-content: center; padding: 1rem; }
    .bm-modal[hidden] { display: none; }
    .bm-modal--top { z-index: 90; }
    .bm-modal__backdrop { position: absolute; inset: 0; background: rgba(27, 28, 24, .65); backdrop-filter: blur(4px); }
    .bm-modal__panel {
        position: relative; width: 100%; max-height: calc(100vh - 2rem); overflow-y: auto;
        background: #ffffff; border: 1.5px solid #e4e3db; border-radius: 1rem;
        box-shadow: 0 24px 48px -16px rgba(27, 28, 24, .2); animation: bmPop .2s ease both;
        color: #1b1c18;
    }

    /* ---------- Tooltip y Toast ---------- */
    #bm-tooltip {
        position: fixed; z-index: 100; max-width: 17rem; padding: .55rem .75rem;
        border-radius: .55rem; background: #1b1c18; color: #ffffff; border: 1px solid #404944;
        font-size: 11px; font-weight: 600; line-height: 1.45; pointer-events: none;
        opacity: 0; transform: translateY(3px); transition: opacity .12s, transform .12s;
    }
    #bm-tooltip.is-open { opacity: 1; transform: none; }
    #bm-toasts { position: fixed; z-index: 100; right: 1rem; bottom: 1rem; display: flex; flex-direction: column; gap: .5rem; max-width: 24rem; }
    .bm-toast {
        display: flex; align-items: flex-start; gap: .6rem; padding: .8rem 1rem;
        border-radius: .75rem; font-size: 12px; font-weight: 600;
        box-shadow: 0 12px 24px -10px rgba(27, 28, 24, .15); animation: bmPop .2s ease both; border: 1px solid;
    }
    .bm-toast--ok { background: #e8f5ec; color: #16512c; border-color: #b3f1be; }
    .bm-toast--error { background: #ffdad6; color: #991b1b; border-color: #fca5a5; }
    .bm-toast--info { background: #fef3c7; color: #92400e; border-color: #fde68a; }
</style>

{{-- Tarjeta flotante rápida (hover) --}}
<div id="pc-popover" class="pc-popover" role="dialog" aria-label="Vista rápida de estación" hidden>
    <div class="flex items-start justify-between gap-2 pb-2.5 border-b border-surface-container-highest">
        <div class="min-w-0">
            <div class="flex items-center gap-2">
                <span class="text-sm font-extrabold font-mono text-on-surface" data-pop="codigo">PC-01</span>
                <span data-pop="badge" class="px-2 py-0.5 rounded text-[10px] font-bold">Disponible</span>
            </div>
            <span class="text-[11px] text-on-surface-variant block mt-0.5 truncate" data-pop="lab"></span>
        </div>
        <span class="text-[10px] font-mono font-bold text-primary shrink-0" data-pop="ip"></span>
    </div>
    <div class="mt-2.5 flex items-center gap-1.5 text-[11px] text-on-surface-variant">
        <svg class="w-3.5 h-3.5 text-primary shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
        <span data-pop="detalle" class="truncate"></span>
    </div>
    <div class="mt-2.5">
        <div class="text-[10px] font-bold uppercase tracking-wider text-tertiary mb-1">Especificaciones clave</div>
        <div class="flex flex-wrap gap-1" data-pop="hardware"></div>
    </div>
    <div class="mt-2">
        <div class="text-[10px] font-bold uppercase tracking-wider text-tertiary mb-1">Software activo</div>
        <div class="flex flex-wrap gap-1" data-pop="software"></div>
    </div>
    <div class="mt-3 grid grid-cols-2 gap-2">
        <button type="button" data-pc-action="reservar" class="inline-flex items-center justify-center gap-1.5 px-2.5 py-2 rounded-lg bg-primary hover:bg-primary-container text-on-primary text-[11px] font-bold transition-colors disabled:opacity-40 disabled:cursor-not-allowed">
            <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/></svg>
            <span data-pop="reservar-label">Apartar PC</span>
        </button>
        <button type="button" data-pc-action="incidencia" class="inline-flex items-center justify-center gap-1.5 px-2.5 py-2 rounded-lg bg-error-container hover:bg-error hover:text-on-error border border-error/30 text-on-error-container text-[11px] font-bold transition-colors">
            <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><line x1="12" x2="12" y1="9" y2="13"/><line x1="12" x2="12.01" y1="17" y2="17"/></svg>
            <span>Reportar fallo</span>
        </button>
    </div>
    <button type="button" data-pc-action="ficha" class="mt-2.5 w-full inline-flex items-center justify-center gap-1 text-[11px] font-bold text-primary hover:underline">
        Inspeccionar ficha técnica completa
        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
    </button>
</div>

{{-- Modal: Ficha Técnica Interactiva de Estación (PC-01 a PC-20) --}}
<div id="pc-detail-modal" class="bm-modal" role="dialog" aria-modal="true" aria-labelledby="modal-pc-codigo" hidden>
    <div class="bm-modal__backdrop" data-close-modal></div>
    <div class="bm-modal__panel sm:max-w-2xl">
        {{-- Cabecera con ilustración técnica del equipo y código de estación --}}
        <div class="bg-surface-container-low px-6 py-4 border-b border-surface-container-highest flex items-center justify-between gap-3">
            <div class="flex items-center gap-3.5 min-w-0">
                <div class="w-14 h-12 rounded-xl bg-surface-container-lowest border border-surface-container-highest flex items-center justify-center p-1.5 shrink-0">
                    <img id="modal-pc-img" src="{{ asset('images/equipos/pc-libre.svg') }}" alt="Estación de Cómputo" class="w-full h-full object-contain">
                </div>
                <div class="min-w-0">
                    <div class="flex flex-wrap items-center gap-2">
                        <h3 class="text-lg font-extrabold font-mono text-on-surface" id="modal-pc-codigo">L-01 · PC-01</h3>
                        <span id="modal-pc-badge" class="px-2.5 py-0.5 rounded-md text-xs font-bold">Disponible</span>
                        <span id="modal-pc-inv" class="px-2 py-0.5 rounded bg-surface-container-lowest border border-surface-container-highest font-mono text-[11px] text-tertiary">MN-INV-01-001</span>
                    </div>
                    <span class="text-xs text-on-surface-variant block truncate mt-0.5" id="modal-pc-lab"></span>
                </div>
            </div>
            <button type="button" data-close-modal aria-label="Cerrar" class="w-8 h-8 rounded-lg bg-surface-container-lowest hover:bg-surface-container border border-surface-container-highest text-on-surface-variant hover:text-on-surface flex items-center justify-center transition-colors shrink-0">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" x2="6" y1="6" y2="18"/><line x1="6" x2="18" y1="6" y2="18"/></svg>
            </button>
        </div>

        <div class="px-6 py-5 space-y-4">
            {{-- Banner visual del equipo + telemetría rápida --}}
            <div class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-center bg-surface-container-low p-3.5 rounded-xl border border-surface-container-highest">
                <div class="sm:col-span-4 flex justify-center">
                    <img src="{{ asset('images/equipos/pc-workstation-ficha.svg') }}" alt="Esquema Workstation MetalNet" class="h-24 object-contain">
                </div>
                <div class="sm:col-span-8 grid grid-cols-2 gap-2 text-xs">
                    <div class="p-2.5 bg-surface-container-lowest rounded-lg border border-surface-container-highest">
                        <span class="block text-[10px] font-bold uppercase text-tertiary">Estado en Tiempo Real</span>
                        <span class="font-bold text-on-surface mt-0.5 block" id="modal-pc-estado">Disponible</span>
                    </div>
                    <div class="p-2.5 bg-surface-container-lowest rounded-lg border border-surface-container-highest">
                        <span class="block text-[10px] font-bold uppercase text-tertiary">Dirección IPv4 LAN</span>
                        <span class="font-mono font-bold text-primary mt-0.5 block" id="modal-pc-ip"></span>
                    </div>
                    <div class="col-span-2 p-2.5 bg-surface-container-lowest rounded-lg border border-surface-container-highest flex items-center gap-2">
                        <svg class="w-4 h-4 text-primary shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                        <span class="text-on-surface font-medium truncate" id="modal-pc-detalle"></span>
                    </div>
                </div>
            </div>

            {{-- Especificaciones detalladas de Hardware --}}
            <div class="rounded-xl border border-surface-container-highest bg-surface-container-low overflow-hidden">
                <div class="px-4 py-2.5 bg-surface-container border-b border-surface-container-highest flex items-center justify-between">
                    <span class="flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-on-surface">
                        <svg class="w-4 h-4 text-primary" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="4" y="4" width="16" height="16" rx="2"/><rect x="9" y="9" width="6" height="6"/><path d="M15 2v2M15 20v2M2 15h2M2 9h2M20 15h2M20 9h2M9 2v2M9 20v2"/></svg>
                        Especificaciones de Hardware
                    </span>
                    <span class="text-[11px] font-mono font-bold text-primary" id="modal-pc-sala"></span>
                </div>
                <div class="p-3.5 grid grid-cols-1 sm:grid-cols-2 gap-2.5 text-xs" id="modal-pc-hw-grid"></div>
            </div>

            {{-- Software instalado en este equipo con versiones --}}
            <div class="rounded-xl border border-surface-container-highest bg-surface-container-low overflow-hidden">
                <div class="px-4 py-2.5 bg-surface-container border-b border-surface-container-highest flex items-center justify-between">
                    <span class="flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-on-surface">
                        <svg class="w-4 h-4 text-primary" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="4 17 10 11 4 5"/><line x1="12" x2="20" y1="19" y2="19"/></svg>
                        Software Instalado en Esta Estación
                    </span>
                    <span class="text-[11px] font-mono text-tertiary" id="modal-pc-sw-count"></span>
                </div>
                <div class="p-3.5 flex flex-wrap gap-1.5" id="modal-pc-software"></div>
            </div>

            <div id="modal-pc-feedback" class="hidden p-3 rounded-xl text-xs font-semibold"></div>

            {{-- Consola rápida de Cambio de Estado / Reparación de Avería (exclusivo Admin y Encargado) --}}
            @if($isStaff)
            <form method="POST" action="{{ route('panel.pc-estado') }}" id="form-staff-pc-estado" class="p-3.5 rounded-xl bg-surface-container-low border border-primary/40 space-y-2.5">
                @csrf
                <input type="hidden" name="estacion_id" id="staff-pc-id" value="">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-extrabold uppercase tracking-wider text-primary flex items-center gap-1.5">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>
                        Control Técnico ({{ strtoupper($userRoleSlug) }}) · Cambiar Estado / Reparar Avería
                    </span>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                    <select name="estado" id="staff-pc-select-estado" class="h-9 px-2.5 rounded-lg bg-surface-container-lowest border border-outline-variant text-xs font-bold text-on-surface">
                        <option value="disponible">Disponible (Libre)</option>
                        <option value="ocupado">Ocupado (En Uso)</option>
                        <option value="mantenimiento">No Disponible (En Reparación)</option>
                    </select>
                    <input type="text" name="nota" placeholder="Bitácora técnica (ej. Cambio de SSD NVMe)" class="sm:col-span-2 h-9 px-3 rounded-lg bg-surface-container-lowest border border-outline-variant text-xs text-on-surface">
                </div>
                <div class="flex justify-end">
                    <button type="submit" class="px-4 py-1.5 rounded-lg bg-primary hover:bg-primary-container text-on-primary text-xs font-bold transition-colors cursor-pointer">
                        Aplicar cambio de estado
                    </button>
                </div>
            </form>
            @endif
        </div>

        {{-- Botones de acción contextual --}}
        <div class="bg-surface-container-low px-6 py-4 border-t border-surface-container-highest flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-2.5">
            <div class="flex flex-col sm:flex-row gap-2">
                <button type="button" id="btn-reservar-equipo" data-pc-action="reservar" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-primary hover:bg-primary-container text-on-primary text-xs font-bold transition-colors disabled:opacity-40 disabled:cursor-not-allowed cursor-pointer">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/></svg>
                    <span id="btn-reservar-label">Apartar esta computadora</span>
                </button>
                <button type="button" id="btn-reportar-incidencia" data-pc-action="incidencia" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-error-container hover:bg-error hover:text-on-error border border-error/30 text-on-error-container text-xs font-bold transition-colors cursor-pointer">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><line x1="12" x2="12" y1="9" y2="13"/><line x1="12" x2="12.01" y1="17" y2="17"/></svg>
                    <span>Reportar fallo en este equipo</span>
                </button>
            </div>
            <button type="button" id="btn-cancelar-reserva" data-pc-action="cancelar" class="hidden px-4 py-2.5 bg-surface-container hover:bg-surface-container-high border border-surface-container-highest text-on-surface rounded-xl text-xs font-semibold transition-colors">
                Liberar mi reserva
            </button>
        </div>
    </div>
</div>

{{-- Sub-modal: Apartar esta computadora --}}
<div id="modal-reserva-rapida" class="bm-modal bm-modal--top" role="dialog" aria-modal="true" aria-labelledby="reserva-titulo" hidden>
    <div class="bm-modal__backdrop" data-close-modal></div>
    <div class="bm-modal__panel sm:max-w-md p-6">
        <div class="flex items-center justify-between pb-3 border-b border-surface-container-highest">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-primary" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" x2="16" y1="21" y2="21"/><line x1="12" x2="12" y1="17" y2="21"/></svg>
                <h4 class="text-base font-bold text-on-surface" id="reserva-titulo">Apartar Estación · <span id="reserva-pc-nombre" class="font-mono text-primary">PC-01</span></h4>
            </div>
            <button type="button" data-close-modal aria-label="Cerrar" class="text-tertiary hover:text-on-surface">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" x2="6" y1="6" y2="18"/><line x1="6" x2="18" y1="6" y2="18"/></svg>
            </button>
        </div>
        <form id="form-reserva-rapida" method="POST" action="{{ route('panel.reservar-pc') }}" class="mt-4 space-y-4">
            @csrf
            <input type="hidden" name="estacion_id" id="reserva-estacion-id" value="">
            <div>
                <label for="reserva-matricula" class="block text-xs font-bold text-tertiary uppercase mb-1">Identificador / Matrícula MetalNet</label>
                <input type="text" id="reserva-matricula" required value="{{ Auth::check() ? (Auth::user()->matricula ?? Auth::user()->email) : '' }}" placeholder="Ej. MN-ALU-2026" class="w-full h-10 px-3 bg-surface-container-lowest rounded-lg border border-outline-variant text-xs text-on-surface font-mono focus:outline-none focus:border-primary">
            </div>
            <div>
                <span class="block text-xs font-bold text-tertiary uppercase mb-1.5">Duración de práctica libre</span>
                <div class="flex flex-wrap gap-2">
                    <label class="bm-pill"><input type="radio" name="duracion" value="1"><span>1 hora</span></label>
                    <label class="bm-pill"><input type="radio" name="duracion" value="2" checked><span>2 horas</span></label>
                    <label class="bm-pill"><input type="radio" name="duracion" value="3"><span>3 horas</span></label>
                </div>
            </div>
            <div>
                <label for="reserva-actividad" class="block text-xs font-bold text-tertiary uppercase mb-1">Actividad a realizar</label>
                <input type="text" name="actividad" id="reserva-actividad" required value="Práctica libre de ingeniería" class="w-full h-10 px-3 bg-surface-container-lowest rounded-lg border border-outline-variant text-xs text-on-surface focus:outline-none focus:border-primary">
            </div>
            <div class="pt-2 flex items-center justify-end gap-2">
                <button type="button" data-close-modal class="px-4 py-2 bg-surface-container hover:bg-surface-container-high rounded-lg text-xs font-semibold text-on-surface">Cancelar</button>
                <button type="submit" class="px-5 py-2 bg-primary hover:bg-primary-container text-on-primary rounded-lg text-xs font-bold cursor-pointer">Confirmar apartado</button>
            </div>
        </form>
    </div>
</div>

{{-- Sub-modal: Aviso de inicio de sesión requerido --}}
<div id="modal-login-forzoso" class="bm-modal bm-modal--top" role="dialog" aria-modal="true" aria-labelledby="login-forzoso-titulo" hidden>
    <div class="bm-modal__backdrop" data-close-modal></div>
    <div class="bm-modal__panel sm:max-w-md p-6">
        <div class="flex items-center justify-between pb-3 border-b border-surface-container-highest">
            <div class="flex items-center gap-2.5">
                <span class="w-9 h-9 rounded-xl bg-primary text-on-primary flex items-center justify-center">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                </span>
                <div>
                    <h4 class="text-base font-extrabold text-on-surface" id="login-forzoso-titulo">Autenticación requerida</h4>
                    <span class="text-[11px] text-on-surface-variant block">Recurso: <strong id="login-forzoso-pc" class="font-mono text-primary">PC-01</strong></span>
                </div>
            </div>
            <button type="button" data-close-modal aria-label="Cerrar" class="text-tertiary hover:text-on-surface">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" x2="6" y1="6" y2="18"/><line x1="6" x2="18" y1="6" y2="18"/></svg>
            </button>
        </div>
        <div class="mt-4 space-y-4 text-xs text-on-surface-variant">
            <p class="leading-relaxed">
                Para apartar estaciones de cómputo, reservar laboratorios o solicitar instalación de software, inicia sesión con tu cuenta institucional de <strong class="text-on-surface">MetalNet</strong>.
            </p>
            <div class="flex items-center justify-end gap-2 pt-2">
                <button type="button" data-close-modal class="px-4 py-2 bg-surface-container hover:bg-surface-container-high rounded-lg text-xs font-semibold text-on-surface">Cerrar</button>
                <a href="{{ route('login') }}" id="lf-ir-login" class="px-5 py-2 bg-primary hover:bg-primary-container text-on-primary rounded-lg text-xs font-bold">Ir a Acceso Institucional</a>
            </div>
        </div>
    </div>
</div>

{{-- Sub-modal: Reportar fallo en este equipo --}}
<div id="modal-incidencia-rapida" class="bm-modal bm-modal--top" role="dialog" aria-modal="true" aria-labelledby="incidencia-titulo" hidden>
    <div class="bm-modal__backdrop" data-close-modal></div>
    <div class="bm-modal__panel sm:max-w-md p-6">
        <div class="flex items-center justify-between pb-3 border-b border-surface-container-highest">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-error" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><line x1="12" x2="12" y1="9" y2="13"/><line x1="12" x2="12.01" y1="17" y2="17"/></svg>
                <h4 class="text-base font-bold text-on-surface" id="incidencia-titulo">Reportar Fallo · <span id="incidencia-pc-nombre" class="font-mono text-error">PC-01</span></h4>
            </div>
            <button type="button" data-close-modal aria-label="Cerrar" class="text-tertiary hover:text-on-surface">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" x2="6" y1="6" y2="18"/><line x1="6" x2="18" y1="6" y2="18"/></svg>
            </button>
        </div>
        <form id="form-incidencia-rapida" method="POST" action="{{ route('public.averia.store') }}" class="mt-4 space-y-3">
            @csrf
            <input type="hidden" name="sala" id="inc-sala-input" value="L-01">
            <input type="hidden" name="pc" id="inc-pc-input" value="PC-01">
            <div class="grid grid-cols-2 gap-2.5">
                <div>
                    <label for="inc-matricula" class="block text-[11px] font-bold text-tertiary uppercase mb-1">1. Matrícula / Clave *</label>
                    <input type="text" name="matricula" id="inc-matricula" required value="{{ Auth::check() ? (Auth::user()->matricula ?? Auth::user()->email) : '' }}" placeholder="MN-ALU-2026" class="w-full h-9 px-2.5 bg-surface-container-lowest rounded-lg border border-outline-variant text-xs font-mono text-on-surface focus:outline-none focus:border-primary">
                </div>
                <div>
                    <label for="inc-nombre" class="block text-[11px] font-bold text-tertiary uppercase mb-1">2. Nombre *</label>
                    <input type="text" name="nombre" id="inc-nombre" required value="{{ Auth::check() ? Auth::user()->name : '' }}" placeholder="Nombre completo" class="w-full h-9 px-2.5 bg-surface-container-lowest rounded-lg border border-outline-variant text-xs text-on-surface focus:outline-none focus:border-primary">
                </div>
            </div>
            <div>
                <span class="block text-[11px] font-bold text-tertiary uppercase mb-1">3. Categoría de Fallo *</span>
                <div class="grid grid-cols-2 gap-1.5">
                    <label class="bm-pill bm-pill--danger"><input type="radio" name="motivo" value="Periféricos" checked><span class="w-full">Periféricos</span></label>
                    <label class="bm-pill bm-pill--danger"><input type="radio" name="motivo" value="Red"><span class="w-full">Red / VLAN</span></label>
                    <label class="bm-pill bm-pill--danger"><input type="radio" name="motivo" value="Software"><span class="w-full">Software / Licencia</span></label>
                    <label class="bm-pill bm-pill--danger"><input type="radio" name="motivo" value="Hardware"><span class="w-full">Hardware / Energía</span></label>
                </div>
            </div>
            <div>
                <label for="incidencia-descripcion" class="block text-[11px] font-bold text-tertiary uppercase mb-1">4. Descripción Técnica *</label>
                <textarea name="causa" id="incidencia-descripcion" rows="2" required placeholder="Describe el síntoma detectado en la estación..." class="w-full p-2.5 bg-surface-container-lowest rounded-lg border border-outline-variant text-xs text-on-surface focus:outline-none focus:border-primary"></textarea>
            </div>
            <div class="pt-2 flex items-center justify-end gap-2">
                <button type="button" data-close-modal class="px-3.5 py-2 bg-surface-container hover:bg-surface-container-high rounded-lg text-xs font-semibold text-on-surface">Cancelar</button>
                <button type="submit" class="px-4 py-2 bg-error hover:bg-[#93000a] text-on-error rounded-lg text-xs font-bold cursor-pointer">Enviar Reporte</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Global: Solicitar Software / Actualización / Hardware al Administrador --}}
<div id="modal-solicitar-software" class="bm-modal bm-modal--top" role="dialog" aria-modal="true" aria-labelledby="solicitud-sw-titulo" hidden>
    <div class="bm-modal__backdrop" data-close-modal></div>
    <div class="bm-modal__panel sm:max-w-lg p-6">
        <div class="flex items-center justify-between pb-3 border-b border-surface-container-highest">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-primary flex items-center justify-center text-on-primary">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" x2="12" y1="22.08" y2="12"/></svg>
                </div>
                <div>
                    <h4 class="text-base font-extrabold text-on-surface" id="solicitud-sw-titulo">Solicitar Software / Recurso al Administrador</h4>
                    <span class="text-[11px] text-on-surface-variant block">El ticket se enviará en tiempo real a la consola del Administrador MetalNet</span>
                </div>
            </div>
            <button type="button" data-close-modal aria-label="Cerrar" class="text-tertiary hover:text-on-surface">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" x2="6" y1="6" y2="18"/><line x1="6" x2="18" y1="6" y2="18"/></svg>
            </button>
        </div>

        <form method="POST" action="{{ route('panel.solicitar-recurso') }}" class="mt-4 space-y-3.5">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-[11px] font-bold uppercase text-tertiary mb-1">Tipo de Solicitud *</label>
                    <select name="tipo" id="sol-sw-tipo" class="w-full h-10 px-3 rounded-lg bg-surface-container-lowest border border-outline-variant text-xs font-bold text-on-surface">
                        <option value="Software">Instalación / Actualización de Software</option>
                        <option value="Hardware">Préstamo de Hardware / Periféricos</option>
                        <option value="Mesa">Asignación de Mesa de Trabajo</option>
                    </select>
                </div>
                <div>
                    <label class="block text-[11px] font-bold uppercase text-tertiary mb-1">Laboratorio Destino *</label>
                    <select name="sala" id="sol-sw-sala" class="w-full h-10 px-3 rounded-lg bg-surface-container-lowest border border-outline-variant text-xs font-bold text-on-surface">
                        @foreach($bmLabsList as $labOpt)
                            <option value="{{ $labOpt['codigo'] }}">{{ $labOpt['codigo'] }} · {{ $labOpt['nombre_corto'] }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-[11px] font-bold uppercase text-tertiary mb-1">Software, Paquete o Equipo Requerido *</label>
                <input type="text" name="recurso" id="sol-sw-recurso" required placeholder="Ej. PyTorch 2.6 + CUDA 12.6, Analizador Lógico, etc." class="w-full h-10 px-3 rounded-lg bg-surface-container-lowest border border-outline-variant text-xs text-on-surface focus:outline-none focus:border-primary">
            </div>
            <div>
                <label class="block text-[11px] font-bold uppercase text-tertiary mb-1">Justificación Académica / Versión / Módulos *</label>
                <textarea name="descripcion" id="sol-sw-desc" rows="3" required placeholder="Indica la asignatura, versión requerida o detalles para el Administrador..." class="w-full p-3 rounded-lg bg-surface-container-lowest border border-outline-variant text-xs text-on-surface focus:outline-none focus:border-primary"></textarea>
            </div>
            <div class="pt-2 flex items-center justify-end gap-2">
                <button type="button" data-close-modal class="px-4 py-2 bg-surface-container hover:bg-surface-container-high rounded-lg text-xs font-semibold text-on-surface">Cancelar</button>
                <button type="submit" class="px-5 py-2 bg-primary hover:bg-primary-container text-on-primary rounded-lg text-xs font-bold cursor-pointer">Enviar Solicitud al Admin</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal: Mapa Maestro y Detalle Técnico de Laboratorios (200 PCs) --}}
<div id="modal-mapa-labs" class="bm-modal" role="dialog" aria-modal="true" aria-labelledby="mapa-labs-titulo" hidden>
    <div class="bm-modal__backdrop" data-close-modal></div>
    <div class="bm-modal__panel sm:max-w-5xl" data-pc-scope>
        <div class="sticky top-0 z-10 bg-surface-container-lowest px-5 sm:px-6 pt-4 pb-3 border-b border-surface-container-highest">
            <div class="flex items-center justify-between gap-3">
                <div class="flex items-center gap-2.5">
                    <svg class="w-5 h-5 text-primary" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="3 6 9 3 15 6 21 3 21 18 15 21 9 18 3 21"/><line x1="9" x2="9" y1="3" y2="18"/><line x1="15" x2="15" y1="6" y2="21"/></svg>
                    <h3 class="text-lg font-extrabold text-on-surface" id="mapa-labs-titulo">Detalle Técnico y Plano de Sala (200 Estaciones)</h3>
                </div>
                <div class="flex items-center gap-2">
                    <div class="hidden md:flex items-center gap-1 bg-surface-container-low px-2 py-1 rounded-xl border border-surface-container-highest">
                        <button type="button" class="bm-chip is-active" data-pc-filter="todos">Todas</button>
                        <button type="button" class="bm-chip" data-pc-filter="disponible"><span class="bm-dot bg-[#16a34a]"></span>Disponible</button>
                        <button type="button" class="bm-chip" data-pc-filter="ocupado"><span class="bm-dot bg-[#eab308]"></span>Ocupado</button>
                        <button type="button" class="bm-chip" data-pc-filter="mantenimiento"><span class="bm-dot bg-[#dc2626]"></span>No disponible</button>
                    </div>
                    <button type="button" data-close-modal aria-label="Cerrar" class="w-8 h-8 rounded-lg bg-surface-container hover:bg-surface-container-high border border-surface-container-highest text-on-surface-variant hover:text-on-surface flex items-center justify-center transition-colors">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" x2="6" y1="6" y2="18"/><line x1="6" x2="18" y1="6" y2="18"/></svg>
                    </button>
                </div>
            </div>
            <div class="mt-3 flex items-center gap-2 overflow-x-auto pb-1" data-tabs="mapa-modal">
                @foreach($bmLabsList as $i => $labMapa)
                    <button type="button" class="bm-tab {{ $i === 0 ? 'is-active' : '' }}" data-tab="{{ $labMapa['id'] }}">
                        <span class="font-mono font-extrabold">{{ $labMapa['codigo'] }}</span>
                        <span class="hidden lg:inline font-semibold opacity-90">· {{ $labMapa['nombre_corto'] }}</span>
                    </button>
                @endforeach
            </div>
        </div>
        <div class="p-5 sm:p-6" data-tab-group="mapa-modal">
            @foreach($bmLabsList as $i => $labMapa)
                <div data-tab-panel="{{ $labMapa['id'] }}" class="{{ $i === 0 ? '' : 'hidden' }}">
                    @include('partials.lab-sala', ['lab' => $labMapa])
                </div>
            @endforeach
        </div>
    </div>
</div>

<div id="bm-tooltip" role="tooltip"></div>
<div id="bm-toasts" aria-live="polite"></div>

<script>
    window.BM_DATA = {
        loggedIn: @json(Auth::check()),
        userRole: @json($userRoleSlug),
        userMatricula: @json(Auth::user()->matricula ?? Auth::user()->email ?? null),
        labs: @json($bmLabsJs ?? []),
        software: @json($bmSoftwareJs ?? []),
        assets: {
            pcLibre: @json(asset('images/equipos/pc-libre.svg')),
            pcOcupada: @json(asset('images/equipos/pc-ocupada.svg')),
            pcNoDisponible: @json(asset('images/equipos/pc-no-disponible.svg')),
        },
        urls: {
            login: @json(url('/login')),
            averia: @json(url('/reportar-averia')),
            software: @json(url('/software')),
            salas: @json(url('/salas')),
            horarios: @json(url('/horarios')),
        }
    };

    window.MetalNetUI = (function () {
        const stack = [];

        function openModal(id) {
            const m = document.getElementById(id);
            if (!m) return;
            m.hidden = false;
            if (!stack.includes(id)) stack.push(id);
            document.documentElement.style.overflow = 'hidden';
        }

        function closeModal(id) {
            const m = document.getElementById(id);
            if (!m) return;
            m.hidden = true;
            const i = stack.indexOf(id);
            if (i > -1) stack.splice(i, 1);
            if (!stack.length) document.documentElement.style.overflow = '';
        }

        function closeTop() {
            if (stack.length) { closeModal(stack[stack.length - 1]); return true; }
            return false;
        }

        function toggle(targetId) {
            const el = document.getElementById(targetId);
            if (!el) return;
            const show = el.classList.contains('hidden');
            el.classList.toggle('hidden', !show);
            if (show) { el.classList.remove('bm-reveal'); void el.offsetWidth; el.classList.add('bm-reveal'); }
            document.querySelectorAll('[data-toggle="' + targetId + '"]').forEach(b => {
                b.setAttribute('aria-expanded', show ? 'true' : 'false');
                const lbl = b.querySelector('[data-toggle-label]');
                if (lbl && lbl.dataset.open && lbl.dataset.closed) lbl.textContent = show ? lbl.dataset.open : lbl.dataset.closed;
            });
        }

        function activateTab(group, key) {
            document.querySelectorAll('[data-tabs="' + group + '"] [data-tab]').forEach(b => b.classList.toggle('is-active', b.dataset.tab == key));
            document.querySelectorAll('[data-tab-group="' + group + '"] > [data-tab-panel]').forEach(p => {
                const on = p.dataset.tabPanel == key;
                p.classList.toggle('hidden', !on);
                if (on) { p.classList.remove('bm-reveal'); void p.offsetWidth; p.classList.add('bm-reveal'); }
            });
            document.dispatchEvent(new CustomEvent('bm:tab', { detail: { group, key } }));
        }

        function toast(html, tone = 'ok', ms = 4200) {
            const wrap = document.getElementById('bm-toasts');
            if (!wrap) return;
            const t = document.createElement('div');
            t.className = 'bm-toast bm-toast--' + tone;
            t.innerHTML = '<span>' + html + '</span>';
            wrap.appendChild(t);
            setTimeout(() => { t.style.transition = 'opacity .25s'; t.style.opacity = '0'; setTimeout(() => t.remove(), 260); }, ms);
        }

        function abrirSolicitudSoftware(nombreSw = '', tipo = 'Software', sala = 'L-01') {
            if (!window.BM_DATA.loggedIn) {
                const url = window.BM_DATA.urls.login + '?motivo=solicitar_software' + (nombreSw ? '&software=' + encodeURIComponent(nombreSw) : '');
                window.location.href = url;
                return;
            }
            const inputRec = document.getElementById('sol-sw-recurso');
            const selectTipo = document.getElementById('sol-sw-tipo');
            const selectSala = document.getElementById('sol-sw-sala');
            if (inputRec && nombreSw) inputRec.value = nombreSw;
            if (selectTipo && tipo) selectTipo.value = tipo;
            if (selectSala && sala) selectSala.value = sala;
            openModal('modal-solicitar-software');
        }

        const tip = () => document.getElementById('bm-tooltip');
        function showTip(el) {
            const t = tip(); if (!t) return;
            t.textContent = el.dataset.tip;
            t.style.left = '0px'; t.style.top = '0px';
            t.classList.add('is-open');
            const r = el.getBoundingClientRect();
            const w = t.offsetWidth, h = t.offsetHeight;
            let left = Math.min(Math.max(8, r.left + r.width / 2 - w / 2), window.innerWidth - w - 8);
            let top = r.top - h - 8;
            if (top < 8) top = r.bottom + 8;
            t.style.left = left + 'px'; t.style.top = top + 'px';
        }
        function hideTip() { const t = tip(); if (t) t.classList.remove('is-open'); }

        document.addEventListener('mouseover', e => { const el = e.target.closest('[data-tip]'); if (el) showTip(el); });
        document.addEventListener('mouseout', e => { const el = e.target.closest('[data-tip]'); if (el && !el.contains(e.relatedTarget)) hideTip(); });
        window.addEventListener('scroll', hideTip, { passive: true });

        document.addEventListener('click', e => {
            const tg = e.target.closest('[data-toggle]');
            if (tg) { e.preventDefault(); toggle(tg.dataset.toggle, tg); return; }

            const tab = e.target.closest('[data-tabs] [data-tab]');
            if (tab) { activateTab(tab.closest('[data-tabs]').dataset.tabs, tab.dataset.tab); return; }

            const close = e.target.closest('[data-close-modal]');
            if (close) { const m = close.closest('.bm-modal'); if (m) closeModal(m.id); return; }

            const solBtn = e.target.closest('[data-open-solicitud-software]');
            if (solBtn) {
                e.preventDefault();
                abrirSolicitudSoftware(solBtn.dataset.software || '', solBtn.dataset.tipo || 'Software', solBtn.dataset.sala || 'L-01');
                return;
            }
        });

        return { openModal, closeModal, closeTop, toggle, activateTab, toast, abrirSolicitudSoftware };
    })();

    function toggleElement(elementId, iconId = null) {
        const el = document.getElementById(elementId);
        if (!el) return;
        el.classList.toggle('hidden');
        if (iconId) { const ic = document.getElementById(iconId); if (ic) ic.classList.toggle('rotate-180'); }
    }

    window.MetalNetPC = (function () {
        const LABS = window.BM_DATA.labs || [];
        const PCS = {};
        LABS.forEach(l => l.pcs.forEach(p => { p.lab = l; p.original = p.estado; PCS[p.id] = p; }));

        const LABEL = {
            disponible: 'Disponible',
            ocupado: 'Ocupado',
            mantenimiento: 'No disponible',
            reservado: 'Ocupado (Apartado)',
        };
        /* 3 ÚNICOS COLORES SEMÁNTICOS: VERDE / AMARILLO / ROJO */
        const BADGE = {
            disponible: 'bg-[#dcfce7] text-[#16512c] border border-[#86efac]',
            ocupado: 'bg-[#fef3c7] text-[#854d0e] border border-[#eab308]',
            mantenimiento: 'bg-[#fee2e2] text-[#991b1b] border border-[#f87171]',
            reservado: 'bg-[#fef3c7] text-[#854d0e] border border-dashed border-[#d97706]',
        };
        let actual = null;
        let hideTimer = null, showTimer = null;
        const canHover = window.matchMedia('(hover: hover) and (pointer: fine)').matches;

        const esc = s => String(s ?? '').replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
        const $ = id => document.getElementById(id);

        function getImgForState(st) {
            if (st === 'disponible') return window.BM_DATA.assets.pcLibre;
            if (st === 'mantenimiento') return window.BM_DATA.assets.pcNoDisponible;
            return window.BM_DATA.assets.pcOcupada;
        }

        function llenarPopover(p) {
            const pop = $('pc-popover');
            if (!pop) return;
            pop.querySelector('[data-pop="codigo"]').textContent = p.lab.codigo + ' · ' + p.codigo;
            pop.querySelector('[data-pop="lab"]').textContent = p.lab.nombre;
            pop.querySelector('[data-pop="ip"]').textContent = p.ip;
            pop.querySelector('[data-pop="detalle"]').textContent = p.detalle;
            const b = pop.querySelector('[data-pop="badge"]');
            b.textContent = LABEL[p.estado] || 'Disponible';
            b.className = 'px-2 py-0.5 rounded text-[10px] font-bold ' + (BADGE[p.estado] || BADGE.disponible);
            pop.querySelector('[data-pop="hardware"]').innerHTML = p.lab.hardware.slice(0, 3).map(h => '<span class="bm-sw-chip">' + esc(h) + '</span>').join('');
            const sw = p.lab.software || [];
            pop.querySelector('[data-pop="software"]').innerHTML = sw.slice(0, 4).map(s => '<span class="bm-sw-chip">' + esc(s) + '</span>').join('') + (sw.length > 4 ? '<span class="bm-sw-chip text-primary">+' + (sw.length - 4) + '</span>' : '');
            const btn = pop.querySelector('[data-pc-action="reservar"]');
            btn.disabled = p.estado !== 'disponible';
            pop.querySelector('[data-pop="reservar-label"]').textContent = p.estado === 'disponible' ? 'Apartar PC' : LABEL[p.estado];
        }

        function mostrarPopover(tile) {
            const p = PCS[tile.dataset.pcId]; if (!p) return;
            actual = p;
            const pop = $('pc-popover'); if (!pop) return;
            llenarPopover(p);
            pop.hidden = false;
            pop.style.visibility = 'hidden';
            const r = tile.getBoundingClientRect();
            const w = pop.offsetWidth, h = pop.offsetHeight;
            let left = r.right + 10, top;
            if (left + w > window.innerWidth - 8) left = r.left - w - 10;
            if (left < 8) {
                left = Math.min(Math.max(8, r.left + r.width / 2 - w / 2), window.innerWidth - w - 8);
                top = r.bottom + 8;
                if (top + h > window.innerHeight - 8) top = r.top - h - 8;
            } else {
                top = r.top + r.height / 2 - h / 2;
            }
            top = Math.min(Math.max(8, top), window.innerHeight - h - 8);
            pop.style.left = left + 'px';
            pop.style.top = top + 'px';
            pop.style.visibility = '';
            requestAnimationFrame(() => pop.classList.add('is-open'));
            document.querySelectorAll('.pc-tile.is-active').forEach(t => t.classList.remove('is-active'));
            tile.classList.add('is-active');
        }

        function ocultarPopover(inmediato = false) {
            clearTimeout(showTimer);
            const go = () => {
                const pop = $('pc-popover'); if (!pop) return;
                pop.classList.remove('is-open');
                setTimeout(() => { if (!pop.classList.contains('is-open')) pop.hidden = true; }, 150);
                document.querySelectorAll('.pc-tile.is-active').forEach(t => t.classList.remove('is-active'));
            };
            clearTimeout(hideTimer);
            if (inmediato) go(); else hideTimer = setTimeout(go, 180);
        }

        function abrirFicha(id) {
            const p = PCS[id] || actual; if (!p) return;
            actual = p;
            ocultarPopover(true);

            $('modal-pc-codigo').textContent = p.lab.codigo + ' · ' + p.codigo;
            $('modal-pc-lab').textContent = p.lab.nombre + ' · ' + p.lab.ubicacion;
            $('modal-pc-ip').textContent = p.ip;
            $('modal-pc-sala').textContent = 'Inventario: ' + (p.inventario || ('MN-INV-' + p.id));
            if ($('modal-pc-inv')) $('modal-pc-inv').textContent = p.inventario || ('MN-INV-' + p.id);
            $('modal-pc-estado').textContent = LABEL[p.estado] || 'Disponible';
            $('modal-pc-detalle').textContent = p.detalle;

            const imgEl = $('modal-pc-img');
            if (imgEl) imgEl.src = getImgForState(p.estado);

            const badge = $('modal-pc-badge');
            badge.textContent = LABEL[p.estado] || 'Disponible';
            badge.className = 'px-2.5 py-0.5 rounded-md text-xs font-bold ' + (BADGE[p.estado] || BADGE.disponible);

            const hw = p.hw || p.lab.hw_detallado || {};
            const hwItems = [
                ['Procesador (CPU)', hw.cpu || p.lab.hardware[0] || 'Workstation CPU'],
                ['Memoria RAM', hw.ram || p.lab.hardware[1] || '32 GB DDR5'],
                ['Gráficos (GPU)', hw.gpu || 'Integrada / Dedicada'],
                ['Almacenamiento SSD/NVMe', hw.almacenamiento || p.lab.hardware[2] || '1 TB NVMe PCIe 4.0'],
                ['Monitor', hw.monitor || p.lab.hardware[3] || 'Monitor 27" IPS'],
                ['Periféricos y Red', (hw.perifericos || 'Teclado y Mouse óptico') + ' · ' + (hw.os || 'Dual Boot')],
            ];
            $('modal-pc-hw-grid').innerHTML = hwItems.map(([k, v]) => (
                '<div class="p-2.5 rounded-lg bg-surface-container-lowest border border-surface-container-highest">' +
                    '<span class="block text-[10px] font-bold uppercase text-tertiary">' + esc(k) + '</span>' +
                    '<span class="font-semibold text-on-surface mt-0.5 block">' + esc(v) + '</span>' +
                '</div>'
            )).join('');

            const swList = (p.sw_versiones && p.sw_versiones.length) ? p.sw_versiones : (p.lab.software || []);
            $('modal-pc-software').innerHTML = swList.map(s => '<span class="bm-sw-chip">' + esc(s) + '</span>').join('');
            $('modal-pc-sw-count').textContent = swList.length + ' aplicaciones activas';

            const staffIdInput = $('staff-pc-id');
            const staffSelect = $('staff-pc-select-estado');
            if (staffIdInput) staffIdInput.value = p.id;
            if (staffSelect) staffSelect.value = p.estado === 'reservado' ? 'ocupado' : p.estado;

            const btn = $('btn-reservar-equipo');
            btn.disabled = p.estado !== 'disponible';
            $('btn-reservar-label').textContent = p.estado === 'disponible' ? 'Apartar esta computadora' : (p.estado === 'ocupado' ? 'Estación ocupada' : 'Estación no disponible');

            MetalNetUI.openModal('pc-detail-modal');
        }

        function abrirReserva() {
            if (!actual || actual.estado !== 'disponible') return;
            ocultarPopover(true);
            if (!window.BM_DATA.loggedIn) {
                $('login-forzoso-pc').textContent = actual.lab.codigo + ' · ' + actual.codigo;
                const link = $('lf-ir-login');
                if (link) link.href = window.BM_DATA.urls.login + '?pc=' + encodeURIComponent(actual.id);
                MetalNetUI.openModal('modal-login-forzoso');
                return;
            }
            $('reserva-pc-nombre').textContent = actual.lab.codigo + ' · ' + actual.codigo;
            const estInput = $('reserva-estacion-id');
            if (estInput) estInput.value = actual.id;
            MetalNetUI.openModal('modal-reserva-rapida');
        }

        function abrirIncidencia() {
            if (!actual) return;
            ocultarPopover(true);
            $('incidencia-pc-nombre').textContent = actual.lab.codigo + ' · ' + actual.codigo;
            if ($('inc-sala-input')) $('inc-sala-input').value = actual.lab.codigo;
            if ($('inc-pc-input')) $('inc-pc-input').value = actual.codigo;
            MetalNetUI.openModal('modal-incidencia-rapida');
        }

        function filtrar(scope, estado) {
            scope.querySelectorAll('[data-pc-filter]').forEach(b => b.classList.toggle('is-active', b.dataset.pcFilter === estado));
            scope.querySelectorAll('.pc-tile').forEach(t => {
                const e = t.dataset.estado === 'reservado' ? 'ocupado' : t.dataset.estado;
                t.classList.toggle('is-dimmed', estado !== 'todos' && e !== estado);
            });
        }

        function abrirMapa(lab, pcId = null, filtro = 'todos') {
            const l = LABS.find(x => x.id == lab || x.codigo === lab) || LABS[0];
            if (!l) return;
            MetalNetUI.activateTab('mapa-modal', l.id);
            const modal = $('modal-mapa-labs');
            filtrar(modal.querySelector('[data-pc-scope]') || modal, filtro);
            MetalNetUI.openModal('modal-mapa-labs');
            if (pcId) {
                setTimeout(() => {
                    const t = modal.querySelector('[data-pc-id="' + pcId + '"]');
                    if (t) { t.scrollIntoView({ block: 'center', behavior: 'smooth' }); t.classList.add('is-highlight'); setTimeout(() => t.classList.remove('is-highlight'), 3200); }
                }, 120);
            }
        }

        document.addEventListener('click', e => {
            const f = e.target.closest('[data-pc-filter]');
            if (f) { filtrar(f.closest('[data-pc-scope]') || document, f.dataset.pcFilter); return; }

            const m = e.target.closest('[data-open-map]');
            if (m) { e.preventDefault(); abrirMapa(m.dataset.openMap, m.dataset.pc || null, m.dataset.filter || 'todos'); return; }

            const a = e.target.closest('[data-pc-action]');
            if (a) {
                const act = a.dataset.pcAction;
                if (act === 'reservar') abrirReserva();
                else if (act === 'incidencia') abrirIncidencia();
                else if (act === 'ficha') abrirFicha(actual && actual.id);
                return;
            }

            const tile = e.target.closest('.pc-tile[data-pc-id]');
            if (tile) {
                const picker = tile.closest('[data-pc-picker]');
                const p = PCS[tile.dataset.pcId];
                if (picker) {
                    picker.querySelectorAll('.pc-tile.is-picked').forEach(t => t.classList.remove('is-picked'));
                    tile.classList.add('is-picked');
                    document.dispatchEvent(new CustomEvent('bm:pc-pick', { detail: { pc: p, lab: p ? p.lab : null } }));
                    return;
                }
                abrirFicha(tile.dataset.pcId);
            }
        });

        if (canHover) {
            document.addEventListener('mouseover', e => {
                const tile = e.target.closest('.pc-tile[data-pc-id]');
                if (tile && !tile.closest('[data-pc-picker]')) {
                    clearTimeout(hideTimer); clearTimeout(showTimer);
                    showTimer = setTimeout(() => mostrarPopover(tile), 90);
                    return;
                }
                if (e.target.closest('#pc-popover')) clearTimeout(hideTimer);
            });
            document.addEventListener('mouseout', e => {
                const from = e.target.closest('.pc-tile[data-pc-id], #pc-popover');
                if (!from) return;
                const to = e.relatedTarget;
                if (to && (to.closest && (to.closest('#pc-popover') || to.closest('.pc-tile[data-pc-id]') === from))) return;
                clearTimeout(showTimer);
                ocultarPopover();
            });
            window.addEventListener('scroll', () => ocultarPopover(true), { passive: true });
        }

        document.addEventListener('keydown', e => {
            if (e.key === 'Escape') { ocultarPopover(true); MetalNetUI.closeTop(); }
        });

        function actualizarVisualPc(pcId, nuevoEstado) {
            const p = PCS[pcId];
            if (p) {
                p.estado = nuevoEstado;
            }
            document.querySelectorAll('.pc-tile[data-pc-id="' + pcId + '"]').forEach(tile => {
                tile.dataset.estado = nuevoEstado;
                tile.setAttribute('data-estado', nuevoEstado);
                const stText = tile.querySelector('.pc-tile__status');
                if (stText) stText.textContent = LABEL[nuevoEstado] || nuevoEstado;
                const led = tile.querySelector('.pc-tile__led');
                if (led) {
                    led.style.background = nuevoEstado === 'disponible' ? '#94ECBE' : (nuevoEstado === 'mantenimiento' ? '#EF4444' : '#F59E0B');
                }
            });
            window.dispatchEvent(new CustomEvent('metalnet:pc-updated', { detail: { pcId, estado: nuevoEstado } }));
        }

        // Interceptar form-staff-pc-estado con AJAX reactivo y toast
        const staffForm = $('form-staff-pc-estado');
        if (staffForm) {
            staffForm.addEventListener('submit', async function (e) {
                e.preventDefault();
                const formData = new FormData(this);
                const pcId = formData.get('estacion_id');
                const nuevoEstado = formData.get('estado');
                try {
                    const res = await fetch(this.action, {
                        method: 'POST',
                        body: formData,
                        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
                    });
                    const data = await res.json();
                    if (data.success) {
                        actualizarVisualPc(pcId, nuevoEstado);
                        MetalNetUI.toast(data.message || 'Estado de estación actualizado correctamente.', 'ok');
                        MetalNetUI.closeModal('pc-detail-modal');
                    } else {
                        MetalNetUI.toast(data.message || 'Error al actualizar estación.', 'error');
                    }
                } catch (err) {
                    this.submit();
                }
            });
        }

        // Interceptar form-incidencia-rapida con AJAX reactivo y toast
        const incForm = $('form-incidencia-rapida');
        if (incForm) {
            incForm.addEventListener('submit', async function (e) {
                e.preventDefault();
                const formData = new FormData(this);
                const salaCode = formData.get('sala') || 'L-01';
                const pcCode = formData.get('pc') || 'PC-01';
                const labFound = LABS.find(l => l.codigo === salaCode);
                const pcIdTarget = (labFound ? ('LAB' + labFound.id) : 'LAB1') + '-' + pcCode.replace('-', '');
                try {
                    const res = await fetch(this.action, {
                        method: 'POST',
                        body: formData,
                        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
                    });
                    const data = await res.json();
                    if (data.success) {
                        actualizarVisualPc(pcIdTarget, 'mantenimiento');
                        MetalNetUI.toast(data.message || 'Reporte de falla enviado exitosamente. El equipo de soporte atenderá la incidencia a la brevedad.', 'ok');
                        MetalNetUI.closeModal('modal-incidencia-rapida');
                        MetalNetUI.closeModal('pc-detail-modal');
                    } else {
                        MetalNetUI.toast(data.message || 'Error al enviar reporte de falla.', 'error');
                    }
                } catch (err) {
                    this.submit();
                }
            });
        }

        // Interceptar form-reserva-rapida con AJAX reactivo y toast
        const resForm = $('form-reserva-rapida');
        if (resForm) {
            resForm.addEventListener('submit', async function (e) {
                e.preventDefault();
                const formData = new FormData(this);
                const pcId = formData.get('estacion_id');
                try {
                    const res = await fetch(this.action, {
                        method: 'POST',
                        body: formData,
                        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
                    });
                    const data = await res.json();
                    if (data.success) {
                        actualizarVisualPc(pcId, 'ocupado');
                        MetalNetUI.toast(data.message || 'Estación apartada exitosamente.', 'ok');
                        MetalNetUI.closeModal('modal-reserva-rapida');
                        MetalNetUI.closeModal('pc-detail-modal');
                    } else {
                        MetalNetUI.toast(data.message || 'No fue posible apartar la estación.', 'error');
                    }
                } catch (err) {
                    this.submit();
                }
            });
        }

        return { abrirFicha, abrirMapa, filtrar, LABS, PCS, LABEL, actualizarVisualPc };
    })();
</script>
