@extends('layouts.private')

@section('title', 'Panel de Control Operativo')

@section('header-title', 'Panel de Control Operativo · Ing. Carlos Mendoza')

@section('header-subtitle')
    <span>Jefatura de Laboratorios de TI</span>
    <span class="text-[#c0c9c2]">/</span>
    <span>Turno Mixto</span>
    <span class="text-[#c0c9c2]">/</span>
    <span>Edificio E, Planta Baja</span>
    <span class="text-[#c0c9c2]">/</span>
    <span class="text-[#2f664f] font-medium flex items-center gap-1">
        <span class="material-symbols-outlined text-[16px]">sync</span>
        Sincronizado 09:42 hrs
    </span>
@endsection

@section('actions')
    <button type="button"
            class="h-10 px-4 rounded-lg bg-[#ffffff] text-[#1b1c18] font-medium text-sm hover:bg-[#efeee7] border border-[#e4e3db] transition-colors flex items-center gap-2">
        <span class="material-symbols-outlined text-[18px]">lock</span>
        <span>Bloqueo Masivo</span>
    </button>

    <button type="button"
            class="h-10 px-4 rounded-lg bg-[#2f664f] hover:bg-[#497f67] text-white font-medium text-sm transition-colors flex items-center gap-2 shadow-sm">
        <span class="material-symbols-outlined text-[18px]">add_circle</span>
        <span>Nueva Solicitud</span>
    </button>
@endsection

@section('content')
<!-- Fila de 4 KPIs Administrativos -->
<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-6 mb-6">
    <!-- KPI 1: Ocupación Global -->
    <div class="bg-[#ffffff] border border-[#e4e3db] rounded-xl p-5 flex flex-col justify-between">
        <div class="flex items-center justify-between mb-2">
            <span class="text-[11px] font-bold uppercase tracking-wider text-[#707973]">Capacidad Operativa</span>
            <span class="w-8 h-8 rounded-lg bg-[#efeee7] flex items-center justify-center text-[#2f664f]">
                <span class="material-symbols-outlined text-[20px]">pie_chart</span>
            </span>
        </div>
        <div>
            <span class="text-sm font-bold text-[#1b1c18] block mb-1">Ocupación Global</span>
            <div class="flex items-baseline gap-2">
                <span class="text-3xl font-extrabold text-[#1b1c18] tracking-tight">82%</span>
                <span class="text-xs font-semibold text-[#316a42]">+4.2% vs ayer</span>
            </div>
        </div>
        <div class="mt-4 pt-2 border-t border-[#e4e3db]">
            <span class="text-xs text-[#404944] flex items-center gap-1">
                <span class="material-symbols-outlined text-[16px] text-[#2f664f]">domain_verification</span>
                5 de 6 laboratorios activos con clase
            </span>
        </div>
    </div>

    <!-- KPI 2: Parque Computacional -->
    <div class="bg-[#ffffff] border border-[#e4e3db] rounded-xl p-5 flex flex-col justify-between">
        <div class="flex items-center justify-between mb-2">
            <span class="text-[11px] font-bold uppercase tracking-wider text-[#707973]">Parque Computacional</span>
            <span class="px-2 py-0.5 rounded text-[11px] font-bold bg-[#b3f1be]/40 text-[#16512c] flex items-center gap-1">
                <span class="w-1.5 h-1.5 rounded-full bg-[#316a42]"></span>
                96.6% DISP
            </span>
        </div>
        <div>
            <span class="text-sm font-bold text-[#1b1c18] block mb-1">Equipos Operativos</span>
            <div class="flex items-baseline gap-2">
                <span class="text-3xl font-extrabold text-[#1b1c18] tracking-tight">142</span>
                <span class="text-xs text-[#707973]">/ 180 activos</span>
            </div>
        </div>
        <div class="mt-4 pt-2 border-t border-[#e4e3db]">
            <span class="text-xs text-[#675a4e] flex items-center gap-1">
                <span class="material-symbols-outlined text-[16px]">build</span>
                6 equipos en taller técnico preventivo
            </span>
        </div>
    </div>

    <!-- KPI 3: Ventanilla de Préstamos -->
    <div class="bg-[#ffffff] border border-[#e4e3db] rounded-xl p-5 flex flex-col justify-between">
        <div class="flex items-center justify-between mb-2">
            <span class="text-[11px] font-bold uppercase tracking-wider text-[#707973]">Ventanilla de Préstamos</span>
            <span class="w-8 h-8 rounded-lg bg-[#efeee7] flex items-center justify-center text-[#2f664f]">
                <span class="material-symbols-outlined text-[20px]">inventory_2</span>
            </span>
        </div>
        <div>
            <span class="text-sm font-bold text-[#1b1c18] block mb-1">Hardware en Préstamo</span>
            <div class="flex items-baseline gap-2">
                <span class="text-3xl font-extrabold text-[#1b1c18] tracking-tight">12</span>
                <span class="text-xs text-[#404944]">vales firmados</span>
            </div>
        </div>
        <div class="mt-4 pt-2 border-t border-[#e4e3db]">
            <span class="text-xs text-[#404944] flex items-center gap-1">
                <span class="material-symbols-outlined text-[16px] text-[#2f664f]">schedule</span>
                Próxima entrega: 11:30 hrs (Lab Redes 2)
            </span>
        </div>
    </div>

    <!-- KPI 4: Mesa de Incidencias -->
    <div class="bg-[#ffffff] border border-[#e4e3db] rounded-xl p-5 flex flex-col justify-between">
        <div class="flex items-center justify-between mb-2">
            <span class="text-[11px] font-bold uppercase tracking-wider text-[#707973]">Mesa de Soporte</span>
            <span class="w-8 h-8 rounded-lg bg-[#ffdad6] flex items-center justify-center text-[#ba1a1a]">
                <span class="material-symbols-outlined text-[20px]">confirmation_number</span>
            </span>
        </div>
        <div>
            <span class="text-sm font-bold text-[#1b1c18] block mb-1">Tickets Pendientes</span>
            <div class="flex items-baseline gap-2">
                <span class="text-3xl font-extrabold text-[#ba1a1a] tracking-tight">4</span>
                <span class="text-xs text-[#707973]">1 crítico</span>
            </div>
        </div>
        <div class="mt-4 pt-2 border-t border-[#e4e3db]">
            <span class="text-xs text-[#ba1a1a] flex items-center gap-1 font-medium">
                <span class="material-symbols-outlined text-[16px]">priority_high</span>
                Falla en Switch B de Laboratorio L-01
            </span>
        </div>
    </div>
