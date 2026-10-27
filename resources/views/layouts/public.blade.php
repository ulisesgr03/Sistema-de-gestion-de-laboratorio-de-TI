<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="shell-type" content="web_standard">

    <title>@yield('title', 'Portal de Laboratorios de TI') · UPTex TI</title>

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

    <!-- ========================================== -->
    <!-- MASTER NAVIGATION DECK (HORIZONTAL 2 NIVELES) -->
    <!-- ========================================== -->
    <header class="fixed top-0 left-0 right-0 z-50 bg-[#ffffff] border-b border-[#e4e3db]">
        <!-- NIVEL 1: Institutional Master Bar (64px) -->
        <div class="h-16 px-4 sm:px-6 lg:px-8 max-w-[1440px] mx-auto flex items-center justify-between gap-4 border-b border-[#e4e3db]">
            <!-- Identidad Institucional -->
            <div class="flex items-center gap-3 shrink-0">
                <a href="{{ url('/') }}" class="flex items-center gap-3 group">
                    <div class="w-10 h-10 rounded bg-[#efeee7] border border-[#e4e3db] flex items-center justify-center text-[#2f664f] group-hover:bg-[#2f664f] group-hover:text-white transition-colors">
                        <span class="material-symbols-outlined text-[24px]">terminal</span>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-[17px] font-extrabold tracking-tight text-[#1b1c18] leading-tight">UPTex · TI</span>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-[#707973] leading-none">Portal Público de Laboratorios</span>
                    </div>
                </a>
            </div>

            <!-- Buscador Rápido del Portal -->
            <div class="hidden md:flex flex-1 max-w-xl">
                <div class="relative flex items-center w-full">
                    <span class="material-symbols-outlined absolute left-3 text-[#707973] pointer-events-none text-[18px]">search</span>
                    <input type="text"
                           id="global-search"
                           placeholder="Buscar salas, software, reglamentos o prácticas... [Ctrl + K]"
                           class="w-full h-10 pl-10 pr-4 bg-[#efeee7] rounded-lg border border-[#c0c9c2] text-sm text-[#1b1c18] placeholder-[#707973] focus:outline-none focus:border-[#2f664f] focus:bg-[#ffffff] transition-colors">
                </div>
            </div>

            <!-- Estado Operativo e Inicio de Sesión -->
            <div class="flex items-center gap-3 shrink-0">
                <!-- Badge de Salud del Sistema -->
                <div class="hidden lg:flex items-center gap-2 px-3 py-1.5 rounded-lg bg-[#f5f4ec] border border-[#e4e3db] text-xs">
                    <span class="w-2 h-2 rounded-full bg-[#316a42] animate-pulse"></span>
                    <span class="font-semibold text-[#1b1c18]">6 Salas Activas</span>
                    <span class="text-[#707973]">|</span>
                    <span class="text-[#316a42] font-bold">100% Operativo</span>
                </div>

                <!-- Botón de Acceso Institucional -->
                @if(Route::has('login'))
                    @auth
                        <a href="{{ url('/panel-operativo') }}"
                           class="inline-flex items-center gap-2 h-10 px-4 rounded-lg bg-[#2f664f] hover:bg-[#497f67] text-white font-medium text-sm transition-colors shadow-sm">
                            <span class="material-symbols-outlined text-[18px]">dashboard</span>
                            <span class="hidden sm:inline">Consola Operativa</span>
                        </a>
                    @else
                        <a href="{{ route('login') }}"
                           class="inline-flex items-center gap-2 h-10 px-4 rounded-lg bg-[#2f664f] hover:bg-[#497f67] text-white font-medium text-sm transition-colors shadow-sm">
                            <span class="material-symbols-outlined text-[18px]">login</span>
                            <span>Acceso Institucional</span>
                        </a>
                    @endauth
                @else
                    <a href="{{ url('/login') }}"
                       class="inline-flex items-center gap-2 h-10 px-4 rounded-lg bg-[#2f664f] hover:bg-[#497f67] text-white font-medium text-sm transition-colors shadow-sm">
                        <span class="material-symbols-outlined text-[18px]">login</span>
                        <span>Acceso Institucional</span>
                    </a>
                @endif
            </div>
        </div>

        <!-- NIVEL 2: Section Command Tabs (46px) -->
        <div class="h-[46px] px-4 sm:px-6 lg:px-8 max-w-[1440px] mx-auto bg-[#ffffff] flex items-center">
            <nav class="flex items-center gap-1 overflow-x-auto w-full h-full text-nowrap text-sm" aria-label="Navegación de Secciones Públicas">
                @php
                    $isRoute = fn($pattern) => request()->is($pattern) || request()->routeIs($pattern);
                @endphp

                <!-- Inicio y Servicios -->
                <a href="{{ url('/') }}"
                   class="h-full flex items-center px-4 font-medium transition-colors border-b-2 {{ $isRoute('/') || $isRoute('inicio*') ? 'border-[#2f664f] text-[#1b1c18] font-bold bg-[#efeee7]' : 'border-transparent text-[#404944] hover:bg-[#f5f4ec] hover:text-[#1b1c18]' }}">
                    <span class="material-symbols-outlined text-[18px] mr-1.5 {{ $isRoute('/') ? 'text-[#2f664f]' : 'text-[#707973]' }}">home</span>
                    Inicio y Servicios
                </a>

                <!-- Salas y Disponibilidad -->
                <a href="{{ url('/salas') }}"
                   class="h-full flex items-center px-4 font-medium transition-colors border-b-2 {{ $isRoute('salas*') ? 'border-[#2f664f] text-[#1b1c18] font-bold bg-[#efeee7]' : 'border-transparent text-[#404944] hover:bg-[#f5f4ec] hover:text-[#1b1c18]' }}">
                    <span class="material-symbols-outlined text-[18px] mr-1.5 {{ $isRoute('salas*') ? 'text-[#2f664f]' : 'text-[#707973]' }}">meeting_room</span>
                    Salas y Disponibilidad
                </a>

                <!-- Horarios y Prácticas -->
                <a href="{{ url('/horarios') }}"
                   class="h-full flex items-center px-4 font-medium transition-colors border-b-2 {{ $isRoute('horarios*') ? 'border-[#2f664f] text-[#1b1c18] font-bold bg-[#efeee7]' : 'border-transparent text-[#404944] hover:bg-[#f5f4ec] hover:text-[#1b1c18]' }}">
                    <span class="material-symbols-outlined text-[18px] mr-1.5 {{ $isRoute('horarios*') ? 'text-[#2f664f]' : 'text-[#707973]' }}">calendar_month</span>
                    Horarios y Prácticas
                </a>

                <!-- Catálogo de Software -->
                <a href="{{ url('/software') }}"
                   class="h-full flex items-center px-4 font-medium transition-colors border-b-2 {{ $isRoute('software*') ? 'border-[#2f664f] text-[#1b1c18] font-bold bg-[#efeee7]' : 'border-transparent text-[#404944] hover:bg-[#f5f4ec] hover:text-[#1b1c18]' }}">
                    <span class="material-symbols-outlined text-[18px] mr-1.5 {{ $isRoute('software*') ? 'text-[#2f664f]' : 'text-[#707973]' }}">apps</span>
                    Catálogo de Software
                </a>

                <!-- Préstamos en Ventanilla -->
                <a href="{{ url('/prestamos') }}"
                   class="h-full flex items-center px-4 font-medium transition-colors border-b-2 {{ $isRoute('prestamos*') ? 'border-[#2f664f] text-[#1b1c18] font-bold bg-[#efeee7]' : 'border-transparent text-[#404944] hover:bg-[#f5f4ec] hover:text-[#1b1c18]' }}">
                    <span class="material-symbols-outlined text-[18px] mr-1.5 {{ $isRoute('prestamos*') ? 'text-[#2f664f]' : 'text-[#707973]' }}">inventory_2</span>
                    Préstamos en Ventanilla
                </a>

                <!-- Reglamento Oficial -->
                <a href="{{ url('/reglamento') }}"
                   class="h-full flex items-center px-4 font-medium transition-colors border-b-2 {{ $isRoute('reglamento*') ? 'border-[#2f664f] text-[#1b1c18] font-bold bg-[#efeee7]' : 'border-transparent text-[#404944] hover:bg-[#f5f4ec] hover:text-[#1b1c18]' }}">
                    <span class="material-symbols-outlined text-[18px] mr-1.5 {{ $isRoute('reglamento*') ? 'text-[#2f664f]' : 'text-[#707973]' }}">gavel</span>
                    Reglamento Oficial
                </a>

                <div class="h-5 w-[1px] bg-[#e4e3db] mx-2 shrink-0"></div>

                <!-- Reportar Avería (Acción destacada para alumnos y docentes) -->
                <a href="{{ url('/reportar-averia') }}"
                   class="h-full flex items-center px-4 font-medium transition-colors border-b-2 {{ $isRoute('reportar-averia*') ? 'border-[#ba1a1a] text-[#ba1a1a] font-bold bg-[#ffdad6]' : 'border-transparent text-[#ba1a1a] hover:bg-[#ffdad6]/40' }}">
                    <span class="material-symbols-outlined text-[18px] mr-1.5 text-[#ba1a1a]">report_problem</span>
                    Reportar Avería
                </a>
            </nav>
        </div>
    </header>

    <!-- ========================================== -->
    <!-- ÁREA DE CONTENIDO PRINCIPAL -->
    <!-- ========================================== -->
    <main class="w-full pt-[110px] bg-[#fbf9f2] flex-1">
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

            @if(session('info'))
                <div class="mb-6 p-4 rounded-xl bg-[#efeee7] border border-[#707973] text-[#1b1c18] flex items-start gap-3" role="alert">
                    <span class="material-symbols-outlined text-[20px] text-[#2f664f] mt-0.5 shrink-0">info</span>
                    <div class="text-sm font-medium leading-relaxed">{{ session('info') }}</div>
                </div>
            @endif

            <!-- Encabezado de Página Opcional -->
            @hasSection('header')
                <div class="mb-6">
                    @yield('header')
                </div>
            @endif

            <!-- Inyección de Contenido Específico -->
            @yield('content')
        </div>
    </main>

    <!-- ========================================== -->
    <!-- PIE DE PÁGINA INSTITUCIONAL -->
    <!-- ========================================== -->
    <footer class="w-full bg-[#ffffff] border-t border-[#e4e3db] mt-auto">
        <div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8 py-10">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-8">
                <!-- Columna 1: Identidad -->
                <div class="flex flex-col gap-3">
                    <div class="flex items-center gap-2 text-[#2f664f]">
                        <span class="material-symbols-outlined text-[22px]">terminal</span>
                        <span class="font-extrabold text-base tracking-tight text-[#1b1c18]">UPTex · Laboratorios de TI</span>
                    </div>
                    <p class="text-xs text-[#404944] leading-relaxed">
                        División de Tecnologías de la Información. Infraestructura computacional certificada para la formación en ingeniería de software, redes, ciberseguridad e inteligencia artificial.
                    </p>
                    <div class="flex items-center gap-2 text-xs text-[#316a42] font-semibold mt-1">
                        <span class="material-symbols-outlined text-[16px]">verified</span>
                        <span>Procesos alineados a ISO/IEC 27001</span>
                    </div>
                </div>

                <!-- Columna 2: Ubicación y Horarios -->
                <div class="flex flex-col gap-2">
                    <span class="text-xs font-bold uppercase tracking-wider text-[#707973]">Ubicación y Atención</span>
                    <p class="text-xs text-[#404944] leading-relaxed">
                        <strong>Campus Central:</strong> Edificio E (Planta Baja y 1er Nivel).<br>
                        <strong>Horario Operativo:</strong> Lunes a Viernes de 07:00 a 20:00 hrs.<br>
                        Sábados de 08:00 a 14:00 hrs.
                    </p>
                    <div class="text-xs text-[#675a4e] mt-1">
                        Ventanilla de Pañol y Préstamos: <strong>Cubículo E-102</strong>
                    </div>
                </div>

                <!-- Columna 3: Enlaces Técnicos -->
                <div class="flex flex-col gap-2">
                    <span class="text-xs font-bold uppercase tracking-wider text-[#707973]">Servicios Universitarios</span>
                    <ul class="text-xs space-y-1.5 text-[#404944]">
                        <li><a href="{{ url('/salas') }}" class="hover:text-[#2f664f] transition-colors">Disponibilidad en Tiempo Real</a></li>
                        <li><a href="{{ url('/software') }}" class="hover:text-[#2f664f] transition-colors">Matriz de Licencias y Versiones</a></li>
                        <li><a href="{{ url('/reglamento') }}" class="hover:text-[#2f664f] transition-colors">Normativa Oficial del Laboratorio</a></li>
                        <li><a href="{{ url('/reportar-averia') }}" class="hover:text-[#ba1a1a] transition-colors">Mesa de Ayuda de Estudiantes</a></li>
                    </ul>
                </div>

                <!-- Columna 4: Soporte y Contacto -->
                <div class="flex flex-col gap-2">
                    <span class="text-xs font-bold uppercase tracking-wider text-[#707973]">Soporte y Contacto</span>
                    <p class="text-xs text-[#404944] leading-relaxed">
                        Para incidencias de red, solicitudes de instalación o reservas grupales:
                    </p>
                    <div class="flex flex-col gap-1 text-xs">
                        <span class="text-[#1b1c18] font-medium">laboratorios.ti@uptex.edu.mx</span>
                        <span class="text-[#707973]">Extensión Interna: 1402 / 1405</span>
                    </div>
                    <div class="mt-2">
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded bg-[#efeee7] text-[11px] font-mono text-[#404944]">
                            <span class="w-1.5 h-1.5 rounded-full bg-[#316a42]"></span>
                            Sincronizado NTP: Red LAN UPTex
                        </span>
                    </div>
                </div>
            </div>

            <div class="pt-6 border-t border-[#e4e3db] flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-[#707973]">
                <p>© {{ date('Y') }} Universidad Politécnica de Texcoco. Todos los derechos reservados.</p>
                <div class="flex items-center gap-4">
                    <span>División de TI</span>
                    <span>·</span>
                    <span>Términos de Conectividad</span>
                    <span>·</span>
                    <a href="{{ url('/login') }}" class="text-[#2f664f] hover:underline font-medium">Acceso para Personal</a>
                </div>
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
