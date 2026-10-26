<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="shell-type" content="web_standard">

    <title>@yield('title', 'Consola de Gestión Operativa') · UPTex TI</title>

    <!-- Fuentes Oficiales: Work Sans & Google Material Symbols Outlined -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Work+Sans:wght@400;500;600;700;800&family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&display=swap" rel="stylesheet">

    <!-- Estilos base y tokens institucionales -->
    <style>
        @layer base {
            html, body {
                margin: 0;
                padding: 0;
            }
            body {
                overscroll-behavior: none;
            }
        }
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-track {
            background: #efeee7;
        }
        ::-webkit-scrollbar-thumb {
            background: #c0c9c2;
            border-radius: 3px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #707973;
        }
        .material-symbols-outlined {
            font-family: 'Material Symbols Outlined';
            font-weight: normal;
            font-style: normal;
            font-size: 20px;
            line-height: 1;
            display: inline-block;
            text-transform: none;
            letter-spacing: normal;
            word-wrap: normal;
            white-space: nowrap;
            direction: ltr;
            vertical-align: middle;
        }
    </style>

    <!-- Compilación con Vite -->
    @if(file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <!-- Fallback autónomo con CDN Tailwind para prototipado rápido -->
        <script src="https://cdn.tailwindcss.com"></script>
        <script>
            tailwind.config = {
                darkMode: "class",
                theme: {
                    extend: {
                        colors: {
                            "primary": "#2f664f",
                            "primary-container": "#497f67",
                            "on-primary": "#ffffff",
                            "on-primary-container": "#f5fff7",
                            "primary-fixed": "#b5efd2",
                            "primary-fixed-dim": "#9ad3b7",
                            "secondary": "#316a42",
                            "secondary-container": "#b3f1be",
                            "on-secondary": "#ffffff",
                            "on-secondary-container": "#377047",
                            "secondary-fixed": "#b3f1be",
                            "secondary-fixed-dim": "#98d4a4",
                            "tertiary": "#675a4e",
                            "tertiary-container": "#807265",
                            "on-tertiary": "#ffffff",
                            "surface": "#fbf9f2",
                            "surface-dim": "#dbdad3",
                            "surface-bright": "#fbf9f2",
                            "surface-container-lowest": "#ffffff",
                            "surface-container-low": "#f5f4ec",
                            "surface-container": "#efeee7",
                            "surface-container-high": "#eae8e1",
                            "surface-container-highest": "#e4e3db",
                            "on-surface": "#1b1c18",
                            "on-surface-variant": "#404944",
                            "outline": "#707973",
                            "outline-variant": "#c0c9c2",
                            "error": "#ba1a1a",
                            "error-container": "#ffdad6",
                            "on-error": "#ffffff",
                            "on-error-container": "#93000a",
                            "background": "#fbf9f2",
                            "on-background": "#1b1c18"
                        },
                        fontFamily: {
                            sans: ['Work Sans', 'sans-serif'],
                            headline: ['Work Sans', 'sans-serif']
                        },
                        spacing: {
                            gutter: "1.5rem",
                            margin: "2rem",
                            "space-xs": "0.25rem",
                            "space-sm": "0.5rem",
                            "space-md": "1rem",
                            "space-lg": "1.5rem",
                            "space-xl": "2rem"
                        }
                    }
                }
            };
        </script>
    @endif

    @stack('styles')
</head>
<body class="bg-[#fbf9f2] text-[#1b1c18] font-sans antialiased min-h-screen flex flex-col selection:bg-[#b5efd2] selection:text-[#002115]">

    <!-- ======================================================== -->
    <!-- MASTER NAVIGATION DECK ADMINISTRATIVO (HORIZONTAL 2 NIVELES) -->
    <!-- ======================================================== -->
    <header class="fixed top-0 left-0 right-0 z-50 bg-[#ffffff] border-b border-[#e4e3db]">
        <!-- NIVEL 1: Institutional Master Bar (64px) -->
        <div class="h-16 px-4 sm:px-6 lg:px-8 max-w-[1440px] mx-auto flex items-center justify-between gap-4 border-b border-[#e4e3db]">
            
            <!-- Identidad Institucional + Terminal Activa -->
            <div class="flex items-center gap-3 shrink-0">
                <a href="{{ url('/panel-operativo') }}" class="flex items-center gap-3 group">
                    <div class="w-10 h-10 rounded bg-[#efeee7] border border-[#e4e3db] flex items-center justify-center text-[#2f664f] group-hover:bg-[#2f664f] group-hover:text-white transition-colors">
                        <span class="material-symbols-outlined text-[24px]">terminal</span>
                    </div>
                    <div class="flex flex-col">
                        <div class="flex items-center gap-1.5">
                            <span class="text-[17px] font-extrabold tracking-tight text-[#1b1c18] leading-tight">UPTex TI</span>
                            <span class="w-2 h-2 rounded-full bg-[#316a42]"></span>
                            <span class="text-[10px] font-bold text-[#316a42] uppercase tracking-wider">GESTIÓN</span>
                        </div>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-[#707973] leading-none">Sistema de Gestión Centralizada</span>
                    </div>
                </a>
            </div>

            <!-- Buscador Administrativo Global (Ctrl + K) -->
            <div class="hidden md:flex flex-1 max-w-xl">
                <div class="relative flex items-center w-full">
                    <span class="material-symbols-outlined absolute left-3 text-[#707973] pointer-events-none text-[18px]">search</span>
                    <input type="text"
                           id="admin-global-search"
                           placeholder="Buscar salas, terminales (PC-01), software, matrículas, tickets #INC... [Ctrl + K]"
                           class="w-full h-10 pl-10 pr-4 bg-[#efeee7] rounded-lg border border-[#c0c9c2] text-sm text-[#1b1c18] placeholder-[#707973] focus:outline-none focus:border-[#2f664f] focus:bg-[#ffffff] transition-colors">
                </div>
            </div>

            <!-- Selector de Contexto, Notificaciones y Perfil del Operador -->
            <div class="flex items-center gap-3 shrink-0">
                <!-- Selector Rápido de Sala / Laboratorio -->
                <div class="hidden xl:block relative">
                    <select aria-label="Laboratorio Concurrente"
                            class="h-10 pl-3 pr-8 bg-[#ffffff] border border-[#c0c9c2] rounded-lg text-xs font-medium text-[#1b1c18] focus:outline-none focus:border-[#2f664f] cursor-pointer appearance-none">
                        <option selected>L-04 · Cómputo Especializado e IA</option>
                        <option>L-01 · Redes y Enrutamiento Cisco</option>
                        <option>L-02 · Arquitectura de Cómputo</option>
                        <option>L-03 · Desarrollo Multiplataforma</option>
                        <option>L-05 · Sistemas Embebidos y Robótica</option>
                        <option>Vista Consolidada (Todos los laboratorios)</option>
                    </select>
                    <span class="material-symbols-outlined absolute right-2.5 top-2.5 text-[#707973] pointer-events-none text-[18px]">arrow_drop_down</span>
                </div>

                <!-- Botón de Notificaciones con Indicador -->
                <button type="button"
                        aria-label="Notificaciones del Sistema"
                        class="relative w-10 h-10 rounded-lg border border-[#c0c9c2] bg-[#ffffff] flex items-center justify-center text-[#1b1c18] hover:bg-[#efeee7] transition-colors">
                    <span class="material-symbols-outlined text-[20px]">notifications</span>
                    <span class="absolute top-2 right-2 w-2 h-2 rounded-full bg-[#316a42]"></span>
                </button>

                <!-- Menú de Usuario Operador -->
                <div class="relative group">
                    <button type="button" class="flex items-center gap-2 pl-2 border-l border-[#e4e3db] focus:outline-none">
                        <div class="hidden sm:flex flex-col text-right">
                            <span class="text-xs font-bold text-[#1b1c18] leading-tight">
                                {{ Auth::user()->name ?? 'Ing. Carlos Mendoza' }}
                            </span>
                            <span class="text-[11px] font-semibold text-[#316a42] leading-tight">
                                {{ Auth::user()->role_label ?? 'Encargado TI · L-04' }}
                            </span>
                        </div>
                        <div class="w-9 h-9 rounded-full bg-[#2f664f] text-white flex items-center justify-center shrink-0 font-bold text-xs shadow-sm">
                            {{ strtoupper(substr(Auth::user()->name ?? 'CM', 0, 2)) }}
                        </div>
                        <span class="material-symbols-outlined text-[#707973] text-[16px]">expand_more</span>
                    </button>

                    <!-- Dropdown de Sesión -->
                    <div class="absolute right-0 mt-2 w-56 bg-[#ffffff] border border-[#1b1c18] rounded-xl py-2 shadow-none hidden group-hover:block hover:block z-50">
                        <div class="px-4 py-2 border-b border-[#e4e3db]">
                            <p class="text-xs font-bold text-[#1b1c18]">{{ Auth::user()->name ?? 'Ing. Carlos Mendoza' }}</p>
                            <p class="text-[11px] text-[#707973] truncate">{{ Auth::user()->email ?? 'carlos.mendoza@uptex.edu.mx' }}</p>
                        </div>

                        <a href="{{ url('/') }}" target="_blank" class="flex items-center gap-2 px-4 py-2 text-xs text-[#404944] hover:bg-[#efeee7] hover:text-[#1b1c18]">
                            <span class="material-symbols-outlined text-[16px]">open_in_new</span>
                            <span>Ver Portal Público</span>
                        </a>

                        <a href="{{ url('/perfil') }}" class="flex items-center gap-2 px-4 py-2 text-xs text-[#404944] hover:bg-[#efeee7] hover:text-[#1b1c18]">
                            <span class="material-symbols-outlined text-[16px]">manage_accounts</span>
                            <span>Ajustes de Perfil</span>
                        </a>

                        <div class="border-t border-[#e4e3db] my-1"></div>

                        <form method="POST" action="{{ route('logout') ?? url('/logout') }}">
                            @csrf
                            <button type="submit" class="w-full flex items-center gap-2 px-4 py-2 text-xs text-[#ba1a1a] hover:bg-[#ffdad6] text-left">
                                <span class="material-symbols-outlined text-[16px]">logout</span>
                                <span>Cerrar Sesión</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- NIVEL 2: Section Command Tabs (46px) -->
        <div class="h-[46px] px-4 sm:px-6 lg:px-8 max-w-[1440px] mx-auto bg-[#ffffff] flex items-center justify-between">
            <nav class="flex items-center gap-1 overflow-x-auto w-full h-full text-nowrap text-sm" aria-label="Navegación de Gestión Operativa">
                @php
                    $isRoute = fn($pattern) => request()->is($pattern) || request()->routeIs($pattern);
                @endphp

                <!-- Panel Operativo -->
                <a href="{{ url('/panel-operativo') }}"
                   class="h-full flex items-center px-4 font-medium transition-colors border-b-2 {{ $isRoute('panel-operativo*') || $isRoute('admin/dashboard*') ? 'border-[#2f664f] text-[#1b1c18] font-bold bg-[#efeee7]' : 'border-transparent text-[#404944] hover:bg-[#f5f4ec] hover:text-[#1b1c18]' }}">
                    <span class="material-symbols-outlined text-[18px] mr-1.5 {{ $isRoute('panel-operativo*') ? 'text-[#2f664f]' : 'text-[#707973]' }}">dashboard</span>
                    Panel Operativo
                </a>

                <!-- Malla Topológica -->
                <a href="{{ url('/malla-topologica') }}"
                   class="h-full flex items-center px-4 font-medium transition-colors border-b-2 {{ $isRoute('malla-topologica*') ? 'border-[#2f664f] text-[#1b1c18] font-bold bg-[#efeee7]' : 'border-transparent text-[#404944] hover:bg-[#f5f4ec] hover:text-[#1b1c18]' }}">
                    <span class="material-symbols-outlined text-[18px] mr-1.5 {{ $isRoute('malla-topologica*') ? 'text-[#2f664f]' : 'text-[#707973]' }}">grid_view</span>
                    Malla Topológica
                </a>

                <!-- Mesa de Incidencias -->
                <a href="{{ url('/mesa-incidencias') }}"
                   class="h-full flex items-center px-4 font-medium transition-colors border-b-2 {{ $isRoute('mesa-incidencias*') ? 'border-[#2f664f] text-[#1b1c18] font-bold bg-[#efeee7]' : 'border-transparent text-[#404944] hover:bg-[#f5f4ec] hover:text-[#1b1c18]' }}">
                    <span class="material-symbols-outlined text-[18px] mr-1.5 {{ $isRoute('mesa-incidencias*') ? 'text-[#2f664f]' : 'text-[#707973]' }}">confirmation_number</span>
                    Mesa de Incidencias
                </a>

                <!-- Inventario y Auditoría -->
                <a href="{{ url('/inventario') }}"
                   class="h-full flex items-center px-4 font-medium transition-colors border-b-2 {{ $isRoute('inventario*') ? 'border-[#2f664f] text-[#1b1c18] font-bold bg-[#efeee7]' : 'border-transparent text-[#404944] hover:bg-[#f5f4ec] hover:text-[#1b1c18]' }}">
                    <span class="material-symbols-outlined text-[18px] mr-1.5 {{ $isRoute('inventario*') ? 'text-[#2f664f]' : 'text-[#707973]' }}">qr_code_scanner</span>
                    Inventario y Auditoría
                </a>

                <!-- Horarios y Prácticas -->
                <a href="{{ url('/admin/horarios') }}"
                   class="h-full flex items-center px-4 font-medium transition-colors border-b-2 {{ $isRoute('admin/horarios*') ? 'border-[#2f664f] text-[#1b1c18] font-bold bg-[#efeee7]' : 'border-transparent text-[#404944] hover:bg-[#f5f4ec] hover:text-[#1b1c18]' }}">
                    <span class="material-symbols-outlined text-[18px] mr-1.5 {{ $isRoute('admin/horarios*') ? 'text-[#2f664f]' : 'text-[#707973]' }}">calendar_today</span>
                    Horarios y Prácticas
                </a>

                <!-- Catálogo de Software -->
                <a href="{{ url('/admin/software') }}"
                   class="h-full flex items-center px-4 font-medium transition-colors border-b-2 {{ $isRoute('admin/software*') ? 'border-[#2f664f] text-[#1b1c18] font-bold bg-[#efeee7]' : 'border-transparent text-[#404944] hover:bg-[#f5f4ec] hover:text-[#1b1c18]' }}">
                    <span class="material-symbols-outlined text-[18px] mr-1.5 {{ $isRoute('admin/software*') ? 'text-[#2f664f]' : 'text-[#707973]' }}">apps</span>
                    Catálogo de Software
                </a>
            </nav>

            <!-- Indicador de Guardia Activa -->
            <div class="hidden lg:flex items-center gap-2 pl-4 shrink-0">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded bg-[#b3f1be]/30 text-[#16512c] font-bold text-[11px] uppercase tracking-wider">
                    <span class="w-1.5 h-1.5 rounded-full bg-[#316a42]"></span>
                    Guardia Activa
                </span>
            </div>
        </div>
    </header>

    <!-- ========================================== -->
    <!-- ÁREA DE CONTENIDO OPERATIVO PRINCIPAL -->
    <!-- ========================================== -->
    <main class="w-full pt-[110px] bg-[#fbf9f2] flex-1 pb-14">
        <div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8 py-6">

            <!-- Mensajes Flash Institucionales -->
            @if(session('success'))
                <div class="mb-6 p-4 rounded-xl bg-[#b3f1be]/30 border border-[#316a42] text-[#16512c] flex items-start gap-3" role="alert">
                    <span class="material-symbols-outlined text-[20px] text-[#316a42] mt-0.5 shrink-0">check_circle</span>
                    <div class="text-sm font-medium leading-relaxed">{{ session('success') }}</div>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 p-4 rounded-xl bg-[#ffdad6] border border-[#ba1a1a] text-[#93000a] flex items-start gap-3" role="alert">
                    <span class="material-symbols-outlined text-[20px] text-[#ba1a1a] mt-0.5 shrink-0">error</span>
                    <div class="text-sm font-medium leading-relaxed">{{ session('error') }}</div>
                </div>
            @endif

            @if(session('warning'))
                <div class="mb-6 p-4 rounded-xl bg-[#fff1d6] border border-[#825500] text-[#825500] flex items-start gap-3" role="alert">
                    <span class="material-symbols-outlined text-[20px] text-[#825500] mt-0.5 shrink-0">warning</span>
                    <div class="text-sm font-medium leading-relaxed">{{ session('warning') }}</div>
                </div>
            @endif

            <!-- Encabezado de Sesión y Acciones Rápidas -->
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 mb-6">
                <div class="flex flex-col gap-1">
                    <div class="flex items-center gap-2">
                        <span class="text-[11px] uppercase tracking-wider text-[#707973] font-bold">UPTex TI · Consola de Gestión Centralizada</span>
                        <span class="w-1.5 h-1.5 rounded-full bg-[#316a42]"></span>
                        <span class="text-[11px] text-[#316a42] font-bold">TERMINAL ACTIVA</span>
                    </div>

                    <h1 class="text-2xl sm:text-3xl font-extrabold text-[#1b1c18] tracking-tight">
                        @yield('header-title', 'Panel de Control Operativo')
                    </h1>

                    <p class="text-sm text-[#404944] flex items-center gap-2 flex-wrap">
                        @yield('header-subtitle')
                    </p>
                </div>

                <!-- Botonera de Acciones Específicas de la Vista -->
                <div class="flex items-center gap-2.5 shrink-0">
                    @yield('actions')
                </div>
            </div>

            <!-- Inyección de Contenido Operativo -->
            @yield('content')
        </div>
    </main>

    <!-- ======================================================== -->
    <!-- BARRA DE ESTADO TÉCNICA INFERIOR (TELEMETRÍA EN TIEMPO REAL) -->
    <!-- ======================================================== -->
    <aside class="fixed bottom-0 left-0 right-0 h-10 bg-[#ffffff] border-t border-[#e4e3db] z-40 px-4 sm:px-6 lg:px-8 text-xs text-[#707973] flex items-center justify-between">
        <div class="flex items-center gap-4">
            <span class="flex items-center gap-1.5 font-medium text-[#1b1c18]">
                <span class="w-2 h-2 rounded-full bg-[#316a42] animate-pulse"></span>
                <span>Enlace Backbone: 10 Gbps Fibra</span>
            </span>
            <span class="hidden md:inline text-[#e4e3db]">|</span>
            <span class="hidden md:flex items-center gap-1 font-mono text-[11px]">
                <span class="material-symbols-outlined text-[15px]">timer</span>
                Latencia: 3.4 ms
            </span>
            <span class="hidden md:inline text-[#e4e3db]">|</span>
            <span class="hidden lg:flex items-center gap-1">
                <span class="material-symbols-outlined text-[15px] text-[#2f664f]">dns</span>
                Gateway: 192.168.10.1 (VLAN-10 Académica)
            </span>
        </div>

        <div class="flex items-center gap-3 font-mono text-[11px]">
            <span class="text-[#404944]">Terminal de Operador: <strong>E104-ADM01</strong></span>
            <span class="text-[#e4e3db]">|</span>
            <span class="text-[#2f664f] font-semibold flex items-center gap-1">
                <span class="material-symbols-outlined text-[14px]">sync</span>
                NTP Sincronizado
            </span>
        </div>
    </aside>

    @stack('scripts')
</body>
</html>
