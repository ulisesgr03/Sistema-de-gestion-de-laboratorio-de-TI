@extends('layouts.public')

@section('title', 'Catálogo de Software e Instrumental · ' . config('app.institution_name', 'MetalNet'))

@section('content')
@php
    $catalogoList = $catalogo ?? [];
    $categoriasList = $categorias ?? [];
@endphp

{{-- ── ENCABEZADO EDITORIAL METALNET (PALETA OFICIAL STITCH) ─────────────────── --}}
<div class="border border-surface-container-highest bg-surface-container-lowest rounded-2xl mb-6 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 mb-2">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded text-[10px] font-bold uppercase tracking-widest border border-[#b3f1be] bg-[#e8f5ec] text-[#16512c]">
                        Homologación de Entornos Técnicos
                    </span>
                    <span class="text-[11px] font-semibold uppercase tracking-widest text-outline">
                        {{ config('app.institution_full', 'MetalNet · Laboratorios de Ingeniería en Sistemas') }}
                    </span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-on-surface">
                    Catálogo de Software Licenciado y Open Source
                </h1>
                <p class="text-xs sm:text-sm mt-1 max-w-2xl text-on-surface-variant">
                    Consulta las suites de desarrollo, redes, simulación y análisis instaladas en los 10 laboratorios o solicita despliegues adicionales al administrador.
                </p>
            </div>

            <button type="button"
                    onclick="window.SoftwareCatalogo.abrirModalSolicitud('')"
                    class="px-4 py-2.5 rounded-xl text-white font-black text-xs inline-flex items-center gap-2 cursor-pointer transition hover:bg-primary-container shadow-xs bg-primary">
                <svg class="w-4 h-4 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" x2="12" y1="15" y2="3"/></svg>
                Solicitar Software / Actualización
            </button>
        </div>
    </div>
</div>

<section class="max-w-7xl mx-auto pb-12">

    {{-- ── BARRA DE FILTROS Y BUSCADOR ────────────────────────────────────── --}}
    <div class="rounded-2xl border border-surface-container-highest p-4 sm:p-5 mb-6 bg-surface-container-lowest shadow-sm">
        <div class="flex flex-wrap items-center justify-between gap-3">
            {{-- Filtros por Categoría --}}
            <div class="flex flex-wrap items-center gap-1.5">
                <a href="{{ route('software.index', ['q' => $busqueda]) }}"
                   class="px-3.5 py-1.5 rounded-xl text-xs font-extrabold border transition {{ empty($categoria) ? 'bg-primary text-white border-primary shadow-xs' : 'bg-surface-container-low text-on-surface-variant border-surface-container-highest hover:bg-surface-container hover:text-on-surface' }}">
                    Todos ({{ count($catalogoList) }})
                </a>
                @foreach($categoriasList as $cat)
                    <a href="{{ route('software.index', ['categoria' => $cat, 'q' => $busqueda]) }}"
                       class="px-3.5 py-1.5 rounded-xl text-xs font-bold border transition {{ strcasecmp($categoria ?? '', $cat) === 0 ? 'bg-primary text-white border-primary shadow-xs' : 'bg-surface-container-low text-on-surface-variant border-surface-container-highest hover:bg-surface-container hover:text-on-surface' }}">
                        {{ $cat }}
                    </a>
                @endforeach
            </div>

            {{-- Buscador --}}
            <form method="GET" action="{{ route('software.index') }}" class="flex items-center gap-2 w-full sm:w-auto">
                @if(!empty($categoria))
                    <input type="hidden" name="categoria" value="{{ $categoria }}">
                @endif
                <input type="text"
                       name="q"
                       value="{{ $busqueda ?? '' }}"
                       placeholder="Buscar aplicación, módulo o sala..."
                       class="h-9 flex-1 sm:w-64 px-3.5 rounded-xl border border-outline-variant bg-surface-container-low text-xs font-semibold text-on-surface focus:outline-none focus:border-primary focus:bg-surface-container-lowest transition-colors">
                <button type="submit"
                        class="h-9 px-4 text-white font-black rounded-xl text-xs cursor-pointer transition hover:bg-primary-container bg-primary shadow-xs">
                    Buscar
                </button>
            </form>
        </div>
    </div>

    {{-- ── CUADRÍCULA DE TARJETAS INTERACTIVAS DE SOFTWARE ────────────────── --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
        @foreach($catalogoList as $idx => $sw)
            @php
                $esActualizado = ($sw['estado_version'] ?? 'Actualizado') === 'Actualizado';
            @endphp
            <article onclick="window.SoftwareCatalogo.abrirDetalle({{ $idx }})"
                     class="group rounded-2xl border border-surface-container-highest p-5 flex flex-col justify-between transition cursor-pointer hover:border-primary bg-surface-container-lowest shadow-sm hover:shadow-md">
                <div>
                    <div class="flex items-start justify-between gap-3 mb-3">
                        <div class="flex items-center gap-3.5 min-w-0">
                            <div class="w-12 h-12 rounded-xl border border-surface-container-highest p-2 flex items-center justify-center shrink-0 bg-surface-container-low">
                                <img src="{{ $sw['logo'] ?? asset('images/software/vscode.svg') }}"
                                     alt="{{ $sw['nombre'] }}"
                                     class="w-full h-full object-contain select-none">
                            </div>
                            <div class="min-w-0">
                                <h2 class="text-base font-extrabold truncate text-on-surface">{{ $sw['nombre'] }}</h2>
                                <span class="text-xs font-semibold block truncate text-outline">
                                    {{ $sw['categoria'] }} · {{ $sw['fabricante'] }}
                                </span>
                            </div>
                        </div>

                        @if($esActualizado)
                            <span class="px-2.5 py-0.5 rounded-md border border-[#b3f1be] text-[10px] font-extrabold shrink-0 bg-[#e8f5ec] text-[#16512c]">
                                Actualizado
                            </span>
                        @else
                            <span class="px-2.5 py-0.5 rounded-md border border-[#fde68a] text-[10px] font-extrabold shrink-0 bg-[#fef3c7] text-[#92400e]">
                                Update disp.
                            </span>
                        @endif
                    </div>

                    <div class="grid grid-cols-2 gap-2 my-3 p-2.5 rounded-xl border border-surface-container-highest text-xs bg-surface-container-low">
                        <div>
                            <span class="text-[10px] font-bold uppercase block text-outline">En Labs</span>
                            <span class="font-mono font-extrabold text-on-surface">v{{ $sw['version'] }}</span>
                        </div>
                        <div>
                            <span class="text-[10px] font-bold uppercase block text-outline">Oficial Reciente</span>
                            <span class="font-mono font-extrabold {{ $esActualizado ? 'text-[#16512c]' : 'text-status-occupied' }}">v{{ $sw['version_oficial'] ?? $sw['version'] }}</span>
                        </div>
                    </div>

                    <p class="text-xs line-clamp-2 mb-3 text-on-surface-variant">
                        {{ $sw['descripcion'] }}
                    </p>
                </div>

                <div class="pt-3 border-t border-surface-container-highest flex items-center justify-between gap-2">
                    <div class="flex flex-wrap items-center gap-1" onclick="event.stopPropagation();">
                        <span class="text-[10px] font-bold uppercase mr-1 text-outline">Salas:</span>
                        @foreach($sw['labs'] as $labCode)
                            <a href="{{ route('public.salas') }}"
                               class="px-2 py-0.5 rounded border border-surface-container-highest text-[10.5px] font-mono font-bold transition hover:bg-surface-container bg-surface-container-low text-primary">
                                {{ $labCode }}
                            </a>
                        @endforeach
                    </div>

                    <span class="text-[11px] font-bold group-hover:underline inline-flex items-center gap-1 shrink-0 text-primary">
                        Detalles
                        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                    </span>
                </div>
            </article>
        @endforeach
    </div>
</section>

{{-- ── MODAL INTERACTIVO DE VERSIÓN MÁS RECIENTE Y DETALLES TÉCNICOS ──────── --}}
<div id="modal-detalle-software" class="fixed inset-0 z-50 hidden items-center justify-center p-4 bg-[#1b1c18]/60 backdrop-blur-sm">
    <div class="w-full max-w-2xl rounded-2xl border border-surface-container-highest overflow-hidden shadow-2xl bg-surface-container-lowest text-on-surface">
        <div class="px-6 py-4 border-b border-surface-container-highest flex items-center justify-between gap-3 bg-surface-container-low">
            <div class="flex items-center gap-3.5 min-w-0">
                <div class="w-14 h-14 rounded-xl border border-surface-container-highest p-2 flex items-center justify-center shrink-0 bg-surface-container-lowest shadow-xs">
                    <img id="md-sw-logo" src="{{ asset('images/software/vscode.svg') }}" alt="Logo Software" class="w-full h-full object-contain">
                </div>
                <div class="min-w-0">
                    <div class="flex flex-wrap items-center gap-2">
                        <h3 class="text-lg font-extrabold text-on-surface" id="md-sw-nombre">Visual Studio Code</h3>
                        <span id="md-sw-badge" class="px-2.5 py-0.5 rounded-md text-xs font-extrabold border border-[#b3f1be] bg-[#e8f5ec] text-[#16512c]">Actualizado</span>
                    </div>
                    <span class="text-xs block mt-0.5 text-outline" id="md-sw-meta">Desarrollo · Microsoft · Open Source MIT</span>
                </div>
            </div>
            <button type="button" onclick="window.SoftwareCatalogo.closeModal('modal-detalle-software')"
                    class="w-8 h-8 rounded-lg border border-surface-container-highest flex items-center justify-center shrink-0 cursor-pointer bg-surface-container text-on-surface-variant hover:text-on-surface">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" x2="6" y1="6" y2="18"/><line x1="6" x2="18" y1="6" y2="18"/></svg>
            </button>
        </div>

        <div class="px-6 py-5 space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 p-3.5 rounded-xl border border-surface-container-highest bg-surface-container-low">
                <div class="p-2.5 rounded-lg border border-surface-container-highest bg-surface-container-lowest">
                    <span class="text-[10px] font-bold uppercase block text-outline">Versión en Labs</span>
                    <span class="text-base font-mono font-extrabold mt-0.5 block text-on-surface" id="md-sw-ver-inst">v1.97</span>
                </div>
                <div class="p-2.5 rounded-lg border border-surface-container-highest bg-surface-container-lowest">
                    <span class="text-[10px] font-bold uppercase block text-outline">Versión Oficial Reciente</span>
                    <span class="text-base font-mono font-extrabold mt-0.5 block text-primary" id="md-sw-ver-ofic">v1.97.2</span>
                </div>
                <div class="p-2.5 rounded-lg border border-surface-container-highest bg-surface-container-lowest">
                    <span class="text-[10px] font-bold uppercase block text-outline">Tipo de Licencia</span>
                    <span class="text-xs font-extrabold mt-1 block text-primary" id="md-sw-licencia">Open Source</span>
                </div>
            </div>

            <p class="text-xs leading-relaxed text-on-surface-variant" id="md-sw-desc"></p>

            <div class="rounded-xl border border-surface-container-highest overflow-hidden bg-surface-container-lowest">
                <div class="px-4 py-2.5 border-b border-surface-container-highest text-xs font-bold uppercase tracking-wider flex items-center gap-2 bg-surface-container-low text-on-surface">
                    <svg class="w-4 h-4 text-primary" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="4" y="4" width="16" height="16" rx="2"/><rect x="9" y="9" width="6" height="6"/></svg>
                    Requisitos de Hardware Recomendados
                </div>
                <div class="p-3.5 grid grid-cols-2 sm:grid-cols-4 gap-2.5 text-xs" id="md-sw-req-grid"></div>
            </div>

            <div class="rounded-xl border border-surface-container-highest overflow-hidden bg-surface-container-lowest">
                <div class="px-4 py-2.5 border-b border-surface-container-highest text-xs font-bold uppercase tracking-wider flex items-center gap-2 bg-surface-container-low text-on-surface">
                    <svg class="w-4 h-4 text-primary" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5"/><path d="M2 12l10 5 10-5"/></svg>
                    Módulos y Extensiones Incluidas
                </div>
                <div class="p-3.5 flex flex-wrap gap-1.5" id="md-sw-modulos"></div>
            </div>

            <div class="p-3.5 rounded-xl border border-surface-container-highest flex flex-wrap items-center justify-between gap-2 bg-surface-container-low">
                <span class="text-xs font-bold text-outline">Laboratorios con este paquete:</span>
                <div class="flex flex-wrap gap-1.5" id="md-sw-labs"></div>
            </div>
        </div>

        <div class="px-6 py-4 border-t border-surface-container-highest flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 bg-surface-container-low">
            <span class="text-[11px] text-outline">¿Requieres este paquete en otro laboratorio o actualizar versión?</span>
            <button type="button"
                    id="md-sw-btn-solicitar"
                    class="px-5 py-2.5 rounded-xl text-white font-black text-xs inline-flex items-center justify-center gap-2 cursor-pointer transition hover:bg-primary-container shadow-xs bg-primary">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" x2="12" y1="15" y2="3"/></svg>
                Solicitar Software / Actualización
            </button>
        </div>
    </div>
</div>

{{-- ── MODAL DE SOLICITUD DE SOFTWARE / ACTUALIZACIÓN ────────────────────── --}}
<div id="modal-solicitar-sw" class="fixed inset-0 z-50 hidden items-center justify-center p-4 bg-[#1b1c18]/60 backdrop-blur-sm">
    <div class="w-full max-w-lg rounded-2xl border border-surface-container-highest p-6 shadow-2xl bg-surface-container-lowest text-on-surface">
        <div class="flex items-center justify-between pb-3 border-b border-surface-container-highest">
            <div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider block text-primary">Mesa de Ingeniería · MetalNet</span>
                <h3 class="text-lg font-extrabold text-on-surface">Solicitar Instalación o Actualización de Software</h3>
            </div>
            <button type="button" onclick="window.SoftwareCatalogo.closeModal('modal-solicitar-sw')"
                    class="w-8 h-8 rounded-lg border border-surface-container-highest flex items-center justify-center cursor-pointer bg-surface-container-low text-on-surface-variant hover:text-on-surface">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" x2="6" y1="6" y2="18"/><line x1="6" x2="18" y1="6" y2="18"/></svg>
            </button>
        </div>

        <form method="POST" action="{{ route('software.solicitar') }}" class="mt-4 space-y-3.5">
            @csrf
            <input type="hidden" name="tipo" value="software">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-[11px] font-bold uppercase mb-1 text-outline">Solicitante *</label>
                    <input type="text" name="solicitante" required value="{{ Auth::check() ? Auth::user()->name : '' }}" placeholder="Nombre completo"
                           class="w-full h-10 px-3 rounded-lg border border-outline-variant bg-surface-container-low text-xs text-on-surface focus:outline-none focus:border-primary">
                </div>
                <div>
                    <label class="block text-[11px] font-bold uppercase mb-1 text-outline">Laboratorio Destino *</label>
                    <select name="sala" id="sw-sol-sala"
                            class="w-full h-10 px-3 rounded-lg border border-outline-variant bg-surface-container-low text-xs font-bold text-on-surface focus:outline-none focus:border-primary">
                        @for($i = 1; $i <= 10; $i++)
                            @php $code = 'L-' . str_pad((string)$i, 2, '0', STR_PAD_LEFT); @endphp
                            <option value="{{ $code }}">{{ $code }}</option>
                        @endfor
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-[11px] font-bold uppercase mb-1 text-outline">Software y Versión Requerida *</label>
                <input type="text" name="recurso" id="sw-sol-input" required placeholder="Ej. Docker Desktop v4.38 / Wireshark 4.4"
                       class="w-full h-10 px-3 rounded-lg border border-outline-variant bg-surface-container-low text-xs font-bold text-on-surface focus:outline-none focus:border-primary">
            </div>
            <div>
                <label class="block text-[11px] font-bold uppercase mb-1 text-outline">Justificación Académica *</label>
                <textarea name="descripcion" rows="3" required placeholder="Indica la materia, grupo y prácticas que requieren este paquete..."
                          class="w-full p-3 rounded-lg border border-outline-variant bg-surface-container-low text-xs text-on-surface focus:outline-none focus:border-primary"></textarea>
            </div>
            <div class="pt-2 flex items-center justify-end gap-2">
                <button type="button" onclick="window.SoftwareCatalogo.closeModal('modal-solicitar-sw')"
                        class="px-4 py-2 rounded-lg text-xs font-semibold border border-surface-container-highest bg-surface-container-low text-on-surface-variant hover:bg-surface-container hover:text-on-surface cursor-pointer">
                    Cancelar
                </button>
                <button type="submit"
                        class="px-5 py-2 text-white font-black rounded-lg text-xs cursor-pointer bg-primary hover:bg-primary-container transition shadow-xs">
                    Enviar Solicitud
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    window.SoftwareCatalogo = (function () {
        const ITEMS = @json(array_values($catalogoList));
        const esc = s => String(s ?? '').replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));

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

        function abrirModalSolicitud(nombreSw, labCode = 'L-01') {
            const input = document.getElementById('sw-sol-input');
            const sala = document.getElementById('sw-sol-sala');
            if (input) input.value = nombreSw || '';
            if (sala && labCode) sala.value = labCode;
            openModal('modal-solicitar-sw');
        }

        function abrirDetalle(idx) {
            const sw = ITEMS[idx];
            if (!sw) return;

            document.getElementById('md-sw-logo').src = sw.logo || '';
            document.getElementById('md-sw-nombre').textContent = sw.nombre;
            document.getElementById('md-sw-meta').textContent = `${sw.categoria} · ${sw.fabricante}`;
            document.getElementById('md-sw-ver-inst').textContent = 'v' + sw.version;
            document.getElementById('md-sw-ver-ofic').textContent = 'v' + (sw.version_oficial || sw.version);
            document.getElementById('md-sw-licencia').textContent = sw.licencia;
            document.getElementById('md-sw-desc').textContent = sw.descripcion;

            const isUp = (sw.estado_version || 'Actualizado') === 'Actualizado';
            const badge = document.getElementById('md-sw-badge');
            badge.textContent = isUp ? 'Actualizado' : 'Update disponible';
            badge.className = isUp
                ? 'px-2.5 py-0.5 rounded-md text-xs font-extrabold border border-[#b3f1be] bg-[#e8f5ec] text-[#16512c]'
                : 'px-2.5 py-0.5 rounded-md text-xs font-extrabold border border-[#fde68a] bg-[#fef3c7] text-[#92400e]';

            const req = sw.requisitos || { cpu: 'Multi-Core 64-bit', ram: '16 GB RAM', gpu: 'Integrada / Dedicada', disco: '10 GB SSD' };
            document.getElementById('md-sw-req-grid').innerHTML = [
                ['CPU', req.cpu],
                ['RAM', req.ram],
                ['GPU', req.gpu],
                ['Almacenamiento', req.disco],
            ].map(([k, v]) => (
                '<div class="p-2.5 rounded-lg border border-surface-container-highest bg-surface-container-lowest">' +
                    '<span class="text-[10px] font-bold uppercase block text-outline">' + esc(k) + '</span>' +
                    '<span class="font-semibold mt-0.5 block text-on-surface">' + esc(v) + '</span>' +
                '</div>'
            )).join('');

            const mods = sw.modulos || ['Paquete Base Homologado'];
            document.getElementById('md-sw-modulos').innerHTML = mods.map(m => (
                '<span class="px-2.5 py-1 rounded-lg border border-surface-container-highest text-xs font-medium bg-surface-container text-on-surface">' + esc(m) + '</span>'
            )).join('');

            const labsWrap = document.getElementById('md-sw-labs');
            labsWrap.innerHTML = '';
            (sw.labs || []).forEach(code => {
                const link = document.createElement('a');
                link.href = `{{ route('public.salas') }}`;
                link.className = 'px-3 py-1 rounded-lg border border-surface-container-highest font-mono text-xs font-bold transition hover:bg-surface-container bg-surface-container-lowest text-primary shadow-xs';
                link.textContent = code + ' · Ver equipos';
                labsWrap.appendChild(link);
            });

            document.getElementById('md-sw-btn-solicitar').onclick = () => {
                closeModal('modal-detalle-software');
                abrirModalSolicitud(`${sw.nombre} (v${sw.version_oficial || sw.version})`, (sw.labs && sw.labs[0]) || 'L-01');
            };

            openModal('modal-detalle-software');
        }

        return { abrirDetalle, abrirModalSolicitud, openModal, closeModal };
    })();
</script>
@endsection
