@extends('layouts.public')

@section('title', 'Inicio y Servicios Tecnológicos')

@section('content')
<!-- Hero Institucional Sobrio -->
<section class="w-full bg-[#ffffff] border border-[#e4e3db] py-8 px-6 sm:px-8 rounded-2xl mb-6 shadow-sm">
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
        <div class="max-w-3xl flex flex-col gap-3">
            <div class="inline-flex items-center gap-1.5 self-start px-3 py-1 bg-[#efeee7] rounded text-xs font-bold uppercase tracking-wider text-[#675a4e]">
                <span class="material-symbols-outlined text-[16px] text-[#2f664f]">lan</span>
                División de Tecnologías de la Información · Universidad Politécnica de Texcoco
            </div>

            <h1 class="text-3xl sm:text-4xl font-extrabold text-[#1b1c18] tracking-tight">
                Infraestructura de Cómputo y Laboratorios de TI
            </h1>

            <p class="text-base text-[#404944] max-w-2xl leading-relaxed">
                Plataforma centralizada de monitoreo y acceso técnico para la comunidad universitaria de la UPTex. Soporte de alta disponibilidad orientado a la docencia especializada, investigación aplicada, certámenes tecnológicos y prácticas de ingeniería.
            </p>

            <div class="flex flex-wrap items-center gap-3 pt-2">
                <a href="{{ url('/salas') }}"
                   class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#2f664f] hover:bg-[#497f67] text-white rounded-lg font-medium text-sm transition-colors shadow-sm">
                    <span class="material-symbols-outlined text-[18px]">sensors</span>
                    <span>Consultar salas disponibles</span>
                </a>

                <a href="{{ url('/reglamento') }}"
                   class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#ffffff] hover:bg-[#efeee7] border border-[#e4e3db] text-[#1b1c18] rounded-lg font-medium text-sm transition-colors">
                    <span class="material-symbols-outlined text-[18px] text-[#707973]">gavel</span>
                    <span>Ver reglamento oficial</span>
                </a>
            </div>
        </div>

        <!-- Telemetría Operativa Compacta -->
        <div class="flex flex-col gap-3 p-4 bg-[#f5f4ec] rounded-xl border border-[#e4e3db] w-full lg:w-80 shrink-0">
            <div class="flex items-center justify-between pb-2 border-b border-[#e4e3db]">
                <span class="text-xs font-bold uppercase tracking-wider text-[#675a4e]">Telemetría de servidores</span>
                <span class="inline-flex items-center gap-1.5 text-xs text-[#316a42] font-bold">
                    <span class="w-2 h-2 rounded-full bg-[#316a42] animate-pulse"></span>
                    En línea
                </span>
            </div>

            <div class="space-y-2 text-xs font-mono">
                <div class="flex justify-between items-center">
                    <span class="text-[#404944]">Latencia del gateway:</span>
                    <span class="font-bold text-[#1b1c18]">3.4 ms</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-[#404944]">Conectividad troncal:</span>
                    <span class="font-bold text-[#1b1c18]">10 Gbps Fibra</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-[#404944]">Sincronización NTP:</span>
                    <span class="font-bold text-[#1b1c18]">{{ date('H:i:s') }} CST</span>
                </div>
            </div>

            <div class="pt-2 border-t border-[#e4e3db]">
                <div class="flex items-center gap-1.5 text-[#404944] text-[11px]">
                    <span class="material-symbols-outlined text-[16px] text-[#316a42]">verified</span>
                    <span>Sistemas validados bajo norma ISO/IEC 27001</span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Fila de 4 KPIs Públicos Planos -->
<section class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6 mb-8">
    <!-- KPI 1 -->
    <div class="bg-[#ffffff] border border-[#e4e3db] rounded-xl p-5 flex flex-col justify-between shadow-sm">
        <div>
            <div class="flex items-center justify-between">
                <span class="text-[11px] uppercase tracking-wider text-[#675a4e] font-bold">Infraestructura física</span>
                <span class="material-symbols-outlined text-[#2f664f] text-[22px]">domain</span>
            </div>
            <div class="text-2xl font-extrabold text-[#1b1c18] mt-1">{{ count($laboratorios) }} salas activas</div>
            <p class="text-xs text-[#404944] mt-1 leading-relaxed">
                Redes, Software, IA, IoT y Cómputo General A y B con climatización controlada.
            </p>
        </div>
        <div class="mt-4 pt-2 border-t border-[#e4e3db] flex items-center justify-between text-xs text-[#707973]">
            <span>Edificios E y F</span>
            <span class="text-[#2f664f] font-bold">100% operativas</span>
        </div>
    </div>

    <!-- KPI 2 -->
    <div class="bg-[#ffffff] border border-[#e4e3db] rounded-xl p-5 flex flex-col justify-between shadow-sm">
        <div>
            <div class="flex items-center justify-between">
                <span class="text-[11px] uppercase tracking-wider text-[#675a4e] font-bold">Nodos computacionales</span>
                <span class="material-symbols-outlined text-[#2f664f] text-[22px]">desktop_windows</span>
            </div>
            <div class="text-2xl font-extrabold text-[#1b1c18] mt-1">{{ $totalEstaciones > 0 ? $totalEstaciones : 180 }} estaciones</div>
            <p class="text-xs text-[#404944] mt-1 leading-relaxed">
                Dell OptiPlex, Precision e iMacs enlazadas en red LAN Gigabit certificada.
            </p>
        </div>
        <div class="mt-4 pt-2 border-t border-[#e4e3db] flex items-center justify-between text-xs text-[#707973]">
            <span>Arranque dual UEFI</span>
            <span class="text-[#316a42] font-bold">Sin anomalías</span>
        </div>
    </div>

    <!-- KPI 3: Destacado Disponibilidad -->
    <div class="bg-[#ffffff] border border-[#e4e3db] rounded-xl p-5 flex flex-col justify-between shadow-sm">
        <div>
            <div class="flex items-center justify-between">
                <span class="text-[11px] uppercase tracking-wider text-[#675a4e] font-bold">Capacidad inmediata</span>
                <span class="material-symbols-outlined text-[#316a42] text-[22px]">event_seat</span>
            </div>
            <div class="flex items-baseline gap-2 mt-1">
                <div class="text-2xl font-extrabold text-[#1b1c18]">42</div>
                <span class="text-xs text-[#404944]">libres en este instante</span>
            </div>
            <div class="inline-flex items-center gap-1.5 px-2.5 py-1 mt-2 bg-[#b3f1be]/40 text-[#16512c] rounded text-xs font-bold">
                <span class="w-1.5 h-1.5 rounded-full bg-[#316a42]"></span>
                <span>Práctica libre autorizada</span>
            </div>
        </div>
        <div class="mt-4 pt-2 border-t border-[#e4e3db] flex items-center justify-between text-xs text-[#707973]">
            <span>Acceso con credencial</span>
            <span class="text-[#1b1c18] font-bold">Tiempo real</span>
        </div>
    </div>

    <!-- KPI 4 -->
    <div class="bg-[#ffffff] border border-[#e4e3db] rounded-xl p-5 flex flex-col justify-between shadow-sm">
        <div>
            <div class="flex items-center justify-between">
                <span class="text-[11px] uppercase tracking-wider text-[#675a4e] font-bold">Régimen de operación</span>
                <span class="material-symbols-outlined text-[#2f664f] text-[22px]">schedule</span>
            </div>
            <div class="text-2xl font-extrabold text-[#1b1c18] mt-1">07:00 – 20:00</div>
            <p class="text-xs text-[#404944] mt-1 leading-relaxed">
                Lunes a sábado en jornada continua. Asistencia técnica presencial permanente.
            </p>
        </div>
        <div class="mt-4 pt-2 border-t border-[#e4e3db] flex items-center justify-between text-xs text-[#707973]">
            <span>Ventanilla E-102</span>
            <span class="text-[#1b1c18] font-bold">Turnos matutino y vespertino</span>
        </div>
    </div>
</section>

<!-- Mosaico de Salas Públicas (Monitoreo de Ocupación) -->
<section class="mb-8" id="salas-en-vivo">
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-2 pb-3 border-b border-[#e4e3db] mb-6">
        <div>
            <div class="text-[11px] font-bold uppercase tracking-wider text-[#675a4e]">Monitor de ocupación en tiempo real</div>
            <h2 class="text-2xl font-bold text-[#1b1c18]">Estado de laboratorios de cómputo</h2>
        </div>
        <div class="flex items-center gap-4 text-xs">
            <span class="flex items-center gap-1.5 text-[#404944]">
                <span class="w-2.5 h-2.5 rounded-full bg-[#316a42]"></span>
                Práctica libre
            </span>
            <span class="flex items-center gap-1.5 text-[#404944]">
                <span class="w-2.5 h-2.5 rounded-full bg-[#2f664f]"></span>
                Clase programada
            </span>
            <span class="flex items-center gap-1.5 text-[#404944]">
                <span class="w-2.5 h-2.5 rounded-full bg-[#675a4e]"></span>
                Taller especializado
            </span>
        </div>
    </div>

    <!-- Grid de 4 laboratorios destacados -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        @foreach($laboratoriosDetallados as $lab)
            <article class="bg-[#ffffff] border border-[#e4e3db] rounded-2xl p-6 flex flex-col justify-between shadow-sm">
                <div>
                    <!-- Encabezado de la Sala -->
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <span class="text-[11px] font-bold uppercase tracking-wider text-[#675a4e]">{{ $lab['ubicacion'] }}</span>
                            <h3 class="text-lg font-bold text-[#1b1c18] mt-0.5">{{ $lab['nombre'] }}</h3>
                        </div>
                        <span class="shrink-0 inline-flex items-center gap-1 px-2.5 py-1 rounded text-xs font-bold {{ $lab['badge_color'] }}">
                            <span class="w-2 h-2 rounded-full {{ $lab['dot_color'] }}"></span>
                            {{ $lab['estado_badge'] }}
                        </span>
                    </div>

                    <!-- Métrica de Ocupación con Barra de Progreso Plana -->
                    <div class="mt-4 p-4 bg-[#f5f4ec] rounded-xl border border-[#e4e3db]">
                        <div class="flex items-center justify-between text-xs sm:text-sm font-semibold">
                            <span class="text-[#1b1c18]">Aforo ocupado: {{ $lab['aforo_ocupado'] }} de {{ $lab['aforo_total'] }} estaciones</span>
                            <span class="font-bold {{ $lab['porcentaje'] > 75 ? 'text-[#2f664f]' : 'text-[#316a42]' }} font-mono">{{ $lab['porcentaje'] }}%</span>
                        </div>
                        <div class="w-full h-2.5 bg-[#e4e3db] rounded-full overflow-hidden mt-2">
                            <div class="h-full {{ $lab['porcentaje'] > 75 ? 'bg-[#2f664f]' : 'bg-[#316a42]' }} rounded-full" style="width: {{ $lab['porcentaje'] }}%;"></div>
                        </div>
                        <div class="flex justify-between items-center text-[11px] text-[#707973] mt-2">
                            <span>{{ $lab['reserva_info'] }}</span>
                            <span class="font-medium text-[#1b1c18]">Monitoreo de red LAN</span>
                        </div>
                    </div>

                    <!-- Metadatos de la Sesión en Curso -->
                    <div class="mt-4 grid grid-cols-1 sm:grid-cols-3 gap-3 py-3 border-y border-[#e4e3db] text-xs">
                        <div>
                            <span class="block text-[10px] uppercase font-bold text-[#675a4e]">Asignatura / actividad</span>
                            <span class="font-medium text-[#1b1c18]">{{ $lab['asignatura'] }}</span>
                        </div>
                        <div>
                            <span class="block text-[10px] uppercase font-bold text-[#675a4e]">Docente / encargado</span>
                            <span class="font-medium text-[#1b1c18]">{{ $lab['docente'] }}</span>
                        </div>
                        <div>
                            <span class="block text-[10px] uppercase font-bold text-[#675a4e]">Grupo / modalidad</span>
                            <span class="font-medium text-[#1b1c18]">{{ $lab['grupo'] }}</span>
                        </div>
                    </div>

                    <!-- Ficha Técnica de Hardware & Software -->
                    <details class="mt-3 group" open>
                        <summary class="flex items-center justify-between cursor-pointer text-xs font-bold text-[#2f664f] uppercase tracking-wider select-none py-1">
                            <span>Especificación de hardware y software base</span>
                            <span class="material-symbols-outlined text-[18px] group-open:rotate-180 transition-transform">expand_more</span>
                        </summary>
                        <div class="mt-2 p-3 bg-[#f5f4ec] rounded-lg text-xs text-[#404944] space-y-1.5 border border-[#e4e3db]">
                            <div class="flex items-start gap-2">
                                <span class="material-symbols-outlined text-[16px] text-[#675a4e] shrink-0 mt-0.5">memory</span>
                                <span><strong class="text-[#1b1c18]">Hardware:</strong> {{ $lab['hardware'] }}</span>
                            </div>
                            <div class="flex items-start gap-2">
                                <span class="material-symbols-outlined text-[16px] text-[#675a4e] shrink-0 mt-0.5">terminal</span>
                                <span><strong class="text-[#1b1c18]">Software:</strong> {{ $lab['software'] }}</span>
                            </div>
                        </div>
                    </details>
                </div>

                <!-- Pie de la Tarjeta con Acción -->
                <div class="mt-6 pt-3 border-t border-[#e4e3db] flex items-center justify-between text-xs">
                    <span class="text-[#707973]">{{ $lab['proximo_cambio'] }}</span>
                    <a href="{{ url('/salas') }}"
                       class="px-3.5 py-1.5 bg-[#efeee7] hover:bg-[#e4e3db] text-[#1b1c18] rounded-lg font-semibold transition-colors flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-[16px]">visibility</span>
                        <span>Ver detalles de la sala</span>
                    </a>
                </div>
            </article>
        @endforeach
    </div>
</section>

<!-- Sección de Avisos Oficiales del Administrador -->
<section class="bg-[#ffffff] border border-[#e4e3db] rounded-2xl p-6 sm:p-8 shadow-sm">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-4 border-b border-[#e4e3db] gap-3">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-[#efeee7] border border-[#e4e3db] flex items-center justify-center text-[#2f664f] shrink-0">
                <span class="material-symbols-outlined text-[24px]">campaign</span>
            </div>
            <div>
                <h3 class="text-lg font-bold text-[#1b1c18]">Tablón técnico de avisos oficiales</h3>
                <span class="text-xs text-[#404944]">Emitido por: Ing. Carlos Mendoza · Jefatura de Operaciones de Cómputo</span>
            </div>
        </div>
        <span class="text-xs font-mono text-[#707973] bg-[#f5f4ec] px-3 py-1 rounded-lg border border-[#e4e3db]">
            Periodo cuatrimestral 2026-1
        </span>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
        <!-- Aviso 1: Mantenimiento -->
        <div class="p-5 bg-[#f5f4ec] rounded-xl border border-[#e4e3db] flex flex-col justify-between">
            <div class="flex flex-col gap-2">
                <div class="flex items-center justify-between">
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-[#e4e3db] text-[#1b1c18] rounded text-xs font-bold">
                        <span class="material-symbols-outlined text-[14px] text-[#675a4e]">build</span>
                        Mantenimiento programado
                    </span>
                    <span class="text-xs font-mono text-[#707973]">Sábado · 14:00 a 18:00 h</span>
                </div>
                <h4 class="text-base font-bold text-[#1b1c18] mt-1">Despliegue de imágenes UEFI en el Laboratorio de Cómputo General A</h4>
                <p class="text-xs text-[#404944] leading-relaxed">
                    Se llevará a cabo el despliegue masivo de imágenes con Ubuntu 22.04 LTS y Windows 11 para 30 terminales. Los usuarios con reservación en ese horario serán reubicados sin penalización al Laboratorio L-02.
                </p>
            </div>
            <div class="mt-4 pt-3 border-t border-[#e4e3db] flex items-center justify-between text-xs">
                <span class="text-[#675a4e] font-medium">Impacto estimado: Bajo</span>
                <a href="{{ url('/salas') }}" class="text-[#2f664f] hover:underline font-bold inline-flex items-center gap-0.5">
                    <span>Ver reasignación de salas</span>
                    <span class="material-symbols-outlined text-[14px]">arrow_forward</span>
                </a>
            </div>
        </div>

        <!-- Aviso 2: Licencias MATLAB -->
        <div class="p-5 bg-[#f5f4ec] rounded-xl border border-[#e4e3db] flex flex-col justify-between">
            <div class="flex flex-col gap-2">
                <div class="flex items-center justify-between">
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-[#b3f1be]/50 text-[#16512c] rounded text-xs font-bold">
                        <span class="material-symbols-outlined text-[14px] text-[#316a42]">verified_user</span>
                        Licenciamiento institucional
                    </span>
                    <span class="text-xs font-mono text-[#707973]">Vigencia 2026</span>
                </div>
                <h4 class="text-base font-bold text-[#1b1c18] mt-1">Renovación de MATLAB R2024b, JetBrains y Cisco Academy</h4>
                <p class="text-xs text-[#404944] leading-relaxed">
                    Las suites de cálculo numérico MATLAB y Simulink, así como los entornos de desarrollo IntelliJ IDEA y Cisco Packet Tracer, han sido actualizados y renovados. Los estudiantes pueden activar su copia individual con el correo institucional @uptex.edu.mx.
                </p>
            </div>
            <div class="mt-4 pt-3 border-t border-[#e4e3db] flex items-center justify-between text-xs">
                <span class="text-[#316a42] font-semibold">Disponible de inmediato</span>
                <a href="{{ url('/software') }}" class="text-[#2f664f] hover:underline font-bold inline-flex items-center gap-0.5">
                    <span>Catálogo de software</span>
                    <span class="material-symbols-outlined text-[14px]">arrow_forward</span>
                </a>
            </div>
        </div>
    </div>
</section>
@endsection
