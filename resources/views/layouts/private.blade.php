<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="shell-type" content="web_standard">

    <title>@yield('title', 'Panel Privado') · {{ $institutionName ?? config('app.institution_name', 'MetalNet') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Alegreya+Sans:ital,wght@0,100;0,300;0,400;0,500;0,700;0,800;0,900;1,100;1,300;1,400;1,500;1,700;1,800;1,900&family=Lora:ital,wght@0,400..700;1,400..700&family=Noto+Serif:ital,wght@0,100..900;1,100..900&family=Original+Surfer&family=Oswald:wght@200..700&family=Roboto+Slab:wght@100..900&display=swap" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        "surface": "#fbf9f2",
                        "surface-dim": "#dbdad3",
                        "surface-bright": "#fbf9f2",
                        "surface-container-lowest": "#ffffff",
                        "surface-container-low": "#f5f4ec",
                        "surface-container": "#efeee7",
                        "surface-container-high": "#eae8e1",
                        "surface-container-highest": "#e4e3db",
                        "outline": "#707973",
                        "outline-variant": "#c0c9c2",
                        "primary": "#2f664f",
                        "primary-container": "#497f67",
                        "on-primary": "#ffffff",
                        "secondary": "#316a42",
                        "secondary-container": "#b3f1be",
                        "on-secondary-container": "#377047",
                        "tertiary": "#675a4e",
                        "error": "#ba1a1a",
                        "error-container": "#ffdad6",
                        "on-surface": "#1b1c18",
                        "on-surface-variant": "#404944",

                        "status-available": "#2f664f",
                        "status-occupied": "#f59e0b",
                        "status-unavailable": "#ef4444",

                        "text-main": "#1b1c18",
                        "text-sub": "#404944",
                        "text-muted": "#707973",

                        // Semántica y compatibilidad
                        "dark-spruce": "#ffffff",
                        "sea-green": "#f5f4ec",
                        "muted-teal": "#e4e3db",
                        "icy-blue": "#2f664f",
                        "tea-green": "#e8f5ec",
                        "dark-slate-grey": "#ffffff",
                        "fern": "#f5f4ec",
                        "sage-green": "#e4e3db",
                        "willow-green": "#2f664f",
                        "pearl-aqua": "#2f664f",
                        "carbon-black": "#ffffff",
                        "black-forest": "#f5f4ec",
                        "hunter-green": "#e4e3db",
                        "aquamarine": "#2f664f",
                        "text-primary": "#1b1c18",
                        "text-secondary": "#404944"
                    },
                    fontFamily: {
                        sans: ['Alegreya Sans', 'Work Sans', 'sans-serif'],
                        slab: ['Roboto Slab', 'serif'],
                        oswald: ['Oswald', 'sans-serif'],
                        serif: ['Lora', 'Noto Serif', 'serif'],
                        surfer: ['Original Surfer', 'cursive'],
                        mono: ['JetBrains Mono', 'monospace']
                    }
                }
            }
        };
    </script>

    <style>
        :root {
            /* Paleta Oficial Stitch */
            --surface: #fbf9f2;
            --surface-canvas: #fbf9f2;
            --surface-dim: #dbdad3;
            --surface-bright: #fbf9f2;
            --surface-container-lowest: #ffffff;
            --surface-container-low: #f5f4ec;
            --surface-container: #efeee7;
            --surface-container-high: #eae8e1;
            --surface-container-highest: #e4e3db;
            --on-surface: #1b1c18;
            --on-surface-variant: #404944;
            --inverse-surface: #30312c;
            --inverse-on-surface: #f2f1e9;
            --outline: #707973;
            --outline-variant: #c0c9c2;
            --surface-tint: #326852;
            --primary: #2f664f;
            --on-primary: #ffffff;
            --primary-container: #497f67;
            --on-primary-container: #f5fff7;
            --inverse-primary: #9ad3b7;
            --secondary: #316a42;
            --on-secondary: #ffffff;
            --secondary-container: #b3f1be;
            --on-secondary-container: #377047;
            --tertiary: #675a4e;
            --on-tertiary: #ffffff;
            --tertiary-container: #807265;
            --on-tertiary-container: #fffbff;
            --error: #ba1a1a;
            --on-error: #ffffff;
            --error-container: #ffdad6;
            --on-error-container: #93000a;
            --background: #fbf9f2;
            --on-background: #1b1c18;
            --surface-variant: #e4e3db;

            /* Colores Semánticos de Estado (3 únicos colores) */
            --status-available:   #2f664f;
            --status-occupied:    #f59e0b;
            --status-unavailable: #ef4444;

            /* Jerarquía de Texto para Alto Contraste */
            --text-main:    #1b1c18;
            --text-sub:     #404944;
            --text-muted:   #707973;

            /* Variables de compatibilidad */
            --dark-spruce: var(--surface-container-lowest);
            --sea-green:   var(--surface-container-low);
            --muted-teal:  var(--surface-container-highest);
            --icy-blue:    var(--primary);
            --tea-green:   var(--secondary-container);

            --dark-slate-grey: var(--surface-container-lowest);
            --fern:            var(--surface-container-low);
            --sage-green:      var(--surface-container-highest);
            --willow-green:    var(--primary);
            --pearl-aqua:      var(--primary);
            --carbon-black:    var(--surface-container-lowest);
            --black-forest:    var(--surface-container-low);
            --hunter-green:    var(--surface-container-highest);
            --aquamarine:      var(--primary);
            --text-primary:    var(--text-main);
            --text-secondary:  var(--text-sub);

            --state-free: var(--status-available);
            --state-free-bg: #e8f5ec;
            --state-free-border: #b3f1be;
            --state-free-text: #16512c;

            --state-busy: var(--status-occupied);
            --state-busy-bg: #fef3c7;
            --state-busy-border: #fde68a;
            --state-busy-text: #92400e;

            --state-down: var(--status-unavailable);
            --state-down-bg: #ffdad6;
            --state-down-border: #fca5a5;
            --state-down-text: #991b1b;
        }

        html, body {
            margin: 0;
            padding: 0;
            background-color: var(--surface);
            color: var(--on-surface);
            font-family: 'Alegreya Sans', 'Work Sans', sans-serif;
        }

        ::-webkit-scrollbar { width: 7px; height: 7px; }
        ::-webkit-scrollbar-track { background: var(--surface-container-low); }
        ::-webkit-scrollbar-thumb { background: var(--surface-container-highest); border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: var(--outline-variant); }

        .mn-pill-navbar {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(14px);
            border: 1px solid var(--surface-container-highest);
            box-shadow: 0 4px 20px -2px rgba(27, 28, 24, 0.08), 0 2px 6px -1px rgba(27, 28, 24, 0.04);
        }

        .mn-legend-capsule {
            display: inline-flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 0.75rem;
            padding: 0.35rem 0.85rem;
            border-radius: 9999px;
            background: #ffffff;
            border: 1px solid var(--surface-container-highest);
            font-size: 11px;
            font-weight: 700;
            color: var(--on-surface-variant);
            box-shadow: 0 1px 3px rgba(27, 28, 24, 0.05);
        }

        .mn-legend-item {
            display: inline-flex;
            align-items: center;
            gap: 0.375rem;
            white-space: nowrap;
        }

        .mn-legend-dot {
            width: 0.6rem;
            height: 0.6rem;
            border-radius: 9999px;
            display: inline-block;
            flex-shrink: 0;
        }

        .mn-legend-dot--free { background: var(--status-available); box-shadow: 0 0 4px rgba(47, 102, 79, 0.4); }
        .mn-legend-dot--busy { background: var(--status-occupied); box-shadow: 0 0 4px rgba(245, 158, 11, 0.4); }
        .mn-legend-dot--down { background: var(--status-unavailable); box-shadow: 0 0 4px rgba(239, 68, 68, 0.4); }
    </style>

    @stack('styles')
