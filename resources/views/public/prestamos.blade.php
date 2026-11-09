@extends('layouts.public')

@section('title', 'Préstamos en Ventanilla')

@section('header')
<div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-4 border-b border-[#e4e3db]">
    <div>
        <div class="flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-[#675a4e]">
            <span class="material-symbols-outlined text-[16px] text-[#2f664f]">inventory_2</span>
            <span>Pañol técnico · Cubículo E-102</span>
        </div>
        <h1 class="text-2xl sm:text-3xl font-extrabold text-[#1b1c18] mt-1 tracking-tight">
            Préstamos de Instrumental y Equipamiento en Ventanilla
        </h1>
        <p class="text-xs sm:text-sm text-[#404944] mt-1">
            Catálogo de instrumental de medición, osciloscopios, tarjetas embebidas, cableado de consola y equipos de campo disponibles para prácticas académicas.
        </p>
    </div>

    <!-- Ubicación de Ventanilla -->
    <div class="flex items-center gap-2 px-3 py-1.5 rounded-lg bg-[#ffffff] border border-[#e4e3db] text-xs font-mono">
        <span class="material-symbols-outlined text-[16px] text-[#2f664f]">storefront</span>
        <span class="text-[#1b1c18] font-bold">Ventanilla E-102 (planta baja)</span>
    </div>
</div>
@endsection

@section('content')
<!-- Protocolo de Préstamo Institucional en 4 Pasos -->
<div class="bg-[#ffffff] border border-[#e4e3db] rounded-2xl p-6 sm:p-8 mb-8 shadow-sm">
    <div class="flex items-center justify-between pb-4 border-b border-[#e4e3db]">
        <div>
            <span class="text-[11px] font-bold uppercase tracking-wider text-[#675a4e]">Procedimiento técnico reglamentario</span>
            <h2 class="text-lg font-bold text-[#1b1c18]">Protocolo de préstamo de instrumental y valija</h2>
        </div>
        <span class="text-xs font-bold text-[#2f664f] bg-[#efeee7] px-3 py-1 rounded-lg border border-[#e4e3db]">
            Turnos matutino y vespertino
        </span>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mt-6">
        <!-- Paso 1 -->
        <div class="p-4 bg-[#f5f4ec] rounded-xl border border-[#e4e3db] flex flex-col justify-between">
            <div>
                <div class="w-8 h-8 rounded-lg bg-[#2f664f] text-white flex items-center justify-center font-bold text-xs mb-3 shadow-sm">
                    1
                </div>
                <h3 class="text-sm font-bold text-[#1b1c18] mb-1">Identificación institucional</h3>
                <p class="text-xs text-[#404944] leading-relaxed">
                    Presentar credencial universitaria vigente de la UPTex. No se aceptan identificaciones ajenas ni fotografías digitales.
                </p>
            </div>
            <div class="mt-3 pt-2 border-t border-[#e4e3db] text-[11px] font-semibold text-[#675a4e]">
                Requisito: Alumno regular
            </div>
        </div>

        <!-- Paso 2 -->
        <div class="p-4 bg-[#f5f4ec] rounded-xl border border-[#e4e3db] flex flex-col justify-between">
            <div>
                <div class="w-8 h-8 rounded-lg bg-[#2f664f] text-white flex items-center justify-center font-bold text-xs mb-3 shadow-sm">
                    2
                </div>
                <h3 class="text-sm font-bold text-[#1b1c18] mb-1">Vale de resguardo</h3>
                <p class="text-xs text-[#404944] leading-relaxed">
                    Completar el vale físico en ventanilla E-102 indicando asignatura, docente titular, sala de uso y accesorios solicitados.
                </p>
            </div>
            <div class="mt-3 pt-2 border-t border-[#e4e3db] text-[11px] font-semibold text-[#675a4e]">
                Formato oficial F-TI-04
            </div>
        </div>

        <!-- Paso 3 -->
        <div class="p-4 bg-[#f5f4ec] rounded-xl border border-[#e4e3db] flex flex-col justify-between">
            <div>
                <div class="w-8 h-8 rounded-lg bg-[#2f664f] text-white flex items-center justify-center font-bold text-xs mb-3 shadow-sm">
                    3
                </div>
                <h3 class="text-sm font-bold text-[#1b1c18] mb-1">Verificación física</h3>
                <p class="text-xs text-[#404944] leading-relaxed">
                    Revisar el estado del equipo junto al técnico de ventanilla: sondas, cables, pantallas y calibración de instrumentos.
                </p>
            </div>
            <div class="mt-3 pt-2 border-t border-[#e4e3db] text-[11px] font-semibold text-[#316a42]">
                Inspección obligatoria
            </div>
        </div>

        <!-- Paso 4 -->
        <div class="p-4 bg-[#f5f4ec] rounded-xl border border-[#e4e3db] flex flex-col justify-between">
            <div>
                <div class="w-8 h-8 rounded-lg bg-[#2f664f] text-white flex items-center justify-center font-bold text-xs mb-3 shadow-sm">
                    4
                </div>
                <h3 class="text-sm font-bold text-[#1b1c18] mb-1">Devolución en turno</h3>
                <p class="text-xs text-[#404944] leading-relaxed">
                    Retornar el equipamiento limpio y ordenado antes del cierre de turno. La entrega tardía genera suspensión temporal del servicio.
                </p>
            </div>
            <div class="mt-3 pt-2 border-t border-[#e4e3db] text-[11px] font-semibold text-[#ba1a1a]">
                Sin excepciones
            </div>
        </div>
    </div>
</div>

<!-- Catálogo de Instrumental Disponible -->
<div class="bg-[#ffffff] border border-[#e4e3db] rounded-2xl overflow-hidden shadow-sm mb-8">
    <div class="px-6 py-4 bg-[#f5f4ec] border-b border-[#e4e3db] flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h2 class="text-base font-bold text-[#1b1c18]">Inventario físico en pañol para préstamo</h2>
            <p class="text-xs text-[#707973]">Consulte la disponibilidad de kits e instrumental antes de acudir a la ventanilla.</p>
        </div>
        <span class="text-xs font-mono text-[#316a42] bg-[#ffffff] px-3 py-1.5 rounded-lg border border-[#e4e3db] font-bold">
            {{ count($equiposDisponibles) }} artículos disponibles
        </span>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left text-xs">
            <thead class="bg-[#efeee7] text-[#1b1c18] uppercase tracking-wider font-bold h-10 border-b border-[#e4e3db]">
                <tr>
                    <th class="px-6 w-36">Código de activo</th>
                    <th class="px-4">Instrumento / equipo</th>
                    <th class="px-4">Marca / modelo</th>
                    <th class="px-4">Accesorios incluidos / observaciones</th>
                    <th class="px-6 text-right">Disponibilidad</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#e4e3db]">
                @forelse($equiposDisponibles as $eq)
                    <tr class="h-14 hover:bg-[#fbf9f2] transition-colors">
                        <td class="px-6 font-mono font-bold text-[#1b1c18]">
                            <span class="bg-[#f5f4ec] px-2 py-1 rounded border border-[#c0c9c2]">
                                {{ $eq->codigo_inventario }}
                            </span>
                        </td>
                        <td class="px-4">
                            <span class="font-bold text-[#1b1c18] block">{{ $eq->nombre }}</span>
                            <span class="text-[11px] text-[#707973]">{{ $eq->categoria->nombre ?? 'Instrumentación' }}</span>
                        </td>
                        <td class="px-4 font-semibold text-[#404944]">
                            <span>{{ $eq->marca }} {{ $eq->modelo }}</span>
                            <span class="block text-[10px] text-[#707973] font-mono">S/N: {{ $eq->numero_serie }}</span>
                        </td>
                        <td class="px-4 text-[#404944] max-w-xs">
                            {{ $eq->observaciones ?: 'Equipo revisado y funcional en pañol.' }}
                        </td>
                        <td class="px-6 text-right">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-[#b3f1be]/40 text-[#16512c] font-bold text-[11px]">
                                <span class="w-1.5 h-1.5 rounded-full bg-[#316a42]"></span>
                                Disponible en pañol
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-[#707973]">
                            No hay equipamiento registrado para préstamo en este momento.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Normativa y Responsabilidad de Resguardo -->
<div class="bg-[#f5f4ec] border border-[#e4e3db] rounded-2xl p-6 sm:p-8 flex items-start gap-4 shadow-sm">
    <div class="w-10 h-10 rounded-xl bg-[#efeee7] border border-[#e4e3db] flex items-center justify-center text-[#ba1a1a] shrink-0 mt-0.5">
        <span class="material-symbols-outlined text-[24px]">gavel</span>
    </div>
    <div class="text-xs text-[#404944] space-y-1.5 leading-relaxed">
        <h4 class="font-bold text-[#1b1c18] text-sm">Responsabilidad Patrimonial del Solicitante</h4>
        <p>
            El alumno o docente que firma el vale de resguardo asume la custodia directa y responsabilidad legal del equipo hasta su devolución formal. En caso de daño físico por negligencia, caída, derrame de líquidos o extravío de componentes, el solicitante deberá reponer el artículo con idénticas características técnicas en un plazo no mayor a 10 días hábiles.
        </p>
    </div>
</div>
@endsection
