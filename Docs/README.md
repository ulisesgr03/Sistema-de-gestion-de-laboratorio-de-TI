# Sistema de Diseño y Guía de Estilos · UPTex TI
## Portal y Sistema de Gestión de Laboratorios de TI — Universidad Politécnica de Texcoco

> **Documento de Referencia Técnica y Visual** extraído del prototipo Stitch (`stitch_uptex_lab_manager_portal.zip`), ubicado en el directorio [`docs/`](./). Este compendio documenta la arquitectura visual, los tokens de diseño, las especificaciones tipográficas, los componentes de interfaz y la guía de integración para el proyecto Laravel con Tailwind CSS.

---

## 1. Visión General y Filosofía de Diseño

El sistema de diseño de **UPTex TI Academic Operations** está concebido como un entorno computacional institucional de **alta densidad de información** para la administración técnica y operativa de laboratorios de cómputo, soporte a la docencia, investigación de hardware/software, control de inventario didáctico y mesa de soporte para incidencias.

### Principios Rectores:
- **Utilitarismo Modernista y Rigor Institucional:** La estética descarta deliberadamente los tropos genéricos de interfaces comerciales o generadas por IA (se prohíben fondos difuminados con efecto *blur* o *glassmorphism*, degradados multicolores, acentos neón y emojis pictóricos).
- **Paradigma de Superficie Plana (*Solid Flat Paradigm*):** Toda la jerarquía se resuelve mediante superficies cromáticas delimitadas y líneas perimetrales nítidas de `1px solid #EAE6E5` o contrastes tonales, suprimiendo las sombras difusas (`box-shadow: none`).
- **Ergonomía Administrativa y Legibilidad Tabular:** Diseñado prioritariamente para estaciones de trabajo y escritorios administrativos de alta resolución (1440px o superior), garantizando una rápida lectura de direcciones IP, números de serie, estados de terminales y métricas operativas.

---

## 2. Inventario de Pantallas y Plantillas del Prototipo

El archivo comprimido contiene 8 módulos y pantallas clave totalmente prototipadas en HTML y Tailwind CSS:

| Directorio | Archivo | Módulo / Pantalla | Descripción Funcional y Componentes Clave |
| :--- | :--- | :--- | :--- |
| `inicio_de_sesi_n_institucional_uptex_ti/` | `code.html` | **Acceso Institucional** | Portal de login con selector de rol (Estudiante, Docente, Personal Técnico), autenticación de dominio Active Directory / LDAP, selector de sede/laboratorio concurrente y modo seguro. |
| `inicio_y_servicios_uptex_ti/` | `code.html` | **Portal Público y Servicios** | Vista principal de bienvenida: estado de sincronización NTP, banner de normativa ISO/IEC 27001, 4 KPIs institucionales, servicios inmediatos, avisos de mantenimiento y horarios de atención. |
| `panel_operativo_uptex_ti/` | `code.html` | **Consola Operativa (Dashboard)** | Panel técnico del Encargado de Laboratorio (Ing. Carlos Mendoza): bloqueo masivo de terminales, reservas rápidas, 4 KPIs (ocupación global 82%, parque computacional, ventanilla, incidencias) y bitácora en vivo. |
| `malla_topol_gica_uptex_ti/` | `code.html` | **Malla Topológica de Laboratorio** | Matriz física interactiva 5×6 (30 estaciones de cómputo) con visualización de pasillos, estrado docente, rack local, IPs asignadas, tiempos de sesión y *drawer* lateral de control remoto (bloqueo, reboot, terminal). |
| `mesa_de_incidencias_uptex_ti/` | `code.html` | **Mesa de Incidencias (Helpdesk)** | Tablero tipo Kanban de 4 columnas: *Reportadas*, *En Diagnóstico*, *En Reparación* y *Resueltas*. Tarjetas con etiquetas de prioridad (Alta, Media, Baja), tiempos transcurridos y formulario de nuevo ticket. |
| `inventario_y_auditor_a_uptex_ti/` | `code.html` | **Inventario y Ventanilla de Préstamos** | Módulo de registro ágil mediante lector óptico de credencial/código de barras, préstamo de equipamiento didáctico (switches Cisco, multímetros, kits IoT), auditoría y tabla de vales activos con hora límite. |
| `cat_logo_de_software_y_servicios_uptex_ti/` | `code.html` | **Catálogo de Software y Licencias** | Tabla densa de paquetería institucional, filtrado por categorías (*Desarrollo, Redes, Simulación, IA, Modelado 3D*), versiones de 64 bits, tipo de licenciamiento y laboratorios habilitados. |
| `horarios_y_pr_cticas_uptex_ti/` | `code.html` | **Programación y Prácticas Libres** | Matriz horaria semanal por turnos, asignación de laboratorios a asignaturas y docentes, bloques de prácticas no tuteladas y disponibilidad de cubículos. |
| `uptex_ti_academic_operations/` | `DESIGN.md` | **Especificación Central del Sistema** | Documento normativo con tokens de color (Material 3), tipografía, espaciado, componentes y reglas de elevación. |