</head>
<body class="font-sans antialiased min-h-screen flex flex-col bg-surface text-on-surface selection:bg-secondary-container selection:text-on-secondary-container">

    @php
        $user = Auth::user();
        $roleSlug = $user ? $user->primaryRoleSlug() : 'alumno';
        $roleLabel = $user ? $user->primaryRoleLabel() : 'Usuario';
    @endphp

    <!-- BARRA DE NAVEGACIÓN CÁPSULA FLOTANTE DE PANEL PRIVADO · METALNET -->
    <header class="fixed top-3.5 left-0 right-0 z-50 px-3 sm:px-6 pointer-events-none">
        <div class="max-w-[1180px] mx-auto flex items-center justify-center">
            <nav aria-label="Navegación Panel Privado"
                 class="mn-pill-navbar pointer-events-auto w-full text-on-surface rounded-full p-2 flex items-center justify-between gap-2">

                <!-- Brand + Role Badge -->
                <div class="flex items-center gap-2.5 pl-2 shrink-0">
                    <a href="{{ url('/') }}"
                       title="Volver al Mapa 3D"
                       class="flex items-center gap-2 hover:opacity-90 transition-opacity">
                        <img src="{{ asset('images/brand/metalnet-logo.svg') }}" alt="MetalNet" class="w-10 h-10 rounded-full border border-surface-container-highest shadow-sm">
                        <div class="hidden md:block leading-tight">
                            <span class="text-sm font-extrabold text-on-surface block">{{ $institutionName ?? 'MetalNet' }}</span>
                            <span class="text-[10px] font-mono font-bold text-primary uppercase block">Consola · {{ $roleLabel }}</span>
                        </div>
                    </a>
                </div>

                <!-- Navegación Central (Panel Privado + Vistas del Sistema) -->
                <div class="flex items-center justify-center gap-1 overflow-x-auto px-2">
                    <a href="{{ $user ? $user->dashboardRoute() : url('/panel-operativo') }}"
                       class="px-3.5 py-2 rounded-full text-xs font-bold transition-all whitespace-nowrap bg-primary text-white shadow-sm">
                        Mi Panel ({{ $roleLabel }})
                    </a>

                    @if($user && $user->isAdmin())
                        <a href="{{ route('panel.admin') }}"
                           class="px-3 py-2 rounded-full text-xs font-semibold transition-all whitespace-nowrap {{ request()->routeIs('panel.admin') ? 'bg-surface-container text-primary font-bold border border-surface-container-highest' : 'text-on-surface-variant hover:text-on-surface hover:bg-surface-container' }}">
                            Admin Central
                        </a>
                        <a href="{{ route('panel.docente') }}"
                           class="px-3 py-2 rounded-full text-xs font-semibold transition-all whitespace-nowrap {{ request()->routeIs('panel.docente') ? 'bg-surface-container text-primary font-bold border border-surface-container-highest' : 'text-on-surface-variant hover:text-on-surface hover:bg-surface-container' }}">
                            Vista Docente
                        </a>
                        <a href="{{ route('panel.alumno') }}"
                           class="px-3 py-2 rounded-full text-xs font-semibold transition-all whitespace-nowrap {{ request()->routeIs('panel.alumno') ? 'bg-surface-container text-primary font-bold border border-surface-container-highest' : 'text-on-surface-variant hover:text-on-surface hover:bg-surface-container' }}">
                            Vista Alumno
                        </a>
                    @endif

                    <a href="{{ url('/') }}"
                       class="px-3 py-2 rounded-full text-xs font-semibold text-on-surface-variant hover:text-on-surface hover:bg-surface-container transition-all whitespace-nowrap inline-flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 3L2 8l10 5 10-5-10-5z"/><path d="M2 13l10 5 10-5"/><path d="M2 18l10 5 10-5"/></svg>
                        <span>Mapa 3D</span>
                    </a>
                    <a href="{{ url('/salas') }}"
                       class="px-3 py-2 rounded-full text-xs font-semibold text-on-surface-variant hover:text-on-surface hover:bg-surface-container transition-all whitespace-nowrap">
                        Laboratorios
                    </a>
                    <a href="{{ url('/horarios') }}"
                       class="px-3 py-2 rounded-full text-xs font-semibold text-on-surface-variant hover:text-on-surface hover:bg-surface-container transition-all whitespace-nowrap">
                        Horarios
                    </a>
                    <a href="{{ url('/software') }}"
                       class="px-3 py-2 rounded-full text-xs font-semibold text-on-surface-variant hover:text-on-surface hover:bg-surface-container transition-all whitespace-nowrap">
                        Software
                    </a>
                </div>

                <!-- Usuario y Cierre de Sesión -->
                <div class="flex items-center gap-1.5 shrink-0 pr-1">
                    <span class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-surface-container-low border border-surface-container-highest text-[11px] font-mono text-on-surface">
                        <span class="w-2 h-2 rounded-full bg-primary"></span>
                        <span class="truncate max-w-[120px]">{{ $user ? $user->name : 'Usuario' }}</span>
                    </span>

                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit"
                                title="Cerrar sesión"
                                class="w-9 h-9 rounded-full bg-surface-container-lowest hover:bg-error hover:text-white text-on-surface-variant border border-surface-container-highest flex items-center justify-center transition-colors cursor-pointer shadow-sm">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                        </button>
                    </form>
                </div>
            </nav>
        </div>
    </header>

    <!-- ÁREA PRINCIPAL -->
    <main class="w-full pt-[84px] flex-1">
        <div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8 py-4">

            {{-- Botón rápido para volver al Mapa 3D --}}
            <div class="mb-4 flex items-center justify-between">
                <a href="{{ url('/') }}"
                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-surface-container-lowest hover:bg-surface-container text-primary text-xs font-bold border border-surface-container-highest transition-all shadow-sm">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
                    <span>Volver al Inicio (Mapa 3D)</span>
                </a>
                <span class="text-xs font-mono text-outline">
                    Sesión activa · {{ $roleLabel }}
                </span>
            </div>

            @if(session('success') || session('status'))
                <div class="mb-4 p-3.5 rounded-xl bg-[#e8f5ec] border border-[#b3f1be] text-[#16512c] flex items-center gap-2.5 text-xs font-bold" role="alert">
                    <svg class="w-4 h-4 shrink-0 text-[#16512c]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                    <span>{{ session('success') ?? session('status') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 p-3.5 rounded-xl bg-[#ffdad6] border border-[#fca5a5] text-[#991b1b] flex items-center gap-2.5 text-xs font-bold" role="alert">
                    <svg class="w-4 h-4 shrink-0 text-[#991b1b]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            @yield('content')
        </div>
    </main>

    <footer class="w-full bg-surface-container-lowest border-t border-surface-container-highest mt-auto">
        <div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8 py-3.5 flex flex-wrap items-center justify-between gap-3 text-xs text-on-surface-variant">
            <div class="flex items-center gap-2 font-bold text-on-surface">
                <img src="{{ asset('images/brand/metalnet-logo.svg') }}" alt="MetalNet" class="w-4 h-4 rounded-full">
                <span>{{ $institutionFull ?? 'MetalNet · Laboratorios de Ingeniería en Sistemas' }}</span>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ url('/') }}" class="text-primary hover:underline font-semibold">Ir al Inicio</a>
                <span class="text-outline-variant">|</span>
                <a href="{{ url('/salas') }}" class="text-on-surface-variant hover:text-primary">Laboratorios</a>
                <span class="text-outline-variant">|</span>
                <a href="{{ url('/horarios') }}" class="text-on-surface-variant hover:text-primary">Horarios</a>
            </div>
        </div>
    </footer>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ url('/') }}" class="text-icy-blue hover:underline font-semibold">Ir al Inicio</a>
                <span class="text-muted-teal">|</span>
                <a href="{{ url('/salas') }}" class="text-text-muted hover:text-white">Laboratorios</a>
                <span class="text-muted-teal">|</span>
                <a href="{{ url('/horarios') }}" class="text-text-muted hover:text-white">Horarios</a>
            </div>
        </div>
    </footer>

    @include('partials.bm-interactivo')

    @stack('scripts')
</body>
</html>
