@extends('layouts.private')

@section('title', 'Panel de Encargado de Laboratorio · ' . config('app.institution_name', 'MetalNet'))

@section('content')
@php
    $labsList = $laboratorios ?? $bmLabs ?? [];
    $incidenciasList = $incidenciasAbiertas ?? collect();
    $prestamosList = $prestamosPendientes ?? collect();
@endphp

<div class="space-y-6">
    {{-- Cabecera del Encargado de Laboratorio (Paleta docs/stitch) --}}
    <div class="p-5 rounded-2xl border flex flex-wrap items-center justify-between gap-4"
         style="background: var(--surface-container-lowest); border-color: var(--outline-variant); box-shadow: 0 2px 10px rgba(27, 28, 24, 0.04);">
        <div class="flex items-center gap-3.5">
            <div class="w-12 h-12 rounded-2xl border flex items-center justify-center"
                 style="background: var(--secondary-container); border-color: #8fcb9b; color: var(--on-secondary-container);">
                <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <span class="px-2.5 py-0.5 rounded-md border text-[10px] font-extrabold uppercase"
                          style="background: var(--secondary-container); color: var(--on-secondary-container); border-color: #8fcb9b;">
                        Rol: Encargado de Laboratorio
                    </span>
                    <span class="text-xs font-mono" style="color: var(--tertiary);">{{ $usuario->matricula ?? $usuario->email }}</span>
                </div>
                <h1 class="text-xl font-black mt-0.5" style="color: var(--on-surface);">
                    Control Operativo de Piso e Inventario · {{ $usuario->name }}
                </h1>
            </div>
        </div>

        {{-- Métricas Operativas en Vivo (Verde, Amarillo, Rojo) --}}
        <div class="flex flex-wrap items-center gap-2.5 font-mono text-xs">
            <div class="px-3.5 py-2 rounded-xl border font-extrabold"
                 style="background: #f0fdf4; border-color: #86efac; color: #166534;">
                {{ $totales['disponible'] ?? 88 }} Disponibles
            </div>
            <div class="px-3.5 py-2 rounded-xl border font-extrabold"
                 style="background: #fefce8; border-color: #fde047; color: #854d0e;">
                {{ $totales['ocupado'] ?? 96 }} Ocupadas
            </div>
            <div class="px-3.5 py-2 rounded-xl border font-extrabold"
                 style="background: #fef2f2; border-color: #fca5a5; color: #991b1b;">
                {{ $totales['mantenimiento'] ?? 16 }} No disponibles
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        {{-- Columna Izquierda (7 cols): Control Operativo de los 10 Laboratorios --}}
        <div class="lg:col-span-7 space-y-5">
            <div class="p-5 rounded-2xl border"
                 style="background: var(--surface-container-lowest); border-color: var(--outline-variant); box-shadow: 0 2px 10px rgba(27, 28, 24, 0.04);">
                <div class="flex flex-wrap items-center justify-between gap-2 pb-3 mb-4 border-b"
                     style="border-color: var(--outline-variant);">
                    <div>
                        <span class="text-[10px] font-extrabold uppercase tracking-wider block" style="color: var(--primary);">Supervisión en Sitio · MetalNet</span>
                        <h2 class="text-base font-extrabold" style="color: var(--on-surface);">Estado Operativo y Validación de Entradas/Salidas (10 Salas)</h2>
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

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    @foreach($labsList as $idx => $lab)
                        <div class="p-3.5 rounded-xl border flex flex-col justify-between gap-2.5"
                             style="background: var(--surface-container-low); border-color: var(--outline-variant);">
                            <div class="flex items-start justify-between gap-2">
                                <div>
                                    <div class="flex items-center gap-1.5">
                                        <span class="px-2 py-0.5 rounded border font-mono text-xs font-extrabold"
                                              style="background: var(--surface-container-lowest); color: var(--primary); border-color: var(--outline-variant);">
                                            {{ $lab['codigo'] }}
                                        </span>
                                        <span class="text-xs font-bold truncate" style="color: var(--on-surface);">{{ $lab['nombre_corto'] }}</span>
                                    </div>
                                    <span class="text-[11px] block mt-1" style="color: var(--on-surface-variant);">{{ $lab['docente'] }} · {{ $lab['grupo'] }}</span>
                                </div>
                                <span class="px-2 py-0.5 rounded border text-[10px] font-bold"
                                      style="{{ $lab['canon']
                                          ? 'background: #f0fdf4; color: #166534; border-color: #86efac;'
                                          : 'background: #fef2f2; color: #991b1b; border-color: #fca5a5;' }}">
                                    {{ $lab['canon'] ? 'Cañón OK' : 'Sin Cañón' }}
                                </span>
                            </div>

                            <div class="flex items-center justify-between pt-2 border-t text-[11px] font-mono"
                                 style="border-color: var(--outline-variant);">
                                <span class="font-bold" style="color: #15803d;">{{ $lab['conteo']['disponible'] }} Disp.</span>
                                <span class="font-bold" style="color: #a16207;">{{ $lab['conteo']['ocupado'] }} Ocup.</span>
                                <span class="font-bold" style="color: #b91c1c;">{{ $lab['conteo']['mantenimiento'] }} No disp.</span>
                                <button type="button"
                                        data-open-map="{{ $idx }}"
                                        class="px-2.5 py-1 rounded-lg text-white font-sans text-[11px] font-bold cursor-pointer transition hover:opacity-95"
                                        style="background: var(--primary);">
                                    Inspeccionar PCs
                                </button>
                            </div>
                        </div>

                        <template id="lab-map-tpl-{{ $idx }}">
                            @include('partials.lab-sala', ['lab' => $lab])
                        </template>
                    @endforeach
                </div>
            </div>

            {{-- Inventario Físico de Hardware e Instrumental --}}
            <div class="p-5 rounded-2xl border"
                 style="background: var(--surface-container-lowest); border-color: var(--outline-variant); box-shadow: 0 2px 10px rgba(27, 28, 24, 0.04);">
                <div class="flex items-center justify-between pb-3 mb-3.5 border-b" style="border-color: var(--outline-variant);">
                    <div>
                        <span class="text-[10px] font-extrabold uppercase tracking-wider block" style="color: var(--primary);">Almacén Técnico E-102 · MetalNet</span>
                        <h3 class="text-base font-extrabold" style="color: var(--on-surface);">Inventario Físico de Hardware, Proyectores y Consumibles</h3>
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                    @foreach(($bmInstrumentalJs ?? []) as $item)
                        <div class="p-3 rounded-xl border flex items-center justify-between gap-2"
                             style="background: var(--surface-container-low); border-color: var(--outline-variant);">
                            <div class="min-w-0">
                                <span class="font-mono text-[10px] font-bold" style="color: var(--primary);">{{ $item['codigo'] }}</span>
                                <p class="font-bold truncate" style="color: var(--on-surface);">{{ $item['nombre'] }}</p>
                                <span class="text-[11px]" style="color: var(--on-surface-variant);">{{ $item['ubicacion'] }}</span>
                            </div>
                            <div class="text-right shrink-0">
                                <span class="text-sm font-mono font-extrabold block" style="color: #15803d;">{{ $item['disponibles'] }}/{{ $item['total'] }}</span>
                                <span class="text-[10px]" style="color: var(--tertiary);">Disponibles</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Columna Derecha (5 cols): Aprobar Préstamos de Equipo en Sitio y Atender Incidencias Técnicas --}}
        <div class="lg:col-span-5 space-y-5">
            <div class="p-5 rounded-2xl border"
                 style="background: var(--surface-container-lowest); border-color: var(--outline-variant); box-shadow: 0 2px 10px rgba(27, 28, 24, 0.04);">
                <div class="flex items-center justify-between pb-3 mb-3 border-b" style="border-color: var(--outline-variant);">
                    <h3 class="text-sm font-extrabold uppercase tracking-wider" style="color: var(--primary);">Préstamos de Equipo y Solicitudes en Sitio</h3>
                    <span class="px-2 py-0.5 rounded border font-mono text-xs font-bold"
                          style="background: var(--secondary-container); color: var(--on-secondary-container); border-color: #8fcb9b;">
                        {{ $prestamosList->count() }}
                    </span>
                </div>
                <div class="space-y-3 max-h-80 overflow-y-auto">
                    @forelse($prestamosList as $pr)
                        <div class="p-3.5 rounded-xl border space-y-2 text-xs"
                             style="background: var(--surface-container-low); border-color: var(--outline-variant);">
                            <div class="flex items-start justify-between gap-2">
                                <div>
                                    <span class="font-mono text-[10px] font-bold" style="color: var(--primary);">{{ $pr->folio }} · {{ strtoupper($pr->tipo) }}</span>
                                    <p class="font-bold" style="color: var(--on-surface);">{{ $pr->titulo }}</p>
                                    <p class="text-[11px]" style="color: var(--on-surface-variant);">{{ $pr->descripcion }}</p>
                                </div>
                                <span class="px-2 py-0.5 rounded border text-[10px] font-extrabold uppercase"
                                      style="background: #fefce8; color: #854d0e; border-color: #fde047;">
                                    {{ $pr->estado }}
                                </span>
                            </div>
                            @if($pr->estado === 'pendiente')
                                <div class="flex items-center justify-end gap-2 pt-1">
                                    <form method="POST" action="{{ route('panel.solicitud-resolver') }}">
                                        @csrf
                                        <input type="hidden" name="proceso_id" value="{{ $pr->id }}">
                                        <input type="hidden" name="accion" value="aprobar">
                                        <button type="submit"
                                                class="px-3 py-1 rounded-lg text-white font-extrabold text-[11px] cursor-pointer"
                                                style="background: var(--primary);">
                                            Entregar / Aprobar
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('panel.solicitud-resolver') }}">
                                        @csrf
                                        <input type="hidden" name="proceso_id" value="{{ $pr->id }}">
                                        <input type="hidden" name="accion" value="rechazar">
                                        <button type="submit"
                                                class="px-3 py-1 rounded-lg border font-bold text-[11px] cursor-pointer"
                                                style="background: #fef2f2; color: #991b1b; border-color: #fca5a5;">
                                            Rechazar
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    @empty
                        <p class="text-xs" style="color: var(--on-surface-variant);">No hay préstamos pendientes por validar.</p>
                    @endforelse
                </div>
            </div>

            {{-- Atender Incidencias Técnicas y Reparar Estaciones --}}
            <div class="p-5 rounded-2xl border"
                 style="background: var(--surface-container-lowest); border-color: var(--outline-variant); box-shadow: 0 2px 10px rgba(27, 28, 24, 0.04);">
                <div class="flex items-center justify-between pb-3 mb-3 border-b" style="border-color: var(--outline-variant);">
                    <h3 class="text-sm font-extrabold uppercase tracking-wider" style="color: #b91c1c;">Incidencias Técnicas Abiertas</h3>
                    <span class="px-2 py-0.5 rounded border font-mono text-xs font-bold"
                          style="background: #fef2f2; color: #991b1b; border-color: #fca5a5;">
                        {{ $incidenciasList->count() }}
                    </span>
                </div>
                <div class="space-y-3 max-h-80 overflow-y-auto">
                    @forelse($incidenciasList as $inc)
                        @php
                            $labNum = $inc->laboratorio_id ?? 1;
                            $pcCode = $inc->estacion?->codigo ?? 'PC-01';
                            $pcNum = (int) preg_replace('/\D+/', '', $pcCode) ?: 1;
                            $pcId = sprintf('LAB%d-PC%02d', $labNum, $pcNum);
                        @endphp
                        <div class="p-3.5 rounded-xl border space-y-2 text-xs"
                             style="background: #fef2f2; border-color: #fca5a5;">
                            <div class="flex items-start justify-between gap-2">
                                <div>
                                    <span class="font-mono font-extrabold" style="color: #991b1b;">{{ $inc->laboratorio?->codigo ?? 'L-01' }} · {{ $pcCode }}</span>
                                    <p class="font-bold mt-0.5" style="color: var(--on-surface);">{{ $inc->titulo }}</p>
                                    <p class="text-[11px]" style="color: var(--on-surface-variant);">{{ $inc->descripcion }}</p>
                                </div>
                                <span class="px-2 py-0.5 rounded border text-[10px] font-extrabold uppercase"
                                      style="background: #ffffff; color: #991b1b; border-color: #fca5a5;">
                                    {{ $inc->severidad }}
                                </span>
                            </div>
                            <form method="POST" action="{{ route('panel.pc-estado') }}" class="flex items-center justify-end gap-2 pt-1">
                                @csrf
                                <input type="hidden" name="estacion_id" value="{{ $pcId }}">
                                <input type="hidden" name="estado" value="disponible">
                                <input type="hidden" name="nota" value="Reparación verificada en sitio por Encargado">
                                <button type="submit"
                                        class="px-3 py-1.5 rounded-lg text-white font-extrabold text-[11px] cursor-pointer"
                                        style="background: var(--primary);">
                                    Marcar Reparada y Disponible
                                </button>
                            </form>
                        </div>
                    @empty
                        <p class="text-xs" style="color: var(--on-surface-variant);">Todas las incidencias están atendidas.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

@include('partials.bm-interactivo')
@endsection