---

## 3. Paleta de Colores y Tokens de Diseño

La paleta se rige por un esquema cromático sobrio basado en verdes institucionales, taupe de soporte, fondo apergaminado y bordes estructurales neutros:

### 3.1. Tokens Principales

```
Verde Institucional Primario:  #2F664F  (Jungle Teal)
Verde Operativo / Secundario:  #316A42  (Celadon Deep)
Acento Positivo / Disponible:  #8FCB9B  (Celadon Light)
Neutro Base / Tipografía:      #1B1C18  (Onyx / On-Surface)
Soporte Técnico / Labels:      #675A4E / #8F8073 (Taupe)
Lienzo / Fondo Base:           #FBF9F2  (Surface Canvas)
Tarjetas / Contenedores:       #FFFFFF  (Surface Container Lowest)
Bordes y Separadores:          #EAE6E5 / #E4E3DB (Alabaster Grey)
Error / Fallo Crítico:         #BA1A1A  (Fondo: #FFDAD6 o #FFEDEA)
Advertencia / Alerta:          #825500  (Fondo: #FFF1D6)
```

### 3.2. Tabla de Tokens Semánticos (Material Design 3 / Tailwind)

| Token Semántico | Valor Hex | Uso Previsto |
| :--- | :--- | :--- |
| `primary` | `#2f664f` | Botones de acción principal, enlaces activos, selectores destacados, iconos de identidad. |
| `on-primary` | `#ffffff` | Texto y glifos sobre fondo primario. |
| `primary-container` | `#497f67` | Hover de botón primario y acentos de navegación seleccionada. |
| `on-primary-container`| `#f5fff7` | Texto sobre contenedores primarios. |
| `primary-fixed` | `#b5efd2` | Fondos de etiquetas y acentos suaves primarios. |
| `secondary` | `#316a42` | Indicadores de terminal activa, métricas favorables, botones de confirmación. |
| `secondary-container`| `#b3f1be` | Chips de estado "Disponible / Operativo / Libre", badges positivos. |
| `on-secondary-container`| `#377047` | Tipografía para chips y badges de éxito. |
| `secondary-fixed` | `#b3f1be` | Variedad fija para fondos de estaciones disponibles. |
| `tertiary` | `#675a4e` | Subtítulos, metadatos, labels secundarios de formularios. |
| `tertiary-container` | `#807265` | Elementos de soporte con peso medio. |
| `surface` | `#fbf9f2` | Color del `body` y lienzo de fondo general de la aplicación. |
| `surface-container-lowest` | `#ffffff` | Fondo de tarjetas (cards), modales, menús flotantes e inputs. |
| `surface-container-low` | `#f5f4ec` | Fondos de secciones anidadas y columnas de tableros Kanban. |
| `surface-container` | `#efeee7` | Contenedores secundarios, cajas de iconos, fondos de inputs inactivos. |
| `surface-container-high` | `#eae8e1` | Separadores de bloque y superficies de contraste moderado. |
| `surface-container-highest` | `#e4e3db` | Bordes perimetrales de tarjetas y líneas divisorias principales. |
| `on-surface` | `#1b1c18` | Tipografía principal para titulares, celdas de tabla y texto de lectura. |
| `on-surface-variant` | `#404944` | Texto secundario y descripciones de tarjetas. |
| `outline` | `#707973` | Iconos inactivos, placeholders y bordes de campos de texto. |
| `outline-variant` | `#c0c9c2` | Bordes ligeros para cuadrículas y separadores sutiles. |
| `error` | `#ba1a1a` | Estado de equipo averiado, ticket de falla crítica, validaciones inválidas. |
| `error-container` | `#ffdad6` | Fondo para alertas de error y badges de estaciones fuera de servicio. |
| `on-error-container` | `#93000a` | Texto de alta visibilidad sobre contenedores de error. |

---

## 4. Tipografía e Iconografía

### 4.1. Familia Tipográfica
- **Fuente Principal:** `Work Sans` (Google Fonts).
- **Propiedades:** Proporción humanista grotesca con excelente legibilidad en pantalla para tablas de datos numéricos, direcciones IPv4, códigos de matrícula y números de serie.

### 4.2. Escala Tipográfica Institucional

| Estilo de Texto | Font Family | Tamaño | Interlineado (*line-height*) | Grosor (*font-weight*) | Espaciado (*letter-spacing*) | Uso |
| :--- | :--- | :--- | :--- | :--- | :--- | :--- |
| `headline-xl` | Work Sans | `2.25rem` (36px) | `2.75rem` (44px) | 800 (Extra Bold) | `-0.02em` | Títulos principales de página y números de KPIs gigantes. |
| `headline-lg` | Work Sans | `1.75rem` (28px) | `2.25rem` (36px) | 700 (Bold) | `-0.015em` | Títulos de sección y encabezados de vistas. |
| `headline-md` | Work Sans | `1.375rem` (22px) | `1.75rem` (28px) | 700 (Bold) | `-0.01em` | Encabezados de tarjetas y modales técnicos. |
| `headline-sm` | Work Sans | `1.125rem` (18px) | `1.5rem` (24px) | 700 (Bold) | `0em` | Subtítulos de módulos y etiquetas de equipos (ej. *PC-01*). |
| `body-lg` | Work Sans | `1.0rem` (16px) | `1.5rem` (24px) | 400 (Regular) | `normal` | Párrafos de introducción y lectura general. |
| `body-md` | Work Sans | `0.875rem` (14px) | `1.25rem` (20px) | 400 (Regular) | `normal` | Texto base de tablas, formularios y descripciones. |
| `body-sm` | Work Sans | `0.75rem` (12px) | `1.0rem` (16px) | 400 (Regular) | `normal` | Metadatos, marcas de tiempo y notas secundarias. |
| `label-lg` | Work Sans | `0.875rem` (14px) | `1.25rem` (20px) | 500 (Medium) | `0.01em` | Botones de navegación y pestañas activas. |
| `label-md` | Work Sans | `0.75rem` (12px) | `1.0rem` (16px) | 500 (Medium) | `0.02em` | Etiquetas de campos de entrada (*labels* de formulario). |
| `label-sm` | Work Sans | `0.6875rem` (11px)| `0.875rem` (14px)| 700 (Bold) | `0.05em` (Mayúsculas) | Micro-etiquetas, categorías y tags de estado. |
| `numeric-table` | Work Sans | `0.875rem` (14px) | `1.25rem` (20px) | 500 (Medium) | `normal` (Lining) | Contadores numéricos, puertos e IPs. |

### 4.3. Iconografía
- **Librería Exclusiva:** **Google Material Symbols Outlined** (peso 400, grado 0, tamaño óptico 20..48px).
- **Prohibición:** Se prohíbe el uso de emojis pictóricos estándar en la interfaz operativa. Los estados técnicos deben comunicarse mediante glifos semánticos:
  - `terminal`, `dns`, `router`, `memory`, `developer_board` (Hardware y redes).
  - `check_circle`, `error`, `warning`, `help` (Estados operativos).
  - `badge`, `fingerprint`, `qr_code_scanner` (Identidad y credenciales).
  - `lock`, `lock_open`, `restart_alt`, `power_settings_new` (Control de estaciones).

---

## 5. Cuadrícula, Estructura de Navegación y Espaciado

### 5.1. Sistema de Doble Barra Superior (*Master Navigation Deck*)
La aplicación prescinde de barras laterales verticales (*sidebars*), adoptando una navegación horizontal fija en dos niveles:

```
┌────────────────────────────────────────────────────────────────────────┐
│ NIVEL 1: Institutional Master Bar (Altura: 64px, fondo: #ffffff)        │
│ [Logo UPTex TI]  [Buscador Global Ctrl+K]  [Modo Acceso] [Campana] [Usuario]│
├────────────────────────────────────────────────────────────────────────┤
│ NIVEL 2: Section Command Tabs (Altura: 46px, fondo: #ffffff / #faf8f7)   │
│ Inicio y Servicios | Salas | Horarios | Software | Préstamos | Operación...│
└────────────────────────────────────────────────────────────────────────┘
  ▼ Offset superior obligatorio para el <main>: pt-[110px]
```

### 5.2. Escala de Espaciado Modular
- `gutter`: `1.5rem` (24px)
- `margin`: `2.0rem` (32px)
- `space-xs`: `0.25rem` (4px)
- `space-sm`: `0.5rem` (8px)
- `space-md`: `1.0rem` (16px)
- `space-lg`: `1.5rem` (24px)
- `space-xl`: `2.0rem` (32px)

### 5.3. Radios de Curvatura (*Border Radius*)
- `rounded-sm`: `0.25rem` (4px) — Checkboxes, indicadores de color, badges miniatura.
- `rounded`: `0.5rem` (8px) — Botones estándar, inputs, dropdowns, selects.
- `rounded-md`: `0.75rem` (12px) — Contenedores secundarios y pestañas.
- `rounded-xl`: `1.0rem` (16px) — Tarjetas principales (*cards*), estaciones y ventanas modales.
- `rounded-full`: `9999px` — Avatares de usuario y puntos de pulso de estado.

---

## 6. Catálogo de Componentes de Interfaz

### 6.1. Botones (*Buttons*)
- **Primario:** Fondo `#2f664f`, texto `#ffffff` (Work Sans 500/700), esquina redondeada `8px`, sin borde.
  - Hover: `#497f67` | Active: `scale-[0.99]`
- **Secundario / Contorno:** Fondo `#ffffff`, borde `1px solid #eae6e5`, texto `#1b1c18`.
  - Hover: `#efeee7`
- **Destructivo:** Fondo `#ba1a1a`, texto `#ffffff`, esquina `8px`.
  - Hover: `#93000a`
- **Icon Actions:** Dimensiones `40x40px`, fondo `#ffffff`, borde `1px solid #c0c9c2`, esquina `8px`.

### 6.2. Tarjetas y Módulos de Información (*Cards*)
- Fondo: `#ffffff` (`bg-surface-container-lowest`).
- Esquinas: `rounded-xl` (`16px`).
- Borde perimetral: `border border-surface-container-highest` o `style="border: 1px solid #eae6e5;"`.
- Relleno interno: `p-space-lg` (24px) para módulos estándar y `p-space-md` (16px) para monitores compactos.
- Separadores internos: `border-t border-surface-container-highest`.

### 6.3. Insignias de Estado (*Status Chips / Badges*)
- **Operativo / Disponible / Libre:**
  - Fondo: `#b3f1be` con opacidad o `bg-secondary-fixed`.
  - Texto: `#16512c` (`font-bold text-[10px]`).
  - Glifo: Punto sólido de 6px en `#316a42` o icono `check_circle`.
- **Ocupado / En Uso:**
  - Fondo: `#eae8e1` (`bg-surface-container-highest`).
  - Texto: `#1b1c18` (`text-on-surface`).
  - Glifo: Candado lineal o icono `lock`.
- **En Mantenimiento / Reportado:**
  - Fondo: `#ffdad6` (`bg-error-container`).
  - Texto: `#ba1a1a` (`text-error`).
  - Glifo: Icono de llave técnica `build` o `error`.

### 6.4. Formularios y Campos de Entrada (*Inputs & Selects*)
- Altura estricta: `40px` (`h-10`).
- Fondo: `#ffffff` con transición a `#f5f4ec` en interacción.
- Borde: `1px solid #c0c9c2` o `#707973` (`border-outline-variant`).
- Enfoque (*Focus*): Borde sólido de `2px solid #2f664f` **sin halos de sombra exterior** (*ring-0*).
- Icono guía: Prefijado a la izquierda mediante `absolute left-3 text-outline` con padding `pl-10`.

### 6.5. Malla Topológica de Laboratorio (*Topology Matrix*)
- Tarjeta de estación individual con dimensiones fijas `h-[156px]`.
- Indicador superior con identificador (*PC-01* a *PC-30*) y badge de estado.
- Código de matrícula de estudiante activo o leyenda "Disponible".
- Dirección IP fija en fuente monoespaciada (`font-mono text-[11px]`).
- Barra inferior con tiempo de uso acumulado e indicadores de periféricos conectados (`settings_ethernet`, `usb`).

---

## 7. Guía de Integración en Laravel + Tailwind CSS

El proyecto actual utiliza **Laravel 11+ con Vite y Tailwind CSS v4**. A continuación se detallan los pasos para incorporar los estilos del diseño:

### 7.1. Inclusión de Fuentes y Glifos en la Plantilla Maestra Blade
Edita `resources/views/layouts/app.blade.php` (o la cabecera correspondiente) para incluir las fuentes oficiales de Google:

```html
<!-- Google Fonts: Work Sans y Material Symbols Outlined -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Work+Sans:wght@400;500;600;700;800&family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&display=swap" rel="stylesheet">
```

### 7.2. Configuración de Tokens en Tailwind CSS v4 (`resources/css/app.css`)
En Tailwind CSS v4, los tokens se declaran bajo la directiva `@theme`. Agrega la siguiente configuración a [`resources/css/app.css`](../resources/css/app.css):

```css
@import 'tailwindcss';

@theme {
    /* Familia tipográfica principal */
    --font-sans: 'Work Sans', system-ui, -apple-system, sans-serif;
    --font-headline: 'Work Sans', sans-serif;

    /* Tokens de Color UPTex TI */
    --color-primary: #2f664f;
    --color-primary-container: #497f67;
    --color-on-primary: #ffffff;
    --color-on-primary-container: #f5fff7;
    --color-primary-fixed: #b5efd2;
    --color-primary-fixed-dim: #9ad3b7;

    --color-secondary: #316a42;
    --color-secondary-container: #b3f1be;
    --color-on-secondary: #ffffff;
    --color-on-secondary-container: #377047;
    --color-secondary-fixed: #b3f1be;
    --color-secondary-fixed-dim: #98d4a4;

    --color-tertiary: #675a4e;
    --color-tertiary-container: #807265;
    --color-on-tertiary: #ffffff;
    --color-tertiary-fixed: #f2dfd0;
    --color-tertiary-fixed-dim: #d5c3b5;

    --color-surface: #fbf9f2;
    --color-surface-dim: #dbdad3;
    --color-surface-bright: #fbf9f2;
    --color-surface-container-lowest: #ffffff;
    --color-surface-container-low: #f5f4ec;
    --color-surface-container: #efeee7;
    --color-surface-container-high: #eae8e1;
    --color-surface-container-highest: #e4e3db;

    --color-on-surface: #1b1c18;
    --color-on-surface-variant: #404944;
    --color-outline: #707973;
    --color-outline-variant: #c0c9c2;

    --color-error: #ba1a1a;
    --color-error-container: #ffdad6;
    --color-on-error-container: #93000a;

    /* Escala de Espaciado */
    --spacing-gutter: 1.5rem;
    --spacing-space-xs: 0.25rem;
    --spacing-space-sm: 0.5rem;
    --spacing-space-md: 1.0rem;
    --spacing-space-lg: 1.5rem;
    --spacing-space-xl: 2.0rem;
}

/* Reglas utilitarias base para Material Symbols */
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
}
```

---

## 8. Resumen de Archivos Extraídos

Los archivos del prototipo se encuentran accesibles en:
- Directorio de prototipos: `docs/stitch_uptex_lab_manager_portal/`
- Especificación markdown original: `docs/stitch_uptex_lab_manager_portal/uptex_ti_academic_operations/DESIGN.md`
- Paquete zip fuente: `docs/stitch_uptex_lab_manager_portal.zip`
