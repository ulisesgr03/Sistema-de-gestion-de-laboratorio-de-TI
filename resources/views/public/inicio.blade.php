@extends('layouts.public')

@section('title', 'Mapa 3D Arquitectónico · ' . config('app.institution_name', 'MetalNet'))

@section('content')
@php
    $labs = $laboratoriosDetallados ?? $bmLabs ?? [];
    $totales = $totales ?? $bmTotales ?? ['total' => 200, 'disponible' => 88, 'ocupado' => 96, 'mantenimiento' => 16, 'labs' => 10, 'sin_canon' => 4];
@endphp

{{-- ── RESUMEN EJECUTIVO DE TELEMETRÍA EN VIVO (PALETA OFICIAL STITCH / METALNET) ── --}}
<section class="max-w-[1440px] mx-auto px-3 sm:px-6 lg:px-8 pt-3 pb-2">
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
        {{-- 10 Laboratorios --}}
        <div class="px-4 py-3.5 rounded-2xl border border-surface-container-highest bg-surface-container-lowest shadow-sm flex items-center justify-between">
            <div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider block text-outline">
                    {{ config('app.institution_full', 'MetalNet · Laboratorios de Ingeniería en Sistemas') }}
                </span>
                <span class="text-2xl sm:text-3xl font-black font-mono leading-none mt-1 block text-on-surface">
                    {{ $totales['labs'] ?? 10 }} Labs
                </span>
            </div>
            <span class="w-11 h-11 rounded-xl border border-surface-container-highest bg-surface-container text-primary flex items-center justify-center shrink-0 shadow-xs">
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5"/><path d="M2 12l10 5 10-5"/></svg>
            </span>
        </div>

        {{-- Estaciones Disponibles (Verde Stitch) --}}
        <a href="{{ route('public.salas') }}"
           class="px-4 py-3.5 rounded-2xl border border-[#b3f1be] bg-surface-container-lowest text-left transition hover:border-primary shadow-sm flex items-center justify-between">
            <div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider block text-[#16512c]">Disponible / Libre</span>
                <span class="text-2xl sm:text-3xl font-black font-mono leading-none mt-1 block text-primary" data-total-count="disponible">{{ $totales['disponible'] }}</span>
            </div>
            <span class="w-11 h-11 rounded-xl border border-[#b3f1be] bg-[#e8f5ec] text-primary flex items-center justify-center shrink-0 shadow-xs">
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            </span>
        </a>

        {{-- Estaciones Ocupadas / En Uso (Amarillo / Ámbar) --}}
        <a href="{{ route('public.salas') }}"
           class="px-4 py-3.5 rounded-2xl border border-[#fde68a] bg-surface-container-lowest text-left transition hover:border-status-occupied shadow-sm flex items-center justify-between">
            <div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider block text-[#92400e]">Ocupado / En Uso</span>
                <span class="text-2xl sm:text-3xl font-black font-mono leading-none mt-1 block text-status-occupied" data-total-count="ocupado">{{ $totales['ocupado'] }}</span>
            </div>
            <span class="w-11 h-11 rounded-xl border border-[#fde68a] bg-[#fef3c7] text-status-occupied flex items-center justify-center shrink-0 shadow-xs">
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" x2="16" y1="21" y2="21"/><line x1="12" x2="12" y1="17" y2="21"/></svg>
            </span>
        </a>

        {{-- No Disponibles (Rojo) --}}
        <a href="{{ route('public.salas') }}"
           class="px-4 py-3.5 rounded-2xl border border-[#fca5a5] bg-surface-container-lowest text-left transition hover:border-status-unavailable shadow-sm flex items-center justify-between">
            <div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider block text-[#991b1b]">No Disponible</span>
                <span class="text-2xl sm:text-3xl font-black font-mono leading-none mt-1 block text-status-unavailable" data-total-count="mantenimiento">{{ $totales['mantenimiento'] }}</span>
            </div>
            <span class="w-11 h-11 rounded-xl border border-[#fca5a5] bg-[#ffdad6] text-status-unavailable flex items-center justify-center shrink-0 shadow-xs">
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><line x1="12" x2="12" y1="9" y2="13"/><line x1="12" x2="12.01" y1="17" y2="17"/></svg>
            </span>
        </a>
    </div>
</section>

{{-- ── PLANO ARQUITECTÓNICO 3D INTERACTIVO (PLANTA BAJA Y PLANTA ALTA) ────── --}}
@include('partials.estructura-bm-3d', ['labs' => $labs])

@include('partials.bm-interactivo')
@endsection
