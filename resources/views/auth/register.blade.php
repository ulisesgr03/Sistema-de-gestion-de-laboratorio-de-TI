<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Registro de Usuario · {{ config('app.institution_name', 'MetalNet') }}</title>

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
            --outline: #707973;
            --outline-variant: #c0c9c2;
            --primary: #2f664f;
            --on-primary: #ffffff;
            --primary-container: #497f67;
            --on-primary-container: #f5fff7;
            --secondary: #316a42;
            --secondary-container: #b3f1be;
            --error: #ba1a1a;
            --error-container: #ffdad6;

            --status-available:   #2f664f;
            --status-occupied:    #f59e0b;
            --status-unavailable: #ef4444;

            --text-main:    #1b1c18;
            --text-sub:     #404944;
            --text-muted:   #707973;

            --dark-spruce: var(--surface-container-lowest);
            --sea-green:   var(--surface-container-low);
            --muted-teal:  var(--surface-container-highest);
            --icy-blue:    var(--primary);
            --tea-green:   var(--secondary-container);
        }
        body {
            background-color: var(--surface);
            color: var(--on-surface);
            font-family: 'Alegreya Sans', 'Work Sans', sans-serif;
        }
    </style>
</head>
<body class="antialiased min-h-screen flex flex-col justify-between p-4 bg-surface text-on-surface">

    <header class="w-full max-w-4xl mx-auto pt-2">
        <div class="rounded-2xl border border-surface-container-highest p-2.5 flex items-center justify-between bg-surface-container-lowest shadow-sm">
            <a href="{{ url('/') }}" class="flex items-center gap-2.5 pl-1 pr-3" title="Volver al Mapa 3D">
                <img src="{{ asset('images/brand/metalnet-logo.svg') }}" alt="MetalNet" class="w-9 h-9 rounded-lg border border-surface-container-highest shadow-sm">
                <div>
                    <span class="text-sm font-black tracking-tight block leading-none text-on-surface">{{ config('app.institution_name', 'MetalNet') }}</span>
                    <span class="text-[10px] font-semibold uppercase tracking-widest hidden sm:block mt-0.5 text-primary">Ingeniería en Sistemas</span>
                </div>
            </a>
            <div class="flex items-center gap-1 sm:gap-2 text-xs font-semibold text-on-surface-variant">
                <a href="{{ url('/') }}" class="px-3 py-1.5 rounded-lg hover:bg-surface-container hover:text-on-surface transition inline-flex items-center gap-1">
                    <svg class="w-3.5 h-3.5 text-primary" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 3L2 8l10 5 10-5-10-5z"/><path d="M2 13l10 5 10-5"/><path d="M2 18l10 5 10-5"/></svg>
                    <span>Inicio (3D)</span>
                </a>
                <a href="{{ url('/salas') }}" class="px-3 py-1.5 rounded-lg hover:bg-surface-container hover:text-on-surface transition">Laboratorios</a>
                <a href="{{ url('/horarios') }}" class="px-3 py-1.5 rounded-lg hover:bg-surface-container hover:text-on-surface transition">Horarios</a>
                <a href="{{ url('/software') }}" class="px-3 py-1.5 rounded-lg hover:bg-surface-container hover:text-on-surface transition">Software</a>
            </div>
            <a href="{{ route('login') }}"
               class="h-9 px-4 rounded-xl text-white font-extrabold text-xs flex items-center transition hover:bg-primary-container bg-primary shadow-sm">
                Iniciar Sesión
            </a>
        </div>
    </header>

    <main class="w-full max-w-[500px] mx-auto my-6">
        <div class="rounded-3xl p-6 sm:p-8 border border-surface-container-highest space-y-5 bg-surface-container-lowest shadow-lg">
            <div class="flex items-center gap-3.5 pb-4 border-b border-surface-container-highest">
                <img src="{{ asset('images/brand/metalnet-logo.svg') }}" alt="MetalNet" class="w-12 h-12 rounded-xl shrink-0 border border-surface-container-highest shadow-sm">
                <div>
                    <span class="px-2 py-0.5 rounded border border-surface-container-highest font-mono text-[10px] font-bold uppercase tracking-wider bg-surface-container text-primary">
                        Alta de Cuenta Institucional
                    </span>
                    <h1 class="text-xl font-black mt-1 leading-tight text-on-surface">Registro de Usuario</h1>
                    <p class="text-xs text-on-surface-variant">
                        {{ config('app.institution_full', 'MetalNet · Laboratorios de Ingeniería en Sistemas') }}
                    </p>
                </div>
            </div>

            @if ($errors->any())
                <div class="p-3.5 rounded-xl border border-[#fca5a5] text-xs font-bold space-y-1 bg-[#ffdad6] text-[#991b1b]">
                    @foreach ($errors->all() as $error)
                        <p class="flex items-center gap-2">
                            <svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                            <span>{{ $error }}</span>
                        </p>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}" class="space-y-3.5">
                @csrf
                <div>
                    <label for="name" class="block text-[11px] font-extrabold uppercase tracking-wider mb-1 text-on-surface-variant">
                        Nombre Completo *
                    </label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required
                           placeholder="Ej. Ing. Daniel Freeman"
                           class="w-full h-11 px-4 rounded-xl border border-outline-variant text-sm font-semibold focus:outline-none focus:border-primary focus:bg-surface-container-lowest transition-colors bg-surface-container-low text-on-surface">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label for="email" class="block text-[11px] font-extrabold uppercase tracking-wider mb-1 text-on-surface-variant">
                            Correo Institucional *
                        </label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" required
                               placeholder="usuario@metalnet.edu"
                               class="w-full h-11 px-3.5 rounded-xl border border-outline-variant text-sm font-semibold focus:outline-none focus:border-primary focus:bg-surface-container-lowest transition-colors bg-surface-container-low text-on-surface">
                    </div>
                    <div>
                        <label for="matricula" class="block text-[11px] font-extrabold uppercase tracking-wider mb-1 text-on-surface-variant">
                            Matrícula / ID *
                        </label>
                        <input type="text" id="matricula" name="matricula" value="{{ old('matricula') }}" required
                               placeholder="MN-ALU-2026"
                               class="w-full h-11 px-3.5 rounded-xl border border-outline-variant font-mono text-sm font-bold focus:outline-none focus:border-primary focus:bg-surface-container-lowest transition-colors bg-surface-container-low text-primary">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label for="rol" class="block text-[11px] font-extrabold uppercase tracking-wider mb-1 text-on-surface-variant">
                            Perfil Institucional *
                        </label>
                        <select id="rol" name="rol" required
                                class="w-full h-11 px-3.5 rounded-xl border border-outline-variant text-sm font-bold focus:outline-none focus:border-primary focus:bg-surface-container-lowest transition-colors bg-surface-container-low text-on-surface cursor-pointer">
                            <option value="alumno" {{ old('rol') === 'alumno' ? 'selected' : '' }}>Alumno (Prácticas y Reserva PC)</option>
                            <option value="docente" {{ old('rol') === 'docente' ? 'selected' : '' }}>Docente (Salas y Bloques Académicos)</option>
                        </select>
                    </div>
                    <div>
                        <label for="telefono" class="block text-[11px] font-extrabold uppercase tracking-wider mb-1 text-on-surface-variant">
                            Extensión / Teléfono
                        </label>
                        <input type="text" id="telefono" name="telefono" value="{{ old('telefono') }}"
                               placeholder="Ext. 8000"
                               class="w-full h-11 px-3.5 rounded-xl border border-outline-variant text-sm font-semibold focus:outline-none focus:border-primary focus:bg-surface-container-lowest transition-colors bg-surface-container-low text-on-surface">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label for="password" class="block text-[11px] font-extrabold uppercase tracking-wider mb-1 text-on-surface-variant">
                            Contraseña *
                        </label>
                        <input type="password" id="password" name="password" required minlength="8"
                               placeholder="Mínimo 8 caracteres"
                               class="w-full h-11 px-3.5 rounded-xl border border-outline-variant text-sm font-semibold focus:outline-none focus:border-primary focus:bg-surface-container-lowest transition-colors bg-surface-container-low text-on-surface">
                    </div>
                    <div>
                        <label for="password_confirmation" class="block text-[11px] font-extrabold uppercase tracking-wider mb-1 text-on-surface-variant">
                            Confirmar Contraseña *
                        </label>
                        <input type="password" id="password_confirmation" name="password_confirmation" required minlength="8"
                               placeholder="Repetir contraseña"
                               class="w-full h-11 px-3.5 rounded-xl border border-outline-variant text-sm font-semibold focus:outline-none focus:border-primary focus:bg-surface-container-lowest transition-colors bg-surface-container-low text-on-surface">
                    </div>
                </div>

                <button type="submit"
                        class="w-full h-11 rounded-xl text-white font-black text-sm flex items-center justify-center gap-2 cursor-pointer transition hover:bg-primary-container shadow-sm bg-primary mt-2">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="4"/><line x1="20" y1="8" x2="20" y2="14"/><line x1="23" y1="11" x2="17" y2="11"/></svg>
                    <span>Completar Registro e Ingresar</span>
                </button>
            </form>

            <div class="pt-3 border-t border-surface-container-highest text-center text-xs text-on-surface-variant">
                <span>¿Ya tienes cuenta institucional?</span>
                <a href="{{ route('login') }}" class="font-bold text-primary hover:underline ml-1">Iniciar Sesión</a>
            </div>
        </div>
    </main>

    <footer class="text-center text-xs pb-2 text-outline">
        <a href="{{ url('/') }}" class="text-primary hover:underline font-semibold">Volver al Inicio (Mapa 3D)</a>
        <span class="mx-2 text-outline-variant">·</span>
        <span>{{ config('app.institution_full', 'MetalNet · Laboratorios de Ingeniería en Sistemas') }}</span>
    </footer>
</body>
</html>
