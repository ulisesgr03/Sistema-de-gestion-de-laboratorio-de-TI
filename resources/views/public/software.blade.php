@extends('layouts.public')

@section('title', 'Catálogo Institucional de Software')

@section('header')
<div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-4 border-b border-[#e4e3db]">
    <div>
        <div class="flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-[#675a4e]">
            <span class="material-symbols-outlined text-[16px] text-[#2f664f]">apps</span>
            <span>Matriz de Software Homologado · División de TI</span>
        </div>
        <h1 class="text-2xl sm:text-3xl font-extrabold text-[#1b1c18] mt-1 tracking-tight">
            Catálogo Institucional de Software y Licenciamiento
        </h1>
        <p class="text-xs sm:text-sm text-[#404944] mt-1">
            Herramientas oficiales, entornos de desarrollo (IDE), simuladores de redes, suites científicas y convenios de licenciamiento campus vigentes.
        </p>
    </div>

    <!-- Badge de Licenciamiento Activo -->
    <div class="flex items-center gap-2 px-3 py-1.5 rounded-lg bg-[#ffffff] border border-[#e4e3db] text-xs font-mono">
        <span class="w-2 h-2 rounded-full bg-[#316a42]"></span>
        <span class="text-[#1b1c18] font-bold">100% homologado 2026</span>
    </div>
</div>
@endsection

@section('content')
<!-- Barra de Búsqueda de Software -->
<div class="bg-[#ffffff] border border-[#e4e3db] rounded-xl p-4 mb-6 shadow-sm">
    <form method="GET" action="{{ route('software.index') }}" class="flex flex-col sm:flex-row items-center gap-3">
        <div class="relative flex-1 w-full">
            <span class="material-symbols-outlined absolute left-3 top-2.5 text-[#707973] pointer-events-none text-[18px]">search</span>
            <input type="text"
                   name="q"
                   value="{{ $busqueda ?? '' }}"
                   placeholder="Buscar por nombre de software (Packet Tracer, MATLAB, Docker...), fabricante o tecnología..."
                   class="w-full h-10 pl-10 pr-4 bg-[#efeee7] rounded-lg border border-[#c0c9c2] text-xs text-[#1b1c18] placeholder-[#707973] focus:outline-none focus:border-[#2f664f] focus:bg-[#ffffff] transition-colors">
        </div>

        <div class="flex items-center gap-2 w-full sm:w-auto">
            <button type="submit"
                    class="h-10 px-5 bg-[#2f664f] hover:bg-[#497f67] text-white rounded-lg text-xs font-bold transition-colors flex items-center justify-center gap-1.5 shadow-sm">
                <span class="material-symbols-outlined text-[16px]">search</span>
                <span>Buscar</span>
            </button>
            @if(!empty($busqueda))
                <a href="{{ route('software.index') }}"
                   class="h-10 px-3 bg-[#efeee7] hover:bg-[#e4e3db] text-[#1b1c18] rounded-lg text-xs font-semibold flex items-center justify-center">
                    Limpiar
                </a>
            @endif
        </div>
    </form>
</div>

<!-- Grid de Títulos de Software -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    @forelse($softwareList as $sw)
        @php
            $licencia = $sw->licencias->first();
            $tipoLicencia = $licencia ? $licencia->tipo : 'Licencia Institucional';
            $isOpenSource = str_contains(strtolower($tipoLicencia), 'open') || str_contains(strtolower($tipoLicencia), 'gpl') || str_contains(strtolower($tipoLicencia), 'mit');
        @endphp

        <div class="bg-[#ffffff] border border-[#e4e3db] rounded-2xl p-6 flex flex-col justify-between shadow-sm">
            <div>
                <!-- Encabezado de la Tarjeta -->
                <div class="flex items-start justify-between gap-3">
                    <div class="w-10 h-10 rounded-xl bg-[#efeee7] border border-[#e4e3db] flex items-center justify-center text-[#2f664f] shrink-0">
                        <span class="material-symbols-outlined text-[22px]">terminal</span>
                    </div>

                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded text-[11px] font-bold {{ $isOpenSource ? 'bg-[#efeee7] text-[#1b1c18]' : 'bg-[#b3f1be]/40 text-[#16512c]' }}">
                        <span class="w-1.5 h-1.5 rounded-full {{ $isOpenSource ? 'bg-[#707973]' : 'bg-[#316a42]' }}"></span>
                        {{ $tipoLicencia }}
                    </span>
                </div>

                <div class="mt-4">
                    <div class="flex items-baseline justify-between gap-2">
                        <h3 class="text-base font-extrabold text-[#1b1c18] tracking-tight">{{ $sw->nombre }}</h3>
                        <span class="text-xs font-mono font-bold text-[#675a4e] bg-[#f5f4ec] px-2 py-0.5 rounded border border-[#e4e3db]">
                            v{{ $sw->version }}
                        </span>
                    </div>
                    <span class="text-[11px] font-bold uppercase tracking-wider text-[#707973] block mt-0.5">
                        {{ $sw->fabricante }}
                    </span>
                </div>

                <p class="text-xs text-[#404944] mt-3 leading-relaxed">
                    {{ $sw->descripcion }}
                </p>

                <!-- Metadatos de Licenciamiento y Compatibilidad -->
                <div class="mt-4 p-3 bg-[#f5f4ec] rounded-xl border border-[#e4e3db] text-xs space-y-1">
                    <div class="flex justify-between items-center text-[11px]">
                        <span class="text-[#707973]">Licencias disponibles:</span>
                        <span class="font-bold text-[#1b1c18] font-mono">{{ $licencia ? $licencia->cantidad_licencias . ' puestos' : 'Campus ilimitada' }}</span>
                    </div>
                    <div class="flex justify-between items-center text-[11px]">
                        <span class="text-[#707973]">Vigencia institucional:</span>
                        <span class="font-semibold text-[#316a42] font-mono">{{ $licencia && $licencia->fecha_vencimiento ? $licencia->fecha_vencimiento->format('d/m/Y') : 'Permanente o anual' }}</span>
                    </div>
                    <div class="flex justify-between items-center text-[11px]">
                        <span class="text-[#707973]">Entorno de ejecución:</span>
                        <span class="font-semibold text-[#1b1c18]">Ubuntu 22.04 / Win 11</span>
                    </div>
                </div>
            </div>

            <!-- Pie de la Tarjeta -->
            <div class="mt-5 pt-3 border-t border-[#e4e3db] flex items-center justify-between text-xs">
                <span class="text-[11px] text-[#707973] flex items-center gap-1">
                    <span class="material-symbols-outlined text-[14px] text-[#316a42]">check_circle</span>
                    Instalado en salas
                </span>

                <a href="mailto:soporte.laboratorios@uptex.edu.mx?subject=Solicitud de Licencia {{ urlencode($sw->nombre) }}"
                   class="inline-flex items-center gap-1 font-bold text-[#2f664f] hover:underline text-xs">
                    <span>Solicitar activación</span>
                    <span class="material-symbols-outlined text-[14px]">arrow_forward</span>
                </a>
            </div>
        </div>
    @empty
        <div class="col-span-3 p-12 bg-[#ffffff] border border-[#e4e3db] rounded-2xl text-center">
            <span class="material-symbols-outlined text-4xl text-[#707973] mb-2">search_off</span>
            <h3 class="text-base font-bold text-[#1b1c18]">No se encontró software con el término ingresado</h3>
            <p class="text-xs text-[#707973] mt-1">Verifique la ortografía o intente con palabras clave como Cisco, MATLAB, Docker o Python.</p>
            <a href="{{ route('software.index') }}" class="inline-flex items-center gap-1 text-xs text-[#2f664f] font-bold mt-4 hover:underline">
                Ver catálogo completo
            </a>
        </div>
    @endforelse
</div>

<!-- Instrucciones para Alumnos sobre Descargas Educativas -->
<div class="mt-8 bg-[#ffffff] border border-[#e4e3db] rounded-2xl p-6 sm:p-8 shadow-sm">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-4 border-b border-[#e4e3db] gap-3">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-[#efeee7] border border-[#e4e3db] flex items-center justify-center text-[#2f664f] shrink-0">
                <span class="material-symbols-outlined text-[24px]">school</span>
            </div>
            <div>
                <h3 class="text-base font-bold text-[#1b1c18]">Licencias educativas para equipos personales de alumnos y docentes</h3>
                <span class="text-xs text-[#404944]">Convenios oficiales de la Universidad Politécnica de Texcoco</span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6 text-xs text-[#404944] leading-relaxed">
        <div class="p-4 bg-[#f5f4ec] rounded-xl border border-[#e4e3db]">
            <strong class="font-bold text-[#1b1c18] block text-sm mb-1">MathWorks MATLAB Campus</strong>
            <p>Acceso ilimitado a MATLAB, Simulink y más de 100 toolboxes para instalación en laptops personales utilizando el correo institucional @uptex.edu.mx.</p>
        </div>
        <div class="p-4 bg-[#f5f4ec] rounded-xl border border-[#e4e3db]">
            <strong class="font-bold text-[#1b1c18] block text-sm mb-1">JetBrains Educational Pack</strong>
            <p>Licencia gratuita de la suite completa de IntelliJ IDEA Ultimate, PyCharm Professional, WebStorm y CLion para proyectos curriculares.</p>
        </div>
        <div class="p-4 bg-[#f5f4ec] rounded-xl border border-[#e4e3db]">
            <strong class="font-bold text-[#1b1c18] block text-sm mb-1">Cisco Networking Academy</strong>
            <p>Descarga directa de Cisco Packet Tracer 8.2 y cursos oficiales con acreditación de habilidades CCNA y ciberseguridad.</p>
        </div>
    </div>
</div>
@endsection
