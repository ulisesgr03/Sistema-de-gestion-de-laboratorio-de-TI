<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="shell-type" content="web_standard">

    <title>@yield('title', 'Laboratorios de TI') · {{ $institutionName ?? config('app.institution_name', 'MetalNet') }}</title>

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

                        // Aliases para compatibilidad total
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
            /* Paleta Oficial Stitch (UPTex TI / MetalNet Academic Operations) */
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

            /* 3 Únicos Colores Semánticos de Estado (Verde / Amarillo / Rojo) */
            --status-available:   #2f664f;
            --status-occupied:    #f59e0b;
            --status-unavailable: #ef4444;

            /* Jerarquía de Texto para Alto Contraste */
            --text-main:    #1b1c18;
            --text-sub:     #404944;
            --text-muted:   #707973;

            /* Aliases de compatibilidad */
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

            /* 3 Únicos Colores Semánticos */
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

        body {
            background-color: var(--surface);
            color: var(--on-surface);
            font-family: 'Alegreya Sans', 'Work Sans', sans-serif;
        }

        /* Enlaces rápidos y accesibilidad */
        .skip-link {
            position: absolute;
            top: -40px;
            left: 0;
            background: var(--primary);
            color: #ffffff;
            padding: 8px;
            z-index: 100;
        }
        .skip-link:focus {
            top: 0;
        }

        /* Cápsula de navegación flotante estilo Stitch institucional */
        .mn-pill-navbar {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
            border-radius: 9999px;
            padding: 6px 12px;
            border: 1px solid var(--surface-container-highest);
            box-shadow: 0 4px 20px -2px rgba(27, 28, 24, 0.08), 0 2px 6px -1px rgba(27, 28, 24, 0.04);
        }

        .mn-pill-link {
            color: var(--on-surface-variant);
            font-weight: 600;
            font-size: 13px;
            padding: 7px 15px;
            border-radius: 9999px;
            transition: all 0.16s ease;
            white-space: nowrap;
        }

        .mn-pill-link:hover {
            color: var(--on-surface);
            background: var(--surface-container);
        }

        .mn-pill-link.is-active {
            color: #FFFFFF;
            background: var(--primary);
            font-weight: 700;
            box-shadow: 0 2px 8px rgba(47, 102, 79, 0.25);
        }

        /* Cápsula compacta de simbología de 3 estados (Verde / Amarillo / Rojo) */
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

    <!-- ======================================================================= -->
    <!-- NAVEGACIÓN SUPERIOR EN CÁPSULA · METALNET                               -->
    <!-- ======================================================================= -->
    <header class="fixed top-3.5 left-0 right-0 z-50 px-3 sm:px-6 pointer-events-none">
        @php
            $isRoute = fn($pattern) => request()->is($pattern) || request()->routeIs($pattern);
            $onInicio = $isRoute('/') || $isRoute('inicio*');
            $onSalas = $isRoute('salas*');
            $onHorarios = $isRoute('horarios*');
            $onSoftware = $isRoute('software*');
            $totalesNav = $bmTotales ?? ['disponible' => 88];
        @endphp

        <div class="max-w-[1080px] mx-auto mn-pill-navbar flex items-center justify-between gap-2 pointer-events-auto">
            <!-- Identidad Institucional MetalNet -->
            <a href="{{ url('/') }}"
               class="flex items-center gap-2.5 pr-2 rounded-full hover:opacity-95 transition-all shrink-0"
               title="{{ $institutionFull ?? 'MetalNet · Laboratorios de Ingeniería en Sistemas' }}">
                <img src="{{ asset('images/brand/metalnet-logo.svg') }}"
                     alt="MetalNet Logo"
                     class="w-10 h-10 rounded-full shrink-0 border border-surface-container-highest shadow-sm">
                <div class="hidden md:block leading-tight">
                    <span class="text-sm font-extrabold tracking-tight text-on-surface block">
                        {{ $institutionName ?? config('app.institution_name', 'MetalNet') }}
                    </span>
                    <span class="text-[10px] font-semibold uppercase tracking-wider text-outline block">
                        Laboratorios de Ingeniería
                    </span>
                </div>
            </a>

            <!-- Estructura de Navegación Principal (Mapa 3D, Laboratorios, Horarios, Software) -->
            <nav class="flex items-center gap-1 overflow-x-auto px-1" aria-label="Navegación Principal">
                <a href="{{ url('/') }}" class="mn-pill-link {{ $onInicio ? 'is-active' : '' }} inline-flex items-center gap-1.5">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 3L2 8l10 5 10-5-10-5z"/><path d="M2 13l10 5 10-5"/><path d="M2 18l10 5 10-5"/></svg>
                    <span>Mapa 3D</span>
                </a>
                <a href="{{ url('/salas') }}" class="mn-pill-link {{ $onSalas ? 'is-active' : '' }} inline-flex items-center gap-1.5">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8"/><path d="M12 17v4"/></svg>
                    <span>Laboratorios</span>
                    <span class="px-2 py-0.5 rounded-full {{ $onSalas ? 'bg-white/20 text-white' : 'bg-surface-container text-primary font-bold border border-surface-container-highest' }} font-mono text-[11px] font-bold" data-total-count="disponible">{{ $totalesNav['disponible'] ?? 88 }}</span>
                </a>
                <a href="{{ url('/horarios') }}" class="mn-pill-link {{ $onHorarios ? 'is-active' : '' }} inline-flex items-center gap-1.5">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    <span>Horarios</span>
                </a>
                <a href="{{ url('/software') }}" class="mn-pill-link {{ $onSoftware ? 'is-active' : '' }} inline-flex items-center gap-1.5">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="16 18 22 12 16 6"/><polyline points="8 6 2 12 8 18"/></svg>
                    <span>Software</span>
                </a>
            </nav>

            <!-- Cápsula Derecha de Acceso / Panel Privado por Rol -->
            <div class="flex items-center gap-1.5 shrink-0">
                <button type="button"
                        data-open-map="1"
                        class="hidden sm:inline-flex items-center justify-center w-10 h-10 rounded-full bg-surface-container hover:bg-surface-container-high text-on-surface border border-surface-container-highest transition-colors cursor-pointer shadow-sm"
                        title="Inspección de las 200 estaciones">
                    <svg class="w-4 h-4 text-primary" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                </button>

                @auth
                    <a href="{{ Auth::user()->dashboardRoute() }}"
                       class="h-10 px-4 rounded-full bg-surface-container-lowest hover:bg-surface-container text-on-surface font-bold text-xs inline-flex items-center gap-2 transition-all border border-surface-container-highest shadow-sm">
                        <span class="w-2 h-2 rounded-full bg-primary"></span>
                        <span id="nav-pill-user-label" class="truncate max-w-[140px]">{{ Auth::user()->primaryRoleLabel() }} · {{ Auth::user()->name }}</span>
                    </a>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit"
                                title="Cerrar sesión"
                                class="w-10 h-10 rounded-full bg-surface-container-lowest hover:bg-error hover:text-white text-on-surface-variant border border-surface-container-highest flex items-center justify-center transition-colors cursor-pointer shadow-sm">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                        </button>
                    </form>
                @else
                    <a href="{{ url('/login') }}"
                       class="h-10 px-4 rounded-full bg-primary hover:bg-primary-container text-white font-bold text-xs inline-flex items-center gap-2 transition-all shadow-sm">
                        <svg class="w-3.5 h-3.5 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                        <span id="nav-pill-user-label">Acceso</span>
                    </a>
                @endauth
            </div>
        </div>
    </header>

    <!-- ========================================== -->
    <!-- ÁREA DE CONTENIDO PRINCIPAL                -->
    <!-- ========================================== -->
    <main class="w-full pt-[84px] flex-1">
        <div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8 py-3">

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

    <!-- Pie de página institucional MetalNet -->
    <footer class="w-full bg-surface-container-lowest border-t border-surface-container-highest mt-auto">
        <div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8 py-4 flex flex-wrap items-center justify-between gap-3 text-xs text-on-surface-variant">
            <div class="flex items-center gap-2.5 font-bold text-on-surface">
                <img src="{{ asset('images/brand/metalnet-logo.svg') }}" alt="MetalNet" class="w-5 h-5 rounded-full border border-surface-container-highest shadow-sm">
                <span>{{ $institutionFull ?? 'MetalNet · Laboratorios de Ingeniería en Sistemas' }}</span>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ url('/') }}" class="text-primary font-semibold hover:underline">Volver al Inicio (Mapa 3D)</a>
                <span class="text-outline-variant">|</span>
                <button type="button" data-open-map="1" class="text-on-surface-variant font-semibold hover:text-primary cursor-pointer">200 Estaciones</button>
                <span class="text-outline-variant">|</span>
                @auth
                    <a href="{{ Auth::user()->dashboardRoute() }}" class="text-primary font-bold hover:underline">Ir a mi Panel ({{ Auth::user()->primaryRoleLabel() }})</a>
                @else
                    <a href="{{ url('/login') }}" class="text-on-surface-variant font-semibold hover:text-primary">Iniciar Sesión</a>
                    <span class="text-outline-variant">|</span>
                    <a href="{{ url('/register') }}" class="text-on-surface-variant font-semibold hover:text-primary">Registro</a>
                @endauth
            </div>
        </div>
    </footer>

    @include('partials.bm-interactivo')

    @stack('scripts')
</body>
</html>
