<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="shell-type" content="web_blank">

    <title>Acceso Institucional · UPTex TI</title>

    <!-- Fuentes Oficiales: Work Sans & Google Material Symbols Outlined -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Work+Sans:wght@400;500;600;700;800&family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&display=swap" rel="stylesheet">

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
            display: none;
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
                    borderRadius: {
                        DEFAULT: "0.25rem",
                        lg: "0.5rem",
                        xl: "0.75rem",
                        full: "9999px"
                    },
                    spacing: {
                        gutter: "1.5rem",
                        margin: "2rem",
                        "space-xs": "0.25rem",
                        "space-sm": "0.5rem",
                        "space-md": "1rem",
                        "space-lg": "1.5rem",
                        "space-xl": "2rem"
                    },
                    fontFamily: {
                        sans: ['Work Sans', 'sans-serif'],
                        headline: ['Work Sans', 'sans-serif']
                    }
                }
            }
        };
    </script>
</head>
<body class="bg-[#fbf9f2] font-sans text-[#1b1c18] antialiased min-h-screen flex items-center justify-center p-4 sm:p-6 selection:bg-[#b5efd2] selection:text-[#002115]">
    <main class="w-full max-w-[540px]">
        <div class="flex flex-col gap-4">

            <!-- Barra Superior de Navegación -->
            <div class="flex items-center justify-between px-1 py-1 text-[#404944] text-xs">
                <div class="flex items-center gap-1.5 font-medium text-[#1b1c18]">
                    <span class="material-symbols-outlined text-[#2f664f] text-[20px]">account_balance</span>
                    <span class="font-bold tracking-tight">UPTex · TI</span>
                </div>
                <a href="{{ url('/') }}" class="inline-flex items-center gap-1 text-[#2f664f] hover:text-[#1b1c18] transition-colors font-semibold">
                    <span class="material-symbols-outlined text-[16px]">arrow_back</span>
                    <span>Volver al portal público</span>
                </a>
            </div>

            <!-- Tarjeta Central de Inicio de Sesión -->
            <div class="bg-[#ffffff] rounded-2xl p-6 sm:p-8 flex flex-col gap-6 border border-[#e4e3db] shadow-sm">

                <!-- Logotipo e Identidad Institucional -->
                <div class="flex flex-col items-center text-center gap-2">
                    <div class="w-12 h-12 rounded-xl bg-[#efeee7] border border-[#e4e3db] flex items-center justify-center text-[#2f664f]">
                        <span class="material-symbols-outlined text-[28px]">terminal</span>
                    </div>
                    <div class="flex flex-col gap-0.5 mt-1">
                        <h1 class="text-xl sm:text-2xl font-extrabold text-[#1b1c18] tracking-tight">
                            Acceso al Sistema de Laboratorios de TI
                        </h1>
                        <p class="text-xs text-[#675a4e]">
                            Universidad Politécnica de Texcoco · División de Tecnologías de la Información
                        </p>
                    </div>
                </div>

                <!-- Selector de Roles Institucionales (Tabs) -->
                <div class="bg-[#efeee7] rounded-lg p-1 grid grid-cols-2 sm:grid-cols-4 gap-1 border border-[#e4e3db]" id="role-selector" role="tablist">
                    <button type="button"
                            role="tab"
                            aria-selected="true"
                            data-role="student"
                            class="tab-btn py-1.5 px-2 rounded-md text-xs text-center transition-all bg-[#ffffff] text-[#1b1c18] font-bold shadow-sm">
                        Estudiante
                    </button>
                    <button type="button"
                            role="tab"
                            aria-selected="false"
                            data-role="faculty"
                            class="tab-btn py-1.5 px-2 rounded-md text-xs text-center transition-all text-[#675a4e] hover:text-[#1b1c18] font-medium">
                        Docente
                    </button>
                    <button type="button"
                            role="tab"
                            aria-selected="false"
                            data-role="staff"
                            class="tab-btn py-1.5 px-2 rounded-md text-xs text-center transition-all text-[#675a4e] hover:text-[#1b1c18] font-medium">
                        Personal Técnico
                    </button>
                    <button type="button"
                            role="tab"
                            aria-selected="false"
                            data-role="admin"
                            class="tab-btn py-1.5 px-2 rounded-md text-xs text-center transition-all text-[#675a4e] hover:text-[#1b1c18] font-medium">
                        Administrador
                    </button>
                </div>

                <!-- Notificaciones de Error -->
                @if ($errors->any())
                    <div class="p-3.5 rounded-xl bg-[#ffdad6] border border-[#ba1a1a] text-[#93000a] text-xs flex items-start gap-2.5" role="alert">
                        <span class="material-symbols-outlined text-[18px] text-[#ba1a1a] shrink-0 mt-0.5">error</span>
                        <div class="flex-1 font-medium leading-relaxed">
                            @foreach ($errors->all() as $error)
                                <p>{{ $error }}</p>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Formulario de Autenticación -->
                <form method="POST" action="{{ route('login') }}" class="flex flex-col gap-4">
                    @csrf

                    <!-- Identificador Institucional -->
                    <div class="flex flex-col gap-1.5">
                        <label for="identifier" class="text-xs font-bold text-[#675a4e] flex items-center justify-between">
                            <span id="label-identifier">Matrícula institucional</span>
                            <span id="hint-format" class="text-[11px] font-normal text-[#707973]">10 dígitos numéricos</span>
                        </label>
                        <div class="relative flex items-center">
                            <span class="material-symbols-outlined absolute left-3 text-[#707973] pointer-events-none text-[20px]">badge</span>
                            <input type="text"
                                   id="identifier"
                                   name="identifier"
                                   value="{{ old('identifier') }}"
                                   placeholder="Ejemplo: 2021134089"
                                   required
                                   autocomplete="username"
                                   class="w-full h-10 pl-10 pr-3 bg-[#ffffff] rounded-lg border border-[#c0c9c2] text-sm text-[#1b1c18] placeholder-[#707973] focus:outline-none focus:border-[#2f664f] focus:ring-1 focus:ring-[#2f664f] transition-all">
                        </div>
                        <span id="identifier-desc" class="text-[11px] text-[#707973] px-1">
                            Registro oficial de matrícula de Servicios Escolares.
                        </span>
                    </div>

                    <!-- Contraseña Active Directory / LDAP -->
                    <div class="flex flex-col gap-1.5">
                        <label for="password" class="text-xs font-bold text-[#675a4e]">
                            Contraseña de Dominio Active Directory / LDAP
                        </label>
                        <div class="relative flex items-center">
                            <span class="material-symbols-outlined absolute left-3 text-[#707973] pointer-events-none text-[20px]">key</span>
                            <input type="password"
                                   id="password"
                                   name="password"
                                   placeholder="••••••••••••"
                                   required
                                   autocomplete="current-password"
                                   class="w-full h-10 pl-10 pr-10 bg-[#ffffff] rounded-lg border border-[#c0c9c2] text-sm text-[#1b1c18] placeholder-[#707973] focus:outline-none focus:border-[#2f664f] focus:ring-1 focus:ring-[#2f664f] transition-all">
                            <button type="button"
                                    id="toggle-password"
                                    aria-label="Alternar visibilidad de contraseña"
                                    class="absolute right-2.5 p-1 text-[#707973] hover:text-[#1b1c18] transition-colors rounded">
                                <span class="material-symbols-outlined text-[18px]" id="eye-icon">visibility</span>
                            </button>
                        </div>
                    </div>

                    <!-- Sede / Laboratorio Concurrente (Opcional) -->
                    <div class="flex flex-col gap-1.5">
                        <label for="laboratory" class="text-xs font-bold text-[#675a4e]">
                            Sede o laboratorio concurrente (opcional)
                        </label>
                        <div class="relative flex items-center">
                            <span class="material-symbols-outlined absolute left-3 text-[#707973] pointer-events-none text-[20px]">dns</span>
                            <select id="laboratory"
                                    name="laboratory"
                                    class="w-full h-10 pl-10 pr-8 bg-[#ffffff] rounded-lg border border-[#c0c9c2] text-xs sm:text-sm text-[#1b1c18] appearance-none focus:outline-none focus:border-[#2f664f] focus:ring-1 focus:ring-[#2f664f] transition-colors cursor-pointer">
                                <option value="all">Acceso general de TI · Conexión distribuida</option>
                                @foreach($laboratorios ?? [] as $lab)
                                    <option value="{{ $lab->id }}" {{ old('laboratory') == $lab->id ? 'selected' : '' }}>
                                        {{ $lab->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            <span class="material-symbols-outlined absolute right-3 text-[#707973] pointer-events-none text-[20px]">expand_more</span>
                        </div>
                    </div>

                    <!-- Recordar Sesión en este Equipo -->
                    <div class="flex items-center justify-between pt-1">
                        <label class="flex items-center gap-2 cursor-pointer select-none">
                            <input type="checkbox"
                                   id="remember"
                                   name="remember"
                                   class="w-4 h-4 rounded border-[#707973] text-[#2f664f] focus:ring-0 focus:ring-offset-0 cursor-pointer">
                            <span class="text-xs text-[#1b1c18]">Mantener sesión iniciada en este equipo seguro</span>
                        </label>
                    </div>

                    <!-- Botón de Envío -->
                    <button type="submit"
                            class="w-full h-11 mt-2 bg-[#2f664f] hover:bg-[#497f67] text-white font-bold text-sm rounded-lg flex items-center justify-center gap-2 transition-all active:scale-[0.99] shadow-sm">
                        <span class="material-symbols-outlined text-[20px]">login</span>
                        <span>Iniciar sesión institucional</span>
                    </button>
                </form>

                <!-- Tarjeta de Mesa de Ayuda Técnica -->
                <div class="bg-[#f5f4ec] rounded-xl p-4 flex flex-col gap-2.5 border border-[#e4e3db]">
                    <div class="flex items-start gap-2.5">
                        <span class="material-symbols-outlined text-[#675a4e] text-[20px] shrink-0 mt-0.5">contact_support</span>
                        <div class="flex flex-col gap-0.5">
                            <p class="text-[11px] font-extrabold uppercase tracking-wider text-[#1b1c18]">
                                Mesa de ayuda técnica y credenciales
                            </p>
                            <p class="text-xs text-[#404944] leading-relaxed">
                                ¿Olvidó su contraseña o requiere el restablecimiento de su cuenta en Active Directory? Acuda a soporte técnico en el <strong class="text-[#1b1c18] font-bold">Edificio E, planta baja</strong>.
                            </p>
                        </div>
                    </div>
                    <div class="flex flex-wrap items-center justify-between gap-2 pt-2 border-t border-[#e4e3db] text-xs font-mono text-[#675a4e]">
                        <div class="flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-[16px]">call</span>
                            <span>Ext. 1420 / 1422</span>
                        </div>
                        <a href="mailto:soporte.laboratorios@uptex.edu.mx" class="flex items-center gap-1.5 text-[#2f664f] hover:underline font-sans font-semibold">
                            <span class="material-symbols-outlined text-[16px]">mail</span>
                            <span>soporte.laboratorios@uptex.edu.mx</span>
                        </a>
                    </div>
                </div>

            </div>

            <!-- Aviso de Normativa y Seguridad Institucional -->
            <div class="bg-[#f5f4ec] rounded-xl p-4 flex items-start gap-3 text-[#404944] border border-[#e4e3db]">
                <span class="material-symbols-outlined text-[#707973] text-[20px] shrink-0 mt-0.5">verified_user</span>
                <p class="text-xs leading-relaxed">
                    <strong class="font-bold text-[#1b1c18]">Normativa Institucional:</strong> El acceso a este sistema está monitoreado bajo el Reglamento General de Tecnologías de la Información de la UPTex. El uso de credenciales ajenas amerita sanción académica y administrativa inmediata.
                </p>
            </div>

        </div>
    </main>

    <!-- Script de Interactividad de Roles y Toggle Password -->
    <script>
        (function () {
            var tabs = document.querySelectorAll('.tab-btn');
            var labelIdentifier = document.getElementById('label-identifier');
            var hintFormat = document.getElementById('hint-format');
            var inputIdentifier = document.getElementById('identifier');
            var descIdentifier = document.getElementById('identifier-desc');

            var roleConfig = {
                student: {
                    label: 'Matrícula institucional',
                    placeholder: 'Ejemplo: 2021134089',
                    hint: '10 dígitos numéricos',
                    desc: 'Registro oficial de matrícula expedido por Servicios Escolares.'
                },
                faculty: {
                    label: 'Clave de docente o investigador',
                    placeholder: 'Ejemplo: DOC-0892',
                    hint: 'Formato DOC-XXXX',
                    desc: 'Identificador del claustro docente asignado por la División de TI.'
                },
                staff: {
                    label: 'Clave de empleado o personal técnico',
                    placeholder: 'Ejemplo: ADM-041',
                    hint: 'Formato ADM-XXX',
                    desc: 'Número de nómina técnica asignado al personal encargado de laboratorio.'
                },
                admin: {
                    label: 'Clave de administrador o identificador de gestión',
                    placeholder: 'Ejemplo: ADMIN-001 o admin@uptex.edu.mx',
                    hint: 'Formato ADMIN-XXX o correo institucional',
                    desc: 'Acceso con privilegios de administración global, auditoría y control de sistema.'
                }
            };

            tabs.forEach(function (btn) {
                btn.addEventListener('click', function () {
                    tabs.forEach(function (b) {
                        b.setAttribute('aria-selected', 'false');
                        b.className = 'tab-btn py-1.5 px-2 rounded-md text-xs text-center transition-all text-[#675a4e] hover:text-[#1b1c18] font-medium';
                    });
                    btn.setAttribute('aria-selected', 'true');
                    btn.className = 'tab-btn py-1.5 px-2 rounded-md text-xs text-center transition-all bg-[#ffffff] text-[#1b1c18] font-bold shadow-sm';

                    var role = btn.getAttribute('data-role');
                    if (roleConfig[role]) {
                        labelIdentifier.textContent = roleConfig[role].label;
                        hintFormat.textContent = roleConfig[role].hint;
                        inputIdentifier.placeholder = roleConfig[role].placeholder;
                        descIdentifier.textContent = roleConfig[role].desc;
                    }
                });
            });

            var togglePassword = document.getElementById('toggle-password');
            var passwordInput = document.getElementById('password');
            var eyeIcon = document.getElementById('eye-icon');

            if (togglePassword && passwordInput && eyeIcon) {
                togglePassword.addEventListener('click', function () {
                    var isPassword = passwordInput.getAttribute('type') === 'password';
                    passwordInput.setAttribute('type', isPassword ? 'text' : 'password');
                    eyeIcon.textContent = isPassword ? 'visibility_off' : 'visibility';
                });
            }
        })();
    </script>
</body>
</html>
