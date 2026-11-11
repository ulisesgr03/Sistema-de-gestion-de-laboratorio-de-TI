@extends('layouts.private')

@section('title', 'Consola de Administración Central · ' . config('app.institution_name', 'MetalNet'))

@section('content')
@php
    $labsList = $laboratorios ?? $bmLabs ?? [];
    $solicitudesList = $solicitudes ?? collect();
    $incidenciasList = $incidenciasAbiertas ?? collect();
    $usuariosList = $usuarios ?? collect();
    $rolesList = $roles ?? collect();
@endphp

<div class="space-y-6">
    {{-- Cabecera Ejecutiva del Administrador Central (Paleta Oficial Stitch) --}}
    <div class="p-5 rounded-2xl border border-surface-container-highest flex flex-wrap items-center justify-between gap-4 bg-surface-container-lowest shadow-sm">
        <div class="flex items-center gap-3.5">
            <div class="w-12 h-12 rounded-2xl border border-[#b3f1be] flex items-center justify-center bg-[#e8f5ec] text-primary">
                <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <span class="px-2.5 py-0.5 rounded-md border border-[#b3f1be] text-[10px] font-extrabold uppercase bg-[#e8f5ec] text-[#16512c]">
                        Rol: Administrador Central
                    </span>
                    <span class="text-xs font-mono text-outline">{{ $usuario->email ?? 'admin@metalnet.edu' }}</span>
                </div>
                <h1 class="text-xl font-black mt-0.5 text-on-surface">
                    Consola Central MetalNet (10 Labs · 200 Estaciones)
                </h1>
            </div>
        </div>

        {{-- KPIs Globales con los 3 Colores Semánticos --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 text-center font-mono">
            <div class="px-3.5 py-2 rounded-xl border border-surface-container-highest bg-surface-container-low">
                <span class="text-lg font-black block leading-none text-on-surface">{{ $totales['labs'] ?? 10 }}</span>
                <span class="text-[9px] font-sans font-bold uppercase text-outline">Labs</span>
            </div>
            <div class="px-3.5 py-2 rounded-xl border border-[#b3f1be] bg-[#e8f5ec]">
                <span class="text-lg font-black block leading-none text-[#16512c]">{{ $totales['disponible'] ?? 88 }}</span>
                <span class="text-[9px] font-sans font-bold uppercase text-[#16512c]">Disponibles</span>
            </div>
            <div class="px-3.5 py-2 rounded-xl border border-[#fde68a] bg-[#fef3c7]">
                <span class="text-lg font-black block leading-none text-[#92400e]">{{ $totales['ocupado'] ?? 96 }}</span>
                <span class="text-[9px] font-sans font-bold uppercase text-[#92400e]">Ocupadas</span>
            </div>
            <div class="px-3.5 py-2 rounded-xl border border-[#fca5a5] bg-[#ffdad6]">
                <span class="text-lg font-black block leading-none text-[#991b1b]">{{ $totales['mantenimiento'] ?? 16 }}</span>
                <span class="text-[9px] font-sans font-bold uppercase text-[#991b1b]">No disp.</span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        {{-- Columna Izquierda (7 cols): Consola Interactiva de Estaciones + Inventario Físico --}}
        <div class="lg:col-span-7 space-y-6">
            {{-- Gestión de Estaciones (01 a 20) --}}
            <div class="p-5 rounded-2xl border border-surface-container-highest bg-surface-container-lowest shadow-sm">
                <div class="flex flex-wrap items-center justify-between gap-2 pb-3 mb-4 border-b border-surface-container-highest">
                    <div>
                        <span class="text-[10px] font-extrabold uppercase tracking-wider block text-primary">Mantenimiento y Control Operativo</span>
                        <h2 class="text-base font-extrabold text-on-surface">Consola Interactiva para Gestionar y Reparar Estaciones</h2>
                    </div>
                </div>

                {{-- Formulario directo de cambio de estado + bitácora técnica --}}
                <form method="POST" action="{{ route('panel.pc-estado') }}"
                      class="p-4 rounded-xl border border-surface-container-highest mb-4 space-y-3 bg-surface-container-low">
                    @csrf
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <span class="text-xs font-extrabold uppercase tracking-wider text-primary">Cambio Rápido de Estado con Bitácora Técnica</span>
                        <span class="text-[11px] text-on-surface-variant">Actualiza en 1 clic el estado en la base de datos</span>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5">
                        <div>
                            <label class="block text-[10px] font-bold uppercase mb-1 text-on-surface-variant">Estación (ej. LAB1-PC10)</label>
                            <input type="text" name="estacion_id" id="admin-quick-pc" required value="LAB1-PC10"
                                   class="w-full h-9 px-3 rounded-lg border border-surface-container-highest text-xs font-mono font-extrabold bg-surface-container-lowest text-on-surface focus:outline-none focus:border-primary">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold uppercase mb-1 text-on-surface-variant">Nuevo Estado</label>
                            <select name="estado"
                                    class="w-full h-9 px-2.5 rounded-lg border border-surface-container-highest text-xs font-bold bg-surface-container-lowest text-on-surface focus:outline-none focus:border-primary">
                                <option value="disponible">Disponible (Reparada / Operativa)</option>
                                <option value="ocupado">Ocupado (En Sesión)</option>
                                <option value="mantenimiento">No disponible (En Avería)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold uppercase mb-1 text-on-surface-variant">Nota Técnica</label>
                            <input type="text" name="nota" placeholder="Ej. Cable Ethernet reemplazado"
                                   class="w-full h-9 px-3 rounded-lg border border-surface-container-highest text-xs bg-surface-container-lowest text-on-surface focus:outline-none focus:border-primary">
                        </div>
                    </div>
                    <div class="flex justify-end">
                        <button type="submit"
                                class="px-4 py-2 rounded-lg text-white text-xs font-extrabold cursor-pointer transition hover:bg-primary-container bg-primary">
                            Actualizar Estado y Guardar Bitácora
                        </button>
                    </div>
                </form>

                {{-- Pestañas de los 10 Laboratorios --}}
                <div class="flex items-center gap-1.5 overflow-x-auto pb-2 mb-3">
                    @foreach($labsList as $i => $labItem)
                        <button type="button"
                                onclick="window.switchLabTab('admin-lab-tabs', 'adm-lab-{{ $labItem['id'] }}', this)"
                                class="adm-tab-btn px-3 py-1.5 rounded-lg border text-xs font-bold flex items-center gap-1.5 shrink-0 cursor-pointer transition {{ $i === 0 ? 'bg-primary text-white border-primary' : 'bg-surface-container-low text-on-surface-variant border-surface-container-highest hover:bg-surface-container hover:text-on-surface' }}">
                            <span class="font-mono">{{ $labItem['codigo'] }}</span>
                            <span class="text-[10px] font-mono {{ $i === 0 ? 'text-[#b3f1be]' : 'text-primary' }}">{{ $labItem['conteo']['disponible'] }}L</span>
                        </button>
                    @endforeach
                </div>

                {{-- Cuadrícula de Estaciones por Pestaña --}}
                <div id="admin-lab-tabs">
                    @foreach($labsList as $i => $labItem)
                        <div data-tab-panel="adm-lab-{{ $labItem['id'] }}" class="{{ $i === 0 ? '' : 'hidden' }} space-y-3">
                            <div class="p-3 rounded-xl border border-surface-container-highest flex flex-wrap items-center justify-between gap-2 text-xs bg-surface-container-low">
                                <div>
                                    <span class="font-mono font-bold text-on-surface">{{ $labItem['codigo'] }} · {{ $labItem['nombre_corto'] }}</span>
                                    <span class="text-on-surface-variant ml-2">{{ $labItem['planta'] }} · {{ $labItem['canon'] ? 'Cañón Activo' : 'Sin Cañón' }}</span>
                                </div>
                                <div class="flex items-center gap-2 font-mono text-[11px]">
                                    <span class="text-[#16512c] font-bold">{{ $labItem['conteo']['disponible'] }} Disp.</span>
                                    <span class="text-[#92400e] font-bold">{{ $labItem['conteo']['ocupado'] }} Ocup.</span>
                                    <span class="text-[#991b1b] font-bold">{{ $labItem['conteo']['mantenimiento'] }} No disp.</span>
                                </div>
                            </div>

                            <div class="p-4 rounded-xl border border-surface-container-highest bg-surface-container-lowest">
                                @include('partials.pc-grid', ['lab' => $labItem, 'variant' => 'full'])
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Inventario Físico de Hardware e Instrumental --}}
            <div class="p-5 rounded-2xl border border-surface-container-highest bg-surface-container-lowest shadow-sm">
                <div class="flex items-center justify-between pb-3 mb-3.5 border-b border-surface-container-highest">
                    <div>
                        <span class="text-[10px] font-extrabold uppercase tracking-wider block text-primary">Almacén Central e Instrumental</span>
                        <h3 class="text-base font-extrabold text-on-surface">Inventario Físico de Hardware, Proyectores y Periféricos</h3>
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                    @php
                        $inventarioFisico = [
                            ['codigo' => 'CAÑON-4K-01', 'nombre' => 'Proyector Láser 4K Epson EB-L730U', 'ubicacion' => 'Resguardo Almacén E-102', 'disponibles' => 3, 'total' => 6],
                            ['codigo' => 'SW-CISCO-C9200', 'nombre' => 'Switch Cisco Catalyst 9200 24P PoE+', 'ubicacion' => 'Rack de Repuesto IDF-02', 'disponibles' => 2, 'total' => 4],
                            ['codigo' => 'OSC-RIGOL-1054', 'nombre' => 'Osciloscopio Digital Rigol DS1054Z 50MHz', 'ubicacion' => 'Laboratorio L-05 · IoT', 'disponibles' => 8, 'total' => 10],
                            ['codigo' => 'VR-QUEST3-01', 'nombre' => 'Visor Meta Quest 3 512GB con Link Cable', 'ubicacion' => 'Laboratorio L-09 · VR/3D', 'disponibles' => 6, 'total' => 8],
                            ['codigo' => 'PLC-SIEMENS-S7', 'nombre' => 'Kit Didáctico PLC Siemens S7-1200', 'ubicacion' => 'Laboratorio L-10 · Robótica', 'disponibles' => 5, 'total' => 6],
                            ['codigo' => 'SSD-NVME-2TB', 'nombre' => 'Unidad SSD NVMe PCIe 4.0 2TB Kingston', 'ubicacion' => 'Cajón Técnico Mantenimiento', 'disponibles' => 12, 'total' => 15],
                        ];
                    @endphp
                    @foreach($inventarioFisico as $item)
                        <div class="p-3 rounded-xl border border-surface-container-highest flex items-center justify-between gap-2 bg-surface-container-low">
                            <div class="min-w-0">
                                <span class="font-mono text-[10px] font-bold text-primary">{{ $item['codigo'] }}</span>
                                <p class="font-bold truncate text-on-surface">{{ $item['nombre'] }}</p>
                                <span class="text-[11px] text-on-surface-variant">{{ $item['ubicacion'] }}</span>
                            </div>
                            <div class="text-right shrink-0">
                                <span class="text-sm font-mono font-extrabold block text-primary">{{ $item['disponibles'] }}/{{ $item['total'] }}</span>
                                <span class="text-[10px] text-outline">Stock</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Columna Derecha (5 cols): Solicitudes, Averías con 1 Clic y Gestión de Roles --}}
        <div class="lg:col-span-5 space-y-6">
            {{-- Bandeja de Solicitudes Pendientes (Software y Hardware) --}}
            <div class="p-5 rounded-2xl border border-surface-container-highest bg-surface-container-lowest shadow-sm">
                <div class="flex items-center justify-between pb-3 mb-3.5 border-b border-surface-container-highest">
                    <div>
                        <span class="text-[10px] font-extrabold uppercase tracking-wider block text-primary">Bandeja de Autorizaciones</span>
                        <h3 class="text-base font-extrabold text-on-surface">Solicitudes de Software y Hardware</h3>
                    </div>
                    <span class="px-2 py-0.5 rounded border border-[#b3f1be] font-mono text-xs font-bold bg-[#e8f5ec] text-[#16512c]">
                        {{ $solicitudesList->where('estado', 'pendiente')->count() }}
                    </span>
                </div>

                <div class="space-y-3 max-h-80 overflow-y-auto">
                    @forelse($solicitudesList as $sol)
                        <div class="p-3.5 rounded-xl border border-surface-container-highest space-y-2 text-xs bg-surface-container-low">
                            <div class="flex items-start justify-between gap-2">
                                <div>
                                    <span class="font-mono text-[10px] font-bold text-primary">{{ $sol->folio }} · {{ strtoupper($sol->tipo) }}</span>
                                    <p class="font-bold mt-0.5 text-on-surface">{{ $sol->titulo }}</p>
                                    <p class="text-[11px] text-on-surface-variant">{{ $sol->descripcion }}</p>
                                    <span class="text-[10px] block mt-1 text-outline">Solicitado por: {{ $sol->usuario?->name ?? 'Usuario' }} ({{ $sol->usuario?->primaryRoleLabel() ?? 'Alumno' }})</span>
                                </div>
                                <span class="px-2 py-0.5 rounded border text-[10px] font-extrabold uppercase {{ $sol->estado === 'aprobado' ? 'border-[#b3f1be] bg-[#e8f5ec] text-[#16512c]' : ($sol->estado === 'rechazado' ? 'border-[#fca5a5] bg-[#ffdad6] text-[#991b1b]' : 'border-[#fde68a] bg-[#fef3c7] text-[#92400e]') }}">
                                    {{ $sol->estado }}
                                </span>
                            </div>

                            @if($sol->estado === 'pendiente')
                                <div class="flex items-center justify-end gap-2 pt-1.5 border-t border-surface-container-highest">
                                    <form method="POST" action="{{ route('panel.solicitud-resolver') }}">
                                        @csrf
                                        <input type="hidden" name="proceso_id" value="{{ $sol->id }}">
                                        <input type="hidden" name="accion" value="aprobar">
                                        <button type="submit"
                                                class="px-3 py-1.5 rounded-lg text-white font-extrabold text-[11px] cursor-pointer transition hover:bg-primary-container bg-primary">
                                            Aprobar Solicitud
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('panel.solicitud-resolver') }}">
                                        @csrf
                                        <input type="hidden" name="proceso_id" value="{{ $sol->id }}">
                                        <input type="hidden" name="accion" value="rechazar">
                                        <button type="submit"
                                                class="px-3 py-1.5 rounded-lg border border-[#fca5a5] font-bold text-[11px] cursor-pointer transition bg-[#ffdad6] text-[#991b1b] hover:bg-[#ba1a1a] hover:text-white">
                                            Rechazar
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    @empty
                        <p class="text-xs text-outline">No hay solicitudes pendientes.</p>
                    @endforelse
                </div>
            </div>

            {{-- Tickets de Avería Activos con Reparación en 1 Clic --}}
            <div class="p-5 rounded-2xl border border-surface-container-highest bg-surface-container-lowest shadow-sm">
                <div class="flex items-center justify-between pb-3 mb-3.5 border-b border-surface-container-highest">
                    <div>
                        <span class="text-[10px] font-extrabold uppercase tracking-wider block text-[#991b1b]">Mesa de Averías</span>
                        <h3 class="text-base font-extrabold text-on-surface">Estaciones No Disponibles / En Reparación</h3>
                    </div>
                    <span class="px-2.5 py-0.5 rounded border border-[#fca5a5] font-mono text-xs font-extrabold bg-[#ffdad6] text-[#991b1b]">
                        {{ $incidenciasList->count() }}
                    </span>
                </div>

                <div class="space-y-2.5 max-h-80 overflow-y-auto">
                    @forelse($incidenciasList as $inc)
                        @php
                            $labNum = $inc->laboratorio_id ?? 1;
                            $pcCode = $inc->estacion?->codigo ?? 'PC-01';
                            $pcNum = (int) preg_replace('/\D+/', '', $pcCode) ?: 1;
                            $pcId = sprintf('LAB%d-PC%02d', $labNum, $pcNum);
                        @endphp
                        <div class="p-3.5 rounded-xl border border-[#fca5a5] space-y-2 text-xs bg-[#fff8f7]">
                            <div class="flex items-start justify-between gap-2">
                                <div>
                                    <span class="font-mono font-extrabold text-[#991b1b]">{{ $inc->laboratorio?->codigo ?? 'L-01' }} · {{ $pcCode }}</span>
                                    <p class="font-bold mt-0.5 text-on-surface">{{ $inc->titulo }}</p>
                                    <p class="text-[11px] text-on-surface-variant">{{ $inc->descripcion }}</p>
                                </div>
                                <span class="px-2 py-0.5 rounded border border-[#fca5a5] text-[10px] font-extrabold uppercase bg-[#ffdad6] text-[#991b1b]">
                                    {{ $inc->severidad }}
                                </span>
                            </div>
                            <form method="POST" action="{{ route('panel.pc-estado') }}" class="flex items-center justify-end gap-2 pt-1">
                                @csrf
                                <input type="hidden" name="estacion_id" value="{{ $pcId }}">
                                <input type="hidden" name="laboratorio_id" value="{{ $labNum }}">
                                <input type="hidden" name="nombre" value="{{ $pcCode }}">
                                <input type="hidden" name="estado" value="disponible">
                                <input type="hidden" name="nota" value="Avería reparada y validada en 1 clic por Administrador">
                                <button type="submit"
                                        class="px-3.5 py-1.5 rounded-lg text-white font-extrabold text-[11px] cursor-pointer transition hover:bg-primary-container bg-primary">
                                    Marcar como reparada y disponible (1 Clic)
                                </button>
                            </form>
                        </div>
                    @empty
                        <p class="text-xs text-outline">Todas las estaciones están operativas sin incidencias.</p>
                    @endforelse
                </div>
            </div>

            {{-- Control Institucional de Usuarios y Roles (3 Roles Únicos: Admin, Docente, Alumno) --}}
            <div class="p-5 rounded-2xl border border-surface-container-highest bg-surface-container-lowest shadow-sm">
                <div class="flex items-center justify-between pb-3 mb-3.5 border-b border-surface-container-highest">
                    <div>
                        <span class="text-[10px] font-extrabold uppercase tracking-wider block text-primary">Cuentas Institucionales</span>
                        <h3 class="text-base font-extrabold text-on-surface">Administración de Perfiles y Roles (3 Roles)</h3>
                    </div>
                </div>

                <div class="space-y-2.5 max-h-60 overflow-y-auto">
                    @foreach($usuariosList as $usr)
                        <div class="p-3 rounded-xl border border-surface-container-highest flex items-center justify-between gap-3 text-xs bg-surface-container-low">
                            <div class="min-w-0">
                                <p class="font-bold truncate text-on-surface">{{ $usr->name }}</p>
                                <span class="font-mono text-[11px] text-outline">{{ $usr->email }} · {{ $usr->matricula }}</span>
                            </div>
                            <form method="POST" action="{{ route('panel.usuario-rol') }}" class="flex items-center gap-1.5 shrink-0">
                                @csrf
                                <input type="hidden" name="user_id" value="{{ $usr->id }}">
                                <select name="role_slug" onchange="this.form.submit()"
                                        class="h-8 px-2 rounded-lg border border-surface-container-highest bg-surface-container-lowest text-xs font-bold text-on-surface focus:outline-none focus:border-primary">
                                    <option value="admin" {{ $usr->isAdmin() ? 'selected' : '' }}>Admin</option>
                                    <option value="docente" {{ $usr->isDocente() ? 'selected' : '' }}>Docente</option>
                                    <option value="alumno" {{ $usr->isAlumno() ? 'selected' : '' }}>Alumno</option>
                                </select>
                            </form>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

@include('partials.bm-interactivo')

<script>
window.switchLabTab = function(containerId, panelKey, btnEl) {
    const container = document.getElementById(containerId);
    if (!container) return;
    container.querySelectorAll('[data-tab-panel]').forEach(p => {
        p.classList.toggle('hidden', p.getAttribute('data-tab-panel') !== panelKey);
    });
    if (btnEl && btnEl.parentElement) {
        btnEl.parentElement.querySelectorAll('button').forEach(b => {
            const isTarget = (b === btnEl);
            b.className = 'adm-tab-btn px-3 py-1.5 rounded-lg border text-xs font-bold flex items-center gap-1.5 shrink-0 cursor-pointer transition ' +
                (isTarget ? 'bg-primary text-white border-primary' : 'bg-surface-container-low text-on-surface-variant border-surface-container-highest hover:bg-surface-container hover:text-on-surface');
        });
    }
};
</script>
@endsection