</div>

<!-- Tabla Operativa de Estado de Laboratorios -->
<div class="bg-[#ffffff] border border-[#e4e3db] rounded-xl overflow-hidden mb-6">
    <div class="px-5 py-4 border-b border-[#e4e3db] flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h2 class="text-base font-bold text-[#1b1c18]">Estado en Tiempo Real por Sala / Laboratorio</h2>
            <p class="text-xs text-[#707973]">Monitoreo de telemetría de red, asignaturas en curso y terminales concurrentes.</p>
        </div>
        <span class="text-xs font-mono text-[#404944] bg-[#f5f4ec] px-3 py-1.5 rounded-lg border border-[#e4e3db]">
            VLAN 10 / Red Troncal 10.100.0.0/16
        </span>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left text-xs">
            <thead class="bg-[#f5f4ec] text-[#1b1c18] uppercase tracking-wider font-bold h-10 border-b border-[#e4e3db]">
                <tr>
                    <th class="px-5">Sala / Laboratorio</th>
                    <th class="px-4">Asignatura / Docente</th>
                    <th class="px-4">Capacidad</th>
                    <th class="px-4">Estado</th>
                    <th class="px-4">Switch de Borde</th>
                    <th class="px-5 text-right">Acciones Rápidas</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#e4e3db]">
                <tr class="h-14 hover:bg-[#fbf9f2] transition-colors">
                    <td class="px-5 font-bold text-[#1b1c18]">
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-[#2f664f] text-[18px]">dns</span>
                            <span>Laboratorio L-01</span>
                        </div>
                        <span class="text-[11px] text-[#707973] font-normal">Redes Cisco y Enrutamiento</span>
                    </td>
                    <td class="px-4 text-[#404944]">
                        <span class="font-semibold text-[#1b1c18] block">Enrutamiento Avanzado (ITI-801)</span>
                        <span>Mtro. Roberto Hernández · 09:00 - 11:00</span>
                    </td>
                    <td class="px-4 font-mono font-medium text-[#1b1c18]">28 / 30 ocupadas</td>
                    <td class="px-4">
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded bg-[#efeee7] text-[#1b1c18] font-bold text-[10px]">
                            <span class="material-symbols-outlined text-[13px]">lock</span> EN CLASE
                        </span>
                    </td>
                    <td class="px-4 font-mono text-[11px] text-[#707973]">SW-CISCO-L01A (Gigabit)</td>
                    <td class="px-5 text-right">
                        <a href="{{ url('/malla-topologica?lab=L01') }}" class="inline-flex items-center gap-1 px-3 py-1.5 rounded bg-[#efeee7] hover:bg-[#e4e3db] text-[#1b1c18] font-medium text-xs transition-colors">
                            <span class="material-symbols-outlined text-[15px]">grid_view</span> Ver Malla
                        </a>
                    </td>
                </tr>

                <tr class="h-14 hover:bg-[#fbf9f2] transition-colors">
                    <td class="px-5 font-bold text-[#1b1c18]">
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-[#2f664f] text-[18px]">terminal</span>
                            <span>Laboratorio L-04</span>
                        </div>
                        <span class="text-[11px] text-[#707973] font-normal">Cómputo Especializado e IA</span>
                    </td>
                    <td class="px-4 text-[#404944]">
                        <span class="font-semibold text-[#316a42] block">Práctica Libre Tutelada</span>
                        <span>Ing. Carlos Mendoza (Encargado)</span>
                    </td>
                    <td class="px-4 font-mono font-medium text-[#1b1c18]">14 / 30 ocupadas</td>
                    <td class="px-4">
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded bg-[#b3f1be]/40 text-[#16512c] font-bold text-[10px]">
                            <span class="w-1.5 h-1.5 rounded-full bg-[#316a42]"></span> LIBRE / PRÁCTICA
                        </span>
                    </td>
                    <td class="px-4 font-mono text-[11px] text-[#707973]">SW-CISCO-L04-AI</td>
                    <td class="px-5 text-right">
                        <a href="{{ url('/malla-topologica?lab=L04') }}" class="inline-flex items-center gap-1 px-3 py-1.5 rounded bg-[#2f664f] hover:bg-[#497f67] text-white font-medium text-xs transition-colors">
                            <span class="material-symbols-outlined text-[15px]">grid_view</span> Monitorear
                        </a>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection
