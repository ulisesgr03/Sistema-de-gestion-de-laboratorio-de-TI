@extends('layouts.public')

@section('title', 'Reglamento Oficial de Laboratorios de TI')

@section('header')
<div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-4 border-b border-[#e4e3db]">
    <div>
        <div class="flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-[#675a4e]">
            <span class="material-symbols-outlined text-[16px] text-[#2f664f]">gavel</span>
            <span>Normativa Institucional Vigente · UPTex</span>
        </div>
        <h1 class="text-2xl sm:text-3xl font-extrabold text-[#1b1c18] mt-1 tracking-tight">
            Reglamento General de los Laboratorios de Tecnologías de la Información
        </h1>
        <p class="text-xs sm:text-sm text-[#404944] mt-1">
            Marco regulatorio de acceso, permanencia, custodia patrimonial, ciberseguridad y disciplina técnica en las salas de cómputo especializadas.
        </p>
    </div>

    <!-- Indicador de Publicación -->
    <div class="flex items-center gap-2 px-3 py-1.5 rounded-lg bg-[#ffffff] border border-[#e4e3db] text-xs font-mono">
        <span class="material-symbols-outlined text-[16px] text-[#316a42]">verified</span>
        <span class="text-[#1b1c18] font-bold">Aprobado por Consejo Académico</span>
    </div>
</div>
@endsection

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-4 gap-6">

    <!-- Menú Lateral de Capítulos (Índice Rápido) -->
    <div class="lg:col-span-1">
        <div class="bg-[#ffffff] border border-[#e4e3db] rounded-2xl p-5 sticky top-[130px] shadow-sm">
            <h3 class="text-xs font-bold uppercase tracking-wider text-[#675a4e] mb-3 flex items-center gap-1.5">
                <span class="material-symbols-outlined text-[16px] text-[#2f664f]">menu_book</span>
                <span>Índice del Reglamento</span>
            </h3>
            <nav class="space-y-1 text-xs">
                <a href="#cap1" class="block px-3 py-2 rounded-lg text-[#404944] hover:bg-[#efeee7] hover:text-[#1b1c18] font-medium transition-colors">
                    Cap. I: Disposiciones Generales
                </a>
                <a href="#cap2" class="block px-3 py-2 rounded-lg text-[#404944] hover:bg-[#efeee7] hover:text-[#1b1c18] font-medium transition-colors">
                    Cap. II: Derechos y Obligaciones
                </a>
                <a href="#cap3" class="block px-3 py-2 rounded-lg text-[#404944] hover:bg-[#efeee7] hover:text-[#1b1c18] font-medium transition-colors">
                    Cap. III: Uso de Hardware y Software
                </a>
                <a href="#cap4" class="block px-3 py-2 rounded-lg text-[#404944] hover:bg-[#efeee7] hover:text-[#1b1c18] font-medium transition-colors">
                    Cap. IV: Prohibiciones Expresas
                </a>
                <a href="#cap5" class="block px-3 py-2 rounded-lg text-[#404944] hover:bg-[#efeee7] hover:text-[#1b1c18] font-medium transition-colors">
                    Cap. V: Préstamos en Ventanilla
                </a>
                <a href="#cap6" class="block px-3 py-2 rounded-lg text-[#404944] hover:bg-[#efeee7] hover:text-[#1b1c18] font-medium transition-colors">
                    Cap. VI: Régimen de Sanciones
                </a>
            </nav>

            <div class="mt-6 pt-4 border-t border-[#e4e3db] text-xs text-[#707973] space-y-2">
                <div class="flex items-center gap-1.5 text-[#1b1c18] font-semibold">
                    <span class="material-symbols-outlined text-[16px] text-[#2f664f]">contact_support</span>
                    <span>¿Dudas de aplicación?</span>
                </div>
                <p>Acuda a la Jefatura de Laboratorios de TI en el Edificio E, Planta Baja.</p>
            </div>
        </div>
    </div>

    <!-- Articulado Completo del Reglamento -->
    <div class="lg:col-span-3 space-y-6">

        <!-- Cap. I -->
        <article id="cap1" class="bg-[#ffffff] border border-[#e4e3db] rounded-2xl p-6 sm:p-8 shadow-sm">
            <span class="text-[11px] font-bold uppercase tracking-wider text-[#2f664f]">Capítulo I</span>
            <h2 class="text-xl font-bold text-[#1b1c18] mt-1 mb-4">Disposiciones Generales y Ámbito de Aplicación</h2>
            <div class="space-y-3 text-xs sm:text-sm text-[#404944] leading-relaxed">
                <p>
                    <strong class="text-[#1b1c18]">Artículo 1.</strong> El presente reglamento es de observancia obligatoria para todos los alumnos, docentes, investigadores, personal administrativo y usuarios externos que hagan uso de las instalaciones, equipos, redes e instrumental de los laboratorios adscritos a la División de Tecnologías de la Información de la Universidad Politécnica de Texcoco.
                </p>
                <p>
                    <strong class="text-[#1b1c18]">Artículo 2.</strong> Los laboratorios tienen como finalidad primordial proveer un entorno técnico de alto desempeño para el desarrollo de actividades académicas, proyectos curriculares, investigación científica y entrenamiento tecnológico certificado.
                </p>
            </div>
        </article>

        <!-- Cap. II -->
        <article id="cap2" class="bg-[#ffffff] border border-[#e4e3db] rounded-2xl p-6 sm:p-8 shadow-sm">
            <span class="text-[11px] font-bold uppercase tracking-wider text-[#2f664f]">Capítulo II</span>
            <h2 class="text-xl font-bold text-[#1b1c18] mt-1 mb-4">Derechos y Obligaciones de los Usuarios</h2>
            <div class="space-y-3 text-xs sm:text-sm text-[#404944] leading-relaxed">
                <p>
                    <strong class="text-[#1b1c18]">Artículo 3.</strong> Son derechos de los usuarios:
                </p>
                <ul class="list-disc pl-5 space-y-1.5 text-xs text-[#404944]">
                    <li>Acceder a las estaciones de trabajo asignadas en sus horarios curriculares y a la práctica libre en los espacios debidamente señalizados como disponibles.</li>
                    <li>Recibir asistencia del personal técnico de laboratorio para resolver incidencias de arranque de sistema o conectividad.</li>
                    <li>Solicitar préstamos de instrumental técnico de acuerdo con el catálogo de pañol disponible.</li>
                </ul>
                <p>
                    <strong class="text-[#1b1c18]">Artículo 4.</strong> Son obligaciones de los usuarios:
                </p>
                <ul class="list-disc pl-5 space-y-1.5 text-xs text-[#404944]">
                    <li>Portar en todo momento su credencial institucional UPTex visible y vigente.</li>
                    <li>Reportar de manera inmediata cualquier anomalía detectada en su estación de trabajo al iniciar su sesión técnica.</li>
                    <li>Dejar la estación de cómputo apagada o en el gestor de inicio, el teclado y ratón alineados y la silla colocada bajo el escritorio.</li>
                </ul>
            </div>
        </article>

        <!-- Cap. III -->
        <article id="cap3" class="bg-[#ffffff] border border-[#e4e3db] rounded-2xl p-6 sm:p-8 shadow-sm">
            <span class="text-[11px] font-bold uppercase tracking-wider text-[#2f664f]">Capítulo III</span>
            <h2 class="text-xl font-bold text-[#1b1c18] mt-1 mb-4">Uso de Hardware, Red LAN y Software Homologado</h2>
            <div class="space-y-3 text-xs sm:text-sm text-[#404944] leading-relaxed">
                <p>
                    <strong class="text-[#1b1c18]">Artículo 5.</strong> Queda terminantemente prohibido desconectar, intercambiar, retirar o modificar componentes internos o externos de las computadoras (cables HDMI, fuentes, cables Ethernet, tarjetas de red o módulos RAM).
                </p>
                <p>
                    <strong class="text-[#1b1c18]">Artículo 6.</strong> La red inalámbrica y cableada de los laboratorios está sujeta a auditoría de tráfico conforme a la norma ISO/IEC 27001. Queda prohibido el escaneo de puertos no autorizado, ataques de denegación de servicio o navegación en sitios ajenos al propósito académico.
                </p>
                <p>
                    <strong class="text-[#1b1c18]">Artículo 7.</strong> La instalación de software no homologado o sin licencia institucional debe solicitarse con 5 días hábiles de anticipación a la Jefatura de TI mediante formato de requerimiento docente.
                </p>
            </div>
        </article>

        <!-- Cap. IV -->
        <article id="cap4" class="bg-[#ffffff] border border-[#e4e3db] rounded-2xl p-6 sm:p-8 shadow-sm">
            <span class="text-[11px] font-bold uppercase tracking-wider text-[#ba1a1a]">Capítulo IV</span>
            <h2 class="text-xl font-bold text-[#1b1c18] mt-1 mb-4">Prohibiciones Expresas</h2>
            <div class="space-y-3 text-xs sm:text-sm text-[#404944] leading-relaxed">
                <div class="p-4 bg-[#ffdad6] border border-[#ba1a1a] rounded-xl text-[#93000a] text-xs font-medium space-y-2">
                    <p class="font-bold flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-[18px]">block</span>
                        <span>Restricciones de Tolerancia Cero:</span>
                    </p>
                    <ul class="list-disc pl-5 space-y-1">
                        <li>Ingresar con cualquier tipo de alimento, golosina, botana, bebida o envase con líquido (incluso hermético o embotellado).</li>
                        <li>Ingresar mochilas o bultos voluminosos sobre las mesas de trabajo; deberán colocarse en los anaqueles destinados para ello.</li>
                        <li>Usar las terminales para videojuegos, apuestas, minería de criptoactivos o contenidos ilícitos.</li>
                        <li>Alterar configuraciones del BIOS/UEFI, cambiar contraseñas de administrador o burlar las políticas de seguridad Active Directory.</li>
                    </ul>
                </div>
            </div>
        </article>

        <!-- Cap. V -->
        <article id="cap5" class="bg-[#ffffff] border border-[#e4e3db] rounded-2xl p-6 sm:p-8 shadow-sm">
            <span class="text-[11px] font-bold uppercase tracking-wider text-[#2f664f]">Capítulo V</span>
            <h2 class="text-xl font-bold text-[#1b1c18] mt-1 mb-4">Préstamos en Ventanilla y Valija Técnica</h2>
            <div class="space-y-3 text-xs sm:text-sm text-[#404944] leading-relaxed">
                <p>
                    <strong class="text-[#1b1c18]">Artículo 8.</strong> Todo préstamo de osciloscopios, fuentes, kits IoT o laptops requiere la entrega de credencial original y firma del vale F-TI-04 en el pañol E-102.
                </p>
                <p>
                    <strong class="text-[#1b1c18]">Artículo 9.</strong> El material deberá ser devuelto 15 minutos antes de la hora pactada en el mismo estado operativo en el que fue entregado.
                </p>
            </div>
        </article>

        <!-- Cap. VI -->
        <article id="cap6" class="bg-[#ffffff] border border-[#e4e3db] rounded-2xl p-6 sm:p-8 shadow-sm">
            <span class="text-[11px] font-bold uppercase tracking-wider text-[#2f664f]">Capítulo VI</span>
            <h2 class="text-xl font-bold text-[#1b1c18] mt-1 mb-4">Régimen de Faltas y Sanciones Disciplinarias</h2>
            <div class="space-y-3 text-xs sm:text-sm text-[#404944] leading-relaxed">
                <p>
                    <strong class="text-[#1b1c18]">Artículo 10.</strong> El incumplimiento a las disposiciones de este reglamento acarreará las siguientes sanciones acumulativas según la gravedad del incidente:
                </p>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-2 text-xs">
                    <div class="p-3 bg-[#f5f4ec] rounded-xl border border-[#e4e3db]">
                        <span class="font-bold text-[#1b1c18] block mb-1">Amonestación Escrita</span>
                        <p class="text-[#404944]">Por ingreso de líquidos o alimentos, o dejar desordenada la estación.</p>
                    </div>
                    <div class="p-3 bg-[#f5f4ec] rounded-xl border border-[#e4e3db]">
                        <span class="font-bold text-[#ba1a1a] block mb-1">Suspensión de Servicio</span>
                        <p class="text-[#404944]">De 1 a 4 semanas de acceso a laboratorios y préstamo de equipo por reincidencia o maltrato.</p>
                    </div>
                    <div class="p-3 bg-[#f5f4ec] rounded-xl border border-[#e4e3db]">
                        <span class="font-bold text-[#ba1a1a] block mb-1">Reposición Patrimonial</span>
                        <p class="text-[#404944]">Pago o sustitución de componentes dañados y canalización ante el Consejo de Honor y Justicia.</p>
                    </div>
                </div>
            </div>
        </article>

    </div>
</div>
@endsection
