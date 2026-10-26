@extends('layouts.public')

@section('title', 'Inicio y Servicios Tecnológicos')

@section('content')
<!-- Hero Institucional Sobrio -->
<section class="w-full bg-[#ffffff] border border-[#e4e3db] py-8 px-6 sm:px-8 rounded-xl mb-6">
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
                    Consultar Salas Disponibles
                </a>
                
                <a href="{{ url('/reglamento') }}"
                   class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#ffffff] hover:bg-[#efeee7] border border-[#e4e3db] text-[#1b1c18] rounded-lg font-medium text-sm transition-colors">
                    <span class="material-symbols-outlined text-[18px] text-[#707973]">gavel</span>
                    Ver Reglamento Oficial
                </a>
            </div>
        </div>

        <!-- Telemetría Operativa Compacta -->
        <div class="flex flex-col gap-3 p-4 bg-[#f5f4ec] rounded-xl border border-[#e4e3db] w-full lg:w-80 shrink-0">
            <div class="flex items-center justify-between pb-2 border-b border-[#e4e3db]">
                <span class="text-xs font-bold uppercase tracking-wider text-[#675a4e]">Telemetría de Servidores</span>
                <span class="inline-flex items-center gap-1.5 text-xs text-[#316a42] font-bold">
                    <span class="w-2 h-2 rounded-full bg-[#316a42] animate-pulse"></span>
                    En Línea
                </span>
            </div>
            
            <div class="space-y-2 text-xs font-mono">
                <div class="flex justify-between items-center">
                    <span class="text-[#404944]">Latencia Gateway:</span>
                    <span class="font-bold text-[#1b1c18]">3.4 ms</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-[#404944]">Backbone:</span>
                    <span class="font-bold text-[#1b1c18]">10 Gbps Fibra</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-[#404944]">Sincronización NTP:</span>
                    <span class="font-bold text-[#1b1c18]">07:22:15 CST</span>
                </div>
            </div>

            <div class="pt-2 border-t border-[#e4e3db]">
                <div class="flex items-center gap-1.5 text-[#404944] text-[11px]">
                    <span class="material-symbols-outlined text-[16px] text-[#316a42]">verified</span>
                    Sistemas validados bajo norma ISO/IEC 27001
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Fila de 4 KPIs Públicos Planos -->
<section class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6 mb-6">
    <!-- KPI 1 -->
    <div class="bg-[#ffffff] border border-[#e4e3db] rounded-xl p-5 flex flex-col justify-between">
        <div>
            <div class="flex items-center justify-between">
                <span class="text-[11px] uppercase tracking-wider text-[#675a4e] font-bold">Infraestructura Física</span>
                <span class="material-symbols-outlined text-[#2f664f] text-[22px]">domain</span>
            </div>
            <div class="text-2xl font-extrabold text-[#1b1c18] mt-1">6 Salas Activas</div>
            <p class="text-xs text-[#404944] mt-1">Redes, Software, IA, IoT y Cómputo General A & B con clima controlado.</p>
        </div>
        <div class="mt-4 pt-2 border-t border-[#e4e3db] flex items-center justify-between text-xs text-[#707973]">
            <span>Edificios E y F</span>
            <span class="text-[#2f664f] font-bold">100% Operativas</span>
        </div>
    </div>

    <!-- KPI 2 -->
    <div class="bg-[#ffffff] border border-[#e4e3db] rounded-xl p-5 flex flex-col justify-between">
        <div>
            <div class="flex items-center justify-between">
                <span class="text-[11px] uppercase tracking-wider text-[#675a4e] font-bold">Nodos Computacionales</span>
                <span class="material-symbols-outlined text-[#2f664f] text-[22px]">desktop_windows</span>
            </div>
            <div class="text-2xl font-extrabold text-[#1b1c18] mt-1">180 Estaciones</div>
            <p class="text-xs text-[#404944] mt-1">Dell Precision e iMacs enlazadas en red LAN Gigabit certificada.</p>
        </div>
        <div class="mt-4 pt-2 border-t border-[#e4e3db] flex items-center justify-between text-xs text-[#707973]">
            <span>Dual Boot UEFI</span>
            <span class="text-[#316a42] font-bold">Sin anomalías</span>
        </div>
    </div>

    <!-- KPI 3 -->
    <div class="bg-[#ffffff] border border-[#e4e3db] rounded-xl p-5 flex flex-col justify-between">
        <div>
            <div class="flex items-center justify-between">
                <span class="text-[11px] uppercase tracking-wider text-[#675a4e] font-bold">Capacidad Inmediata</span>
                <span class="material-symbols-outlined text-[#316a42] text-[22px]">event_seat</span>
            </div>
            <div class="flex items-baseline gap-2 mt-1">
                <div class="text-2xl font-extrabold text-[#1b1c18]">42</div>
                <span class="text-xs text-[#404944]">libres en este instante</span>
            </div>
            <div class="inline-flex items-center gap-1.5 px-2 py-0.5 mt-2 bg-[#b3f1be]/40 text-[#16512c] rounded text-xs font-bold">
                <span class="w-1.5 h-1.5 rounded-full bg-[#316a42]"></span>
                Acceso libre con credencial
            </div>
        </div>
        <div class="mt-4 pt-2 border-t border-[#e4e3db] flex items-center justify-between text-xs text-[#707973]">
            <span>L-01, L-03, L-05</span>
            <span class="text-[#316a42] font-bold">Práctica No Tutelada</span>
        </div>
    </div>

    <!-- KPI 4 -->
    <div class="bg-[#ffffff] border border-[#e4e3db] rounded-xl p-5 flex flex-col justify-between">
        <div>
            <div class="flex items-center justify-between">
                <span class="text-[11px] uppercase tracking-wider text-[#675a4e] font-bold">Paquetes de Software</span>
                <span class="material-symbols-outlined text-[#2f664f] text-[22px]">layers</span>
            </div>
            <div class="text-2xl font-extrabold text-[#1b1c18] mt-1">38 Títulos</div>
            <p class="text-xs text-[#404944] mt-1">Matlab, Cisco Packet Tracer, Docker, IntelliJ, Vivado, Android Studio.</p>
        </div>
        <div class="mt-4 pt-2 border-t border-[#e4e3db] flex items-center justify-between text-xs text-[#707973]">
            <span>Licencias Campus</span>
            <span class="text-[#2f664f] font-bold">Versiones 2026</span>
        </div>
    </div>
</section>
@endsection
