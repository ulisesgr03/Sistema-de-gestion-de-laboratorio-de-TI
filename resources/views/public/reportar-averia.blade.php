@extends('layouts.public')

@section('title', 'Reportar Fallo Técnico · ' . config('app.institution_name', 'MetalNet'))

@section('content')
@php
    $labsList = $salas ?? $bmLabs ?? [];
@endphp

<section class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <div class="rounded-2xl border border-surface-container-highest p-6 bg-surface-container-lowest shadow-sm">
        <div class="flex items-center justify-between pb-4 border-b border-surface-container-highest">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-xl border border-[#fca5a5] flex items-center justify-center shrink-0 bg-[#ffdad6] text-[#991b1b] shadow-xs">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><line x1="12" x2="12" y1="9" y2="13"/><line x1="12" x2="12.01" y1="17" y2="17"/></svg>
                </div>
                <div>
                    <span class="text-[10px] font-extrabold uppercase tracking-wider block text-[#991b1b]">
                        Mesa de Soporte e Ingeniería · {{ config('app.institution_name', 'MetalNet') }}
                    </span>
                    <h1 class="text-xl font-extrabold text-on-surface">Levantar Reporte de Fallo en Estación de Cómputo</h1>
                </div>
            </div>

            <a href="{{ url('/') }}"
               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-surface-container-highest bg-surface-container-low hover:bg-surface-container text-primary text-xs font-bold transition">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
                <span>Volver al Inicio</span>
            </a>
        </div>

        <form method="POST" action="{{ route('public.averia.store') }}" class="mt-5 space-y-4">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase mb-1 text-outline">Matrícula / Identificador *</label>
                    <input type="text" name="matricula" required value="{{ Auth::check() ? (Auth::user()->matricula ?? Auth::user()->email) : '' }}" placeholder="MN-ALU-2026"
                           class="w-full h-10 px-3 rounded-xl border border-outline-variant bg-surface-container-low text-xs font-mono text-on-surface focus:outline-none focus:border-primary">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase mb-1 text-outline">Nombre Completo *</label>
                    <input type="text" name="nombre" required value="{{ Auth::check() ? Auth::user()->name : '' }}" placeholder="Nombre del reportante"
                           class="w-full h-10 px-3 rounded-xl border border-outline-variant bg-surface-container-low text-xs text-on-surface focus:outline-none focus:border-primary">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase mb-1 text-outline">Laboratorio *</label>
                    <select name="sala"
                            class="w-full h-10 px-3 rounded-xl border border-outline-variant bg-surface-container-low text-xs font-bold text-on-surface focus:outline-none focus:border-primary">
                        @foreach($labsList as $l)
                            <option value="{{ $l['codigo'] }}" {{ ($salaSeleccionada ?? '') === $l['codigo'] ? 'selected' : '' }}>{{ $l['codigo'] }} · {{ $l['nombre_corto'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase mb-1 text-outline">Estación (PC-01 a PC-20) *</label>
                    <input type="text" name="pc" required value="{{ $pcSeleccionada ?? 'PC-01' }}"
                           class="w-full h-10 px-3 rounded-xl border border-outline-variant bg-surface-container-low text-xs font-mono font-bold text-primary focus:outline-none focus:border-primary">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase mb-1 text-outline">Categoría de Incidencia *</label>
                    <select name="motivo"
                            class="w-full h-10 px-3 rounded-xl border border-outline-variant bg-surface-container-low text-xs font-bold text-on-surface focus:outline-none focus:border-primary">
                        <option value="Hardware">Hardware / No enciende</option>
                        <option value="Red">Red / Sin enlace VLAN</option>
                        <option value="Software">Software / Entorno de compilación</option>
                        <option value="Periféricos">Periféricos / Monitor / Teclado</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase mb-1 text-outline">Descripción Técnica del Problema *</label>
                <textarea name="causa" rows="3" required placeholder="Describe brevemente el fallo observado en el equipo para guiar la revisión del soporte técnico..."
                          class="w-full p-3 rounded-xl border border-outline-variant bg-surface-container-low text-xs text-on-surface focus:outline-none focus:border-primary"></textarea>
            </div>

            <div class="pt-2 flex items-center justify-end gap-2">
                <a href="{{ route('public.salas') }}"
                   class="px-4 py-2.5 rounded-xl border border-surface-container-highest bg-surface-container-low hover:bg-surface-container text-xs font-bold text-on-surface-variant transition">
                    Cancelar
                </a>
                <button type="submit"
                        class="px-5 py-2.5 rounded-xl text-white text-xs font-extrabold cursor-pointer transition hover:opacity-95 bg-error shadow-sm">
                    Registrar Reporte Técnico
                </button>
            </div>
        </form>
    </div>
</section>
@endsection
