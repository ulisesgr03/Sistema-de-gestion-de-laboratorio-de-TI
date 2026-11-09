@extends('layouts.public')

@section('title', 'Salas y Disponibilidad en Tiempo Real')

@section('header')
<div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-4 border-b border-[#e4e3db]">
    <div>
        <div class="flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-[#675a4e]">
            <span class="material-symbols-outlined text-[16px] text-[#2f664f]">meeting_room</span>
            <span>Infraestructura Física · División de TI</span>
        </div>
        <h1 class="text-2xl sm:text-3xl font-extrabold text-[#1b1c18] mt-1 tracking-tight">
            Salas de Cómputo y Disponibilidad en Tiempo Real
        </h1>
        <p class="text-xs sm:text-sm text-[#404944] mt-1">
            Monitoreo en vivo de capacidad instalada, estaciones funcionales y especificaciones técnicas de hardware y software por laboratorio.
        </p>
    </div>

    <div class="flex items-center gap-3">
        <a href="{{ url('/reportar-averia') }}"
           class="inline-flex items-center gap-2 h-10 px-4 rounded-lg bg-[#ffffff] hover:bg-[#efeee7] border border-[#e4e3db] text-[#ba1a1a] font-semibold text-xs transition-colors">
            <span class="material-symbols-outlined text-[18px]">report_problem</span>
            <span>Reportar avería en sala</span>
        </a>
    </div>
</div>
@endsection

@section('content')
<!-- Barra de Filtros y Búsqueda -->
<div class="bg-[#ffffff] border border-[#e4e3db] rounded-xl p-4 mb-6 shadow-sm">
    <form method="GET" action="{{ route('salas.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-center">
        <!-- Buscador -->
        <div class="sm:col-span-6 relative">
            <span class="material-symbols-outlined absolute left-3 top-2.5 text-[#707973] pointer-events-none text-[18px]">search</span>
            <input type="text"
                   name="q"
                   value="{{ $busqueda ?? '' }}"
                   placeholder="Buscar por sala, edificio, tarjeta gráfica, procesador o software..."
                   class="w-full h-10 pl-10 pr-3 bg-[#efeee7] rounded-lg border border-[#c0c9c2] text-xs text-[#1b1c18] placeholder-[#707973] focus:outline-none focus:border-[#2f664f] focus:bg-[#ffffff] transition-colors">
        </div>

        <!-- Filtro por Edificio -->
        <div class="sm:col-span-4 relative">
            <select name="edificio"
                    class="w-full h-10 pl-3 pr-8 bg-[#efeee7] rounded-lg border border-[#c0c9c2] text-xs text-[#1b1c18] appearance-none focus:outline-none focus:border-[#2f664f] focus:bg-[#ffffff] transition-colors cursor-pointer">
                <option value="">Todos los Edificios (E y F)</option>
                <option value="Edificio E" {{ ($edificio ?? '') == 'Edificio E' ? 'selected' : '' }}>Edificio E (Redes, Software e IA)</option>
                <option value="Edificio F" {{ ($edificio ?? '') == 'Edificio F' ? 'selected' : '' }}>Edificio F (Embebidos y Certificaciones)</option>
            </select>
            <span class="material-symbols-outlined absolute right-2.5 top-2.5 text-[#707973] pointer-events-none text-[18px]">expand_more</span>
        </div>

        <!-- Botones de Acción -->
        <div class="sm:col-span-2 flex items-center gap-2">
            <button type="submit"
                    class="w-full h-10 bg-[#2f664f] hover:bg-[#497f67] text-white rounded-lg text-xs font-bold transition-colors flex items-center justify-center gap-1 shadow-sm">
                <span class="material-symbols-outlined text-[16px]">filter_list</span>
                <span>Filtrar</span>
            </button>
            @if(!empty($busqueda) || !empty($edificio))
                <a href="{{ route('salas.index') }}"
                   class="h-10 px-3 bg-[#efeee7] hover:bg-[#e4e3db] text-[#1b1c18] rounded-lg text-xs font-semibold flex items-center justify-center transition-colors">
                    Limpiar
                </a>
            @endif
        </div>
    </form>
</div>

<!-- Listado de Laboratorios -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    @forelse($laboratorios as $lab)
        @php
            $estacionesTotal = $lab->estaciones->count();
            $activas = $lab->estaciones->where('estado', 'activa')->count();
            $mantenimiento = $lab->estaciones->where('estado', 'mantenimiento')->count();
            $porcentajeActivas = $estacionesTotal > 0 ? round(($activas / $estacionesTotal) * 100) : 100;
        @endphp

        <article class="bg-[#ffffff] border border-[#e4e3db] rounded-2xl p-6 flex flex-col justify-between shadow-sm">
            <div>
                <!-- Encabezado de la Sala -->
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-[#316a42]"></span>
                            <span class="text-[11px] font-bold uppercase tracking-wider text-[#675a4e]">{{ $lab->ubicacion }}</span>
                        </div>
                        <h2 class="text-lg font-bold text-[#1b1c18] mt-1">{{ $lab->nombre }}</h2>
                    </div>

                    <span class="shrink-0 inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-[#b3f1be]/40 text-[#16512c] font-bold text-xs border border-[#316a42]/20">
                        <span class="w-1.5 h-1.5 rounded-full bg-[#316a42]"></span>
                        Sala operativa
                    </span>
                </div>

                <p class="text-xs text-[#404944] mt-2 leading-relaxed">
                    {{ $lab->descripcion }}
                </p>

                <!-- Métricas de Estaciones -->
                <div class="grid grid-cols-3 gap-3 my-4 p-3 bg-[#f5f4ec] rounded-xl border border-[#e4e3db] text-center">
                    <div>
                        <span class="block text-[10px] font-bold uppercase text-[#675a4e]">Capacidad total</span>
                        <span class="text-base font-extrabold text-[#1b1c18] font-mono">{{ $lab->capacidad }} estaciones</span>
                    </div>
                    <div>
                        <span class="block text-[10px] font-bold uppercase text-[#316a42]">Operativas</span>
                        <span class="text-base font-extrabold text-[#316a42] font-mono">{{ $activas }} funcionales</span>
                    </div>
                    <div>
                        <span class="block text-[10px] font-bold uppercase text-[#ba1a1a]">En mantenimiento</span>
                        <span class="text-base font-extrabold text-[#ba1a1a] font-mono">{{ $mantenimiento }}</span>
                    </div>
                </div>

                <!-- Mini Malla de Estaciones de la Sala -->
                <div class="mb-4">
                    <div class="flex items-center justify-between text-[11px] text-[#675a4e] font-bold mb-2">
                        <span>Disposición de estaciones de trabajo (PC-01 a PC-{{ sprintf('%02d', $estacionesTotal) }})</span>
                        <span class="text-[#316a42]">{{ $porcentajeActivas }}% de disponibilidad</span>
                    </div>
                    <div class="grid grid-cols-6 sm:grid-cols-10 gap-1.5 p-3 bg-[#efeee7] rounded-xl border border-[#e4e3db]">
                        @foreach($lab->estaciones as $est)
                            <div title="Estación {{ $est->nombre }} ({{ ucfirst($est->estado) }}) - {{ $est->ubicacion }}"
                                 class="h-6 rounded text-[10px] font-mono font-bold flex items-center justify-center border transition-transform hover:scale-110 cursor-help {{ $est->estado === 'activa' ? 'bg-[#ffffff] text-[#1b1c18] border-[#c0c9c2]' : 'bg-[#ffdad6] text-[#ba1a1a] border-[#ba1a1a]' }}">
                                {{ substr($est->nombre, -2) }}
                            </div>
                        @endforeach
                    </div>
                    <div class="flex items-center gap-4 text-[10px] text-[#707973] mt-2">
                        <span class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded bg-[#ffffff] border border-[#c0c9c2]"></span> Estación operativa</span>
                        <span class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded bg-[#ffdad6] border border-[#ba1a1a]"></span> En mantenimiento</span>
                    </div>
                </div>

            </div>

            <!-- Acciones de Navegación -->
            <div class="pt-4 border-t border-[#e4e3db] flex flex-wrap items-center justify-between gap-2">
                <a href="{{ route('horarios.index', ['laboratorio_id' => $lab->id]) }}"
                   class="inline-flex items-center gap-1 text-xs text-[#2f664f] hover:underline font-bold">
                    <span class="material-symbols-outlined text-[16px]">calendar_month</span>
                    <span>Ver horarios de esta sala</span>
                </a>

                <a href="{{ route('averia.create', ['lab' => $lab->id]) }}"
                   class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-[#efeee7] hover:bg-[#e4e3db] text-[#1b1c18] text-xs font-semibold transition-colors">
                    <span class="material-symbols-outlined text-[15px] text-[#ba1a1a]">report_problem</span>
                    <span>Reportar avería</span>
                </a>
            </div>
        </article>
    @empty
        <div class="col-span-2 p-12 bg-[#ffffff] border border-[#e4e3db] rounded-2xl text-center">
            <span class="material-symbols-outlined text-4xl text-[#707973] mb-2">search_off</span>
            <h3 class="text-base font-bold text-[#1b1c18]">No se encontraron salas con los criterios especificados</h3>
            <p class="text-xs text-[#707973] mt-1">Intente remover filtros o realizar una búsqueda con otros términos.</p>
            <a href="{{ route('salas.index') }}" class="inline-flex items-center gap-1 text-xs text-[#2f664f] font-bold mt-4 hover:underline">
                Restablecer vista completa
            </a>
        </div>
    @endforelse
</div>
@endsection
