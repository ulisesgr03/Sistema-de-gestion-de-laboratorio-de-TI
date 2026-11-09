@extends('layouts.public')

@section('title', 'Reportar Avería en Laboratorio')

@section('header')
<div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-4 border-b border-[#e4e3db]">
    <div>
        <div class="flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-[#ba1a1a]">
            <span class="material-symbols-outlined text-[16px]">report_problem</span>
            <span>Mesa de Ayuda de Laboratorios · Soporte Técnico</span>
        </div>
        <h1 class="text-2xl sm:text-3xl font-extrabold text-[#1b1c18] mt-1 tracking-tight">
            Reportar Avería o Falla Técnica en Sala
        </h1>
        <p class="text-xs sm:text-sm text-[#404944] mt-1">
            Canal oficial para estudiantes, docentes e investigadores. Genere un ticket institucional para atención inmediata de terminales y equipo.
        </p>
    </div>

    <div class="flex items-center gap-2 px-3 py-1.5 rounded-lg bg-[#ffffff] border border-[#e4e3db] text-xs font-mono">
        <span class="material-symbols-outlined text-[16px] text-[#316a42]">schedule</span>
        <span class="text-[#1b1c18] font-bold">Tiempo promedio de atención: &lt; 20 min</span>
    </div>
</div>
@endsection

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

    <!-- Formulario de Reporte -->
    <div class="lg:col-span-8">
        <div class="bg-[#ffffff] border border-[#e4e3db] rounded-2xl p-6 sm:p-8 shadow-sm">
            <h2 class="text-base font-bold text-[#1b1c18] mb-1 flex items-center gap-2">
                <span class="material-symbols-outlined text-[20px] text-[#2f664f]">confirmation_number</span>
                <span>Registro de incidencia técnica</span>
            </h2>
            <p class="text-xs text-[#707973] mb-6">
                Complete los datos de la terminal y describa el síntoma presentado para que el técnico de turno acuda con el reemplazo adecuado.
            </p>

            @if ($errors->any())
                <div class="mb-6 p-4 rounded-xl bg-[#ffdad6] border border-[#ba1a1a] text-[#93000a] text-xs" role="alert">
                    <div class="font-bold mb-1 flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-[16px]">error</span>
                        <span>Corrija los siguientes campos antes de continuar:</span>
                    </div>
                    <ul class="list-disc pl-5 space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('averia.store') }}" class="space-y-5">
                @csrf

                <!-- Ubicación de la Falla -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="flex flex-col gap-1.5">
                        <label for="laboratorio_id" class="text-xs font-bold text-[#675a4e]">
                            Laboratorio / sala afectada <span class="text-[#ba1a1a]">*</span>
                        </label>
                        <div class="relative flex items-center">
                            <span class="material-symbols-outlined absolute left-3 text-[#707973] pointer-events-none text-[18px]">dns</span>
                            <select id="laboratorio_id"
                                    name="laboratorio_id"
                                    required
                                    class="w-full h-10 pl-10 pr-8 bg-[#ffffff] rounded-lg border border-[#c0c9c2] text-xs text-[#1b1c18] appearance-none focus:outline-none focus:border-[#2f664f] focus:ring-1 focus:ring-[#2f664f] cursor-pointer">
                                <option value="">Seleccione el laboratorio...</option>
                                @foreach($laboratorios as $lab)
                                    <option value="{{ $lab->id }}" {{ old('laboratorio_id', request('lab')) == $lab->id ? 'selected' : '' }}>
                                        {{ $lab->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            <span class="material-symbols-outlined absolute right-3 text-[#707973] pointer-events-none text-[18px]">expand_more</span>
                        </div>
                    </div>

                    <div class="flex flex-col gap-1.5">
                        <label for="estacion_nombre" class="text-xs font-bold text-[#675a4e]">
                            Número de estación o puesto (opcional)
                        </label>
                        <div class="relative flex items-center">
                            <span class="material-symbols-outlined absolute left-3 text-[#707973] pointer-events-none text-[18px]">desktop_windows</span>
                            <input type="text"
                                   id="estacion_nombre"
                                   name="estacion_nombre"
                                   value="{{ old('estacion_nombre') }}"
                                   placeholder="Ejemplo: PC-04, PC-18, o Switch Central"
                                   class="w-full h-10 pl-10 pr-3 bg-[#ffffff] rounded-lg border border-[#c0c9c2] text-xs text-[#1b1c18] placeholder-[#707973] focus:outline-none focus:border-[#2f664f] focus:ring-1 focus:ring-[#2f664f]">
                        </div>
                    </div>
                </div>

                <!-- Categoría y Prioridad Técnica -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="flex flex-col gap-1.5">
                        <label for="categoria" class="text-xs font-bold text-[#675a4e]">
                            Tipo de componente afectado <span class="text-[#ba1a1a]">*</span>
                        </label>
                        <div class="relative flex items-center">
                            <span class="material-symbols-outlined absolute left-3 text-[#707973] pointer-events-none text-[18px]">category</span>
                            <select id="categoria"
                                    name="categoria"
                                    required
                                    class="w-full h-10 pl-10 pr-8 bg-[#ffffff] rounded-lg border border-[#c0c9c2] text-xs text-[#1b1c18] appearance-none focus:outline-none focus:border-[#2f664f] focus:ring-1 focus:ring-[#2f664f] cursor-pointer">
                                <option value="hardware" {{ old('categoria') == 'hardware' ? 'selected' : '' }}>Hardware (CPU, RAM, Fuente, Pantalla)</option>
                                <option value="periferico" {{ old('categoria') == 'periferico' ? 'selected' : '' }}>Periféricos (Teclado, Mouse, Audífonos)</option>
                                <option value="red" {{ old('categoria') == 'red' ? 'selected' : '' }}>Conectividad y Red LAN (Sin IP / Sin Internet)</option>
                                <option value="software" {{ old('categoria') == 'software' ? 'selected' : '' }}>Software y S.O. (Fallo de arranque, licencia)</option>
                                <option value="proyector" {{ old('categoria') == 'proyector' ? 'selected' : '' }}>Proyector / Pantalla de Cátedra</option>
                                <option value="instalacion" {{ old('categoria') == 'instalacion' ? 'selected' : '' }}>Instalación Física / Clima / Tomas Eléctricas</option>
                            </select>
                            <span class="material-symbols-outlined absolute right-3 text-[#707973] pointer-events-none text-[18px]">expand_more</span>
                        </div>
                    </div>

                    <div class="flex flex-col gap-1.5">
                        <label for="prioridad" class="text-xs font-bold text-[#675a4e]">
                            Prioridad de atención <span class="text-[#ba1a1a]">*</span>
                        </label>
                        <div class="relative flex items-center">
                            <span class="material-symbols-outlined absolute left-3 text-[#707973] pointer-events-none text-[18px]">flag</span>
                            <select id="prioridad"
                                    name="prioridad"
                                    required
                                    class="w-full h-10 pl-10 pr-8 bg-[#ffffff] rounded-lg border border-[#c0c9c2] text-xs text-[#1b1c18] appearance-none focus:outline-none focus:border-[#2f664f] focus:ring-1 focus:ring-[#2f664f] cursor-pointer">
                                <option value="media" {{ old('prioridad') == 'media' ? 'selected' : '' }}>Media · Falla individual en estación</option>
                                <option value="baja" {{ old('prioridad') == 'baja' ? 'selected' : '' }}>Baja · Detalle estético o periférico menor</option>
                                <option value="alta" {{ old('prioridad') == 'alta' ? 'selected' : '' }}>Alta · Impide desarrollo de práctica en curso</option>
                                <option value="critica" {{ old('prioridad') == 'critica' ? 'selected' : '' }}>Crítica · Afecta a toda la sala o equipo central</option>
                            </select>
                            <span class="material-symbols-outlined absolute right-3 text-[#707973] pointer-events-none text-[18px]">expand_more</span>
                        </div>
                    </div>
                </div>

                <!-- Resumen de la Falla -->
                <div class="flex flex-col gap-1.5">
                    <label for="titulo" class="text-xs font-bold text-[#675a4e]">
                        Título o resumen de la incidencia <span class="text-[#ba1a1a]">*</span>
                    </label>
                    <input type="text"
                           id="titulo"
                           name="titulo"
                           value="{{ old('titulo') }}"
                           required
                           placeholder="Ejemplo: Pantalla parpadea en azul al iniciar sesión en Ubuntu"
                           class="w-full h-10 px-3 bg-[#ffffff] rounded-lg border border-[#c0c9c2] text-xs text-[#1b1c18] placeholder-[#707973] focus:outline-none focus:border-[#2f664f] focus:ring-1 focus:ring-[#2f664f]">
                </div>

                <!-- Detalle Extendido -->
                <div class="flex flex-col gap-1.5">
                    <label for="descripcion" class="text-xs font-bold text-[#675a4e]">
                        Descripción detallada del síntoma <span class="text-[#ba1a1a]">*</span>
                    </label>
                    <textarea id="descripcion"
                              name="descripcion"
                              rows="4"
                              required
                              placeholder="Describa el comportamiento anómalo: ¿emite ruidos inusuales?, ¿muestra algún código de error en pantalla?, ¿ocurre al ejecutar un programa específico?..."
                              class="w-full p-3 bg-[#ffffff] rounded-lg border border-[#c0c9c2] text-xs text-[#1b1c18] placeholder-[#707973] focus:outline-none focus:border-[#2f664f] focus:ring-1 focus:ring-[#2f664f] leading-relaxed">{{ old('descripcion') }}</textarea>
                </div>

                <!-- Datos de Contacto del Reportante -->
                <div class="pt-4 border-t border-[#e4e3db] space-y-4">
                    <span class="text-xs font-bold uppercase tracking-wider text-[#675a4e] block">
                        Identificación del reportante (para seguimiento)
                    </span>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div class="flex flex-col gap-1.5">
                            <label for="reportante_nombre" class="text-xs font-medium text-[#404944]">
                                Nombre completo <span class="text-[#ba1a1a]">*</span>
                            </label>
                            <input type="text"
                                   id="reportante_nombre"
                                   name="reportante_nombre"
                                   value="{{ old('reportante_nombre', Auth::user()->name ?? '') }}"
                                   required
                                   placeholder="Nombre y Apellidos"
                                   class="w-full h-10 px-3 bg-[#ffffff] rounded-lg border border-[#c0c9c2] text-xs text-[#1b1c18] focus:outline-none focus:border-[#2f664f]">
                        </div>

                        <div class="flex flex-col gap-1.5">
                            <label for="reportante_matricula" class="text-xs font-medium text-[#404944]">
                                Matrícula o clave de nómina <span class="text-[#ba1a1a]">*</span>
                            </label>
                            <input type="text"
                                   id="reportante_matricula"
                                   name="reportante_matricula"
                                   value="{{ old('reportante_matricula', Auth::user()->matricula ?? '') }}"
                                   required
                                   placeholder="2021134089 o DOC-0892"
                                   class="w-full h-10 px-3 bg-[#ffffff] rounded-lg border border-[#c0c9c2] text-xs text-[#1b1c18] focus:outline-none focus:border-[#2f664f]">
                        </div>

                        <div class="flex flex-col gap-1.5">
                            <label for="reportante_correo" class="text-xs font-medium text-[#404944]">
                                Correo institucional
                            </label>
                            <input type="email"
                                   id="reportante_correo"
                                   name="reportante_correo"
                                   value="{{ old('reportante_correo', Auth::user()->email ?? '') }}"
                                   placeholder="@uptex.edu.mx"
                                   class="w-full h-10 px-3 bg-[#ffffff] rounded-lg border border-[#c0c9c2] text-xs text-[#1b1c18] focus:outline-none focus:border-[#2f664f]">
                        </div>
                    </div>
                </div>

                <!-- Botón de Envío -->
                <div class="pt-2">
                    <button type="submit"
                            class="w-full sm:w-auto h-11 px-8 bg-[#2f664f] hover:bg-[#497f67] text-white font-bold text-xs rounded-lg flex items-center justify-center gap-2 transition-all active:scale-[0.99] shadow-sm">
                        <span class="material-symbols-outlined text-[18px]">send</span>
                        <span>Registrar reporte institucional</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Barra Lateral de Información Técnica de Soporte -->
    <div class="lg:col-span-4 space-y-6">

        <!-- Tarjeta de Tiempos y SLA -->
        <div class="bg-[#ffffff] border border-[#e4e3db] rounded-2xl p-6 shadow-sm">
            <h3 class="text-xs font-bold uppercase tracking-wider text-[#675a4e] mb-3 flex items-center gap-1.5">
                <span class="material-symbols-outlined text-[18px] text-[#2f664f]">speed</span>
                <span>Compromisos de atención técnica</span>
            </h3>

            <div class="space-y-3 text-xs">
                <div class="p-3 bg-[#f5f4ec] rounded-xl border border-[#e4e3db]">
                    <div class="flex justify-between items-center font-bold text-[#ba1a1a]">
                        <span>Fallas críticas</span>
                        <span>&lt; 1 hora</span>
                    </div>
                    <p class="text-[11px] text-[#707973] mt-1">Switches de red, servidores locales o suspensión total de una sala.</p>
                </div>

                <div class="p-3 bg-[#f5f4ec] rounded-xl border border-[#e4e3db]">
                    <div class="flex justify-between items-center font-bold text-[#1b1c18]">
                        <span>Fallas altas y medias</span>
                        <span>&lt; 4 horas</span>
                    </div>
                    <p class="text-[11px] text-[#707973] mt-1">Reemplazo de monitores, teclados, fuentes de poder o ratones.</p>
                </div>

                <div class="p-3 bg-[#f5f4ec] rounded-xl border border-[#e4e3db]">
                    <div class="flex justify-between items-center font-bold text-[#316a42]">
                        <span>Fallas leves y de software</span>
                        <span>&lt; 24 horas</span>
                    </div>
                    <p class="text-[11px] text-[#707973] mt-1">Actualización de paquetes, licencias o configuración de perfiles.</p>
                </div>
            </div>
        </div>

        <!-- Tarjeta de Soporte Presencial Inmediato -->
        <div class="bg-[#f5f4ec] border border-[#e4e3db] rounded-2xl p-6 shadow-sm text-xs text-[#404944] space-y-3">
            <div class="flex items-center gap-2 text-[#1b1c18] font-bold">
                <span class="material-symbols-outlined text-[20px] text-[#2f664f]">support_agent</span>
                <span>Soporte presencial inmediato</span>
            </div>
            <p class="leading-relaxed">
                Si se encuentra en plena clase o certamen tecnológico y requiere apoyo urgente en la sala, acuda directamente a la mesa de soporte técnico:
            </p>
            <div class="space-y-1 font-mono text-[11px] text-[#1b1c18] pt-1">
                <div><strong>Ubicación:</strong> Edificio E, planta baja · Cubículo E-104</div>
                <div><strong>Encargado de turno:</strong> Ing. Carlos Mendoza</div>
                <div><strong>Extensión telefónica:</strong> 1420 / 1422</div>
            </div>
        </div>

    </div>

</div>
@endsection
