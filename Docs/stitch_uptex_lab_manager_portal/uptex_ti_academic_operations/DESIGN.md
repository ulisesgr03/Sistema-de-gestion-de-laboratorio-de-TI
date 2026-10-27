---
name: UPTex TI Academic Operations
colors:
  surface: '#fbf9f2'
  surface-dim: '#dbdad3'
  surface-bright: '#fbf9f2'
  surface-container-lowest: '#ffffff'
  surface-container-low: '#f5f4ec'
  surface-container: '#efeee7'
  surface-container-high: '#eae8e1'
  surface-container-highest: '#e4e3db'
  on-surface: '#1b1c18'
  on-surface-variant: '#404944'
  inverse-surface: '#30312c'
  inverse-on-surface: '#f2f1e9'
  outline: '#707973'
  outline-variant: '#c0c9c2'
  surface-tint: '#326852'
  primary: '#2f664f'
  on-primary: '#ffffff'
  primary-container: '#497f67'
  on-primary-container: '#f5fff7'
  inverse-primary: '#9ad3b7'
  secondary: '#316a42'
  on-secondary: '#ffffff'
  secondary-container: '#b3f1be'
  on-secondary-container: '#377047'
  tertiary: '#675a4e'
  on-tertiary: '#ffffff'
  tertiary-container: '#807265'
  on-tertiary-container: '#fffbff'
  error: '#ba1a1a'
  on-error: '#ffffff'
  error-container: '#ffdad6'
  on-error-container: '#93000a'
  primary-fixed: '#b5efd2'
  primary-fixed-dim: '#9ad3b7'
  on-primary-fixed: '#002115'
  on-primary-fixed-variant: '#16503b'
  secondary-fixed: '#b3f1be'
  secondary-fixed-dim: '#98d4a4'
  on-secondary-fixed: '#00210c'
  on-secondary-fixed-variant: '#16512c'
  tertiary-fixed: '#f2dfd0'
  tertiary-fixed-dim: '#d5c3b5'
  on-tertiary-fixed: '#231a11'
  on-tertiary-fixed-variant: '#514539'
  background: '#fbf9f2'
  on-background: '#1b1c18'
  surface-variant: '#e4e3db'
typography:
  headline-xl:
    fontFamily: Work Sans
    fontSize: 2.25rem
    fontWeight: '800'
    lineHeight: 2.75rem
    letterSpacing: -0.02em
  headline-lg:
    fontFamily: Work Sans
    fontSize: 1.75rem
    fontWeight: '700'
    lineHeight: 2.25rem
    letterSpacing: -0.015em
  headline-md:
    fontFamily: Work Sans
    fontSize: 1.375rem
    fontWeight: '700'
    lineHeight: 1.75rem
    letterSpacing: -0.01em
  headline-sm:
    fontFamily: Work Sans
    fontSize: 1.125rem
    fontWeight: '700'
    lineHeight: 1.5rem
    letterSpacing: 0em
  body-lg:
    fontFamily: Work Sans
    fontSize: 1rem
    fontWeight: '400'
    lineHeight: 1.5rem
  body-md:
    fontFamily: Work Sans
    fontSize: 0.875rem
    fontWeight: '400'
    lineHeight: 1.25rem
  body-sm:
    fontFamily: Work Sans
    fontSize: 0.75rem
    fontWeight: '400'
    lineHeight: 1rem
  label-lg:
    fontFamily: Work Sans
    fontSize: 0.875rem
    fontWeight: '500'
    lineHeight: 1.25rem
    letterSpacing: 0.01em
  label-md:
    fontFamily: Work Sans
    fontSize: 0.75rem
    fontWeight: '500'
    lineHeight: 1rem
    letterSpacing: 0.02em
  label-sm:
    fontFamily: Work Sans
    fontSize: 0.6875rem
    fontWeight: '700'
    lineHeight: 0.875rem
    letterSpacing: 0.05em
  numeric-table:
    fontFamily: Work Sans
    fontSize: 0.875rem
    fontWeight: '500'
    lineHeight: 1.25rem
rounded:
  sm: 0.25rem
  DEFAULT: 0.5rem
  md: 0.75rem
  lg: 1rem
  xl: 1.5rem
  full: 9999px
spacing:
  gutter: 1.5rem
  margin: 2rem
  space-xs: 0.25rem
  space-sm: 0.5rem
  space-md: 1rem
  space-lg: 1.5rem
  space-xl: 2rem
---

## Brand & Style

This design system delivers an institutional, high-density computing environment tailored for university laboratory administration, academic research, and hardware inventory operations. The aesthetic repudiates generic generative-AI tropes—specifically discarding blurred backdrops, multi-color gradient fills, neon accents, and expressive emoji sets—in favor of a disciplined, utilitarian editorial minimalism.

Rooted in functional modernist information design, the system prioritizes high-contrast legibility, precise tabular layouts, and structured administrative ergonomics. The emotional tone is authoritative, sober, resilient, and academically rigorous, instilling confidence across faculty supervisors, laboratory technicians, and student researchers navigating mission-critical hardware, inventory schedules, and facility bookings.

## Colors

The palette operates under a strict solid flat paradigm. All gradients, color stops, atmospheric tints, and glow effects are explicitly forbidden.

- **Primary Accent (`--jungle-teal`: #5B9279):** Primary interactive triggers, active navigation markers, confirmed operational states, and primary button fills.
- **Secondary Accent (`--celadon`: #8FCB9B):** Affirmative indicators, hardware availability tags, operative status pills, and data-density meter fills.
- **Neutral Core (`--onyx`: #12130F):** Primary body typography, high-impact numerical readouts, heavy table headers, and deep contrast elements.
- **Support Taupe (`--taupe`: #8F8073):** Secondary metadata, subheadings, non-active icon strokes, form input boundary states, and field labels.
- **Structural Neutral (`--alabaster-grey`: #EAE6E5):** Component borders, data-grid lines, neutral chip surfaces, and secondary control backplates.
- **Surface Canvas (`#FAF8F7`):** Base workspace background.
- **Surface Container (`#FFFFFF`):** High-level card layers, input surfaces, popovers, and contextual modals.
- **System States:** Error is represented by flat solid `#BA1A1A` with `#FFEDEA` background; Warning is represented by `#825500` with `#FFF1D6` background. No translucent overlays are used for state signaling.

## Typography

Typography relies on a clean, structural humanist grotesk configuration executed in solid monochrome tokens. Every level targets tabular accuracy, legibility under dense laboratory catalog listings, and explicit structural contrast.

- All iconographic references are strictly restricted to Google Material Symbols Outlined (weight 400).
- Absolute prohibition of pictorial emojis in production UI; operational status is conveyed via technical iconography or typographic tags.
- Data tables require proportional lining numbers for financial, serial, and IP address alignment.
- Labels utilize medium-to-bold weights in `--taupe` or `--onyx` to anchor form elements without visual noise.

## Layout & Spacing

The layout is built for high-resolution desktop terminals (1440px viewport and above), enforcing a fixed 100% horizontal top-level navigation structure with absolutely zero vertical sidebars.

- **Master Navigation Deck:**
  - **Level 1 (Institutional Master Bar):** Exact height of 64px, static background `#FFFFFF`, bottom border 1px solid `#EAE6E5`. Contains the university laboratory identity, institutional scope switcher, system health index, and user operator identity.
  - **Level 2 (Section Command Tabs):** Exact height of 46px, static background `#FAF8F7`, bottom border 1px solid `#EAE6E5`. Houses view modes, laboratory sub-domains, and global search filters.
- **Canvas Grid:** The content plane operates within a 12-column fixed grid with a max width of 1440px (centered on larger ultra-wide displays), 24px (1.5rem) gutters, and 32px (2rem) outer margin padding.
- **Density Hierarchy:** Micro-spacing scales in 4px (0.25rem) increments. Inner card paddings conform uniformly to 24px (1.5rem) on standard modules and 16px (1rem) on compact laboratory monitoring racks.

## Elevation & Depth

This system operates entirely without blur-based drop shadows, ambient box-shadows, or glassmorphic backdrops. Hierarchy is established exclusively through surface tonal containment and crisp 1px structural borders.

- **Flat Layering Structure:**
  - **Level 0 (Canvas):** `#FAF8F7` solid base canvas.
  - **Level 1 (Primary Surface / Cards):** `#FFFFFF` solid fill bounded by a mandatory `1px solid #EAE6E5` edge.
  - **Level 2 (Secondary Surface / Wells):** `#EAE6E5` solid neutral surface for nested metrics, toolbars, and inactive parameter groupings.
  - **Level 3 (Overlays / Menus):** `#FFFFFF` solid fill bordered by `1px solid #12130F`. No drop shadow; sharp line perimeter ensures complete detachment from the underlying canvas.
- **Divider Treatment:** All horizontal and vertical separators use explicit `1px solid #EAE6E5` lines. Opacity shifts or gradient-faded lines are disallowed.

## Shapes

The geometric framework balances technical rigor with structural comfort:

- **Cards and Functional Enclosures:** Fixed corner radius of 16px (`1rem`), matching the mandatory 1px border.
- **Form Controls & Inputs:** 8px radius for text inputs, dropdown selects, and data entry tables.
- **Buttons & Clickable Triggers:** 8px corner radius for standard action elements; 4px radius for compact table action icons.
- **Status Badges & Status Chips:** 6px radius for compact information chips.
- **Interactive Checkboxes:** 4px radius with 1.5px solid border in `--taupe` or `--jungle-teal`.

## Components

### Buttons & Interactive Controls
- **Primary Action:** Solid background `--jungle-teal` (#5B9279), label text `#FFFFFF` (Work Sans 500), border none, corner radius 8px. Hover state shifts to `#4B7964`. Active state: `#3F6453`.
- **Secondary Action:** Solid background `#FFFFFF`, 1px solid border `#EAE6E5`, label text `--onyx` (#12130F). Hover state: background `#EAE6E5`.
- **Destructive Action:** Solid background `#BA1A1A`, text `#FFFFFF`. Hover state: `#931313`.

### Navigation Bars
- **Level 1 Top Bar (64px):** Fixed to screen top. Houses institutional mark, active laboratory context (e.g., "Laboratorio de Cómputo Especializado L-04"), operational clock, and profile drawer. Border bottom: `1px solid #EAE6E5`.
- **Level 2 Tab Deck (46px):** Positioned directly beneath Level 1. Features flat horizontal text tabs with icons. Active tab state: solid 2px bottom border in `--jungle-teal` with text colored `--onyx` (700 weight). Inactive tabs: text colored `--taupe` (500 weight).

### Cards & Data Tiles
- Constructed exclusively with background `#FFFFFF`, corner radius 16px, and perimeter border `1px solid #EAE6E5`.
- Inner content sections are divided using horizontal rules of `1px solid #EAE6E5`.
- Card headers feature uppercase metadata labels (`label-sm`) in `--taupe` followed by bold headlines in `--onyx`.

### Status Badges & Chips
- **Operative / Available:** Background `#8FCB9B` at 20% opacity over solid `#FFFFFF`, text `#2A5A36` (Work Sans 700), with a 6px solid dot in `#8FCB9B`.
- **Occupied / In Session:** Background `#EAE6E5`, text `--onyx`, paired with a linear padlock icon.
- **Maintenance / Out of Order:** Background `#FFEDEA`, text `#BA1A1A`, with a linear hardware wrench icon.

### Form Inputs & Filters
- **Text Inputs & Selects:** Height 40px, background `#FFFFFF`, border `1px solid #8F8073`, radius 8px, text `--onyx`. Focus state: border `2px solid #5B9279` without outer focus glow rings.
- **Labels:** Positioned above inputs, typography `label-md` in `--taupe`.

### Data Grid & Inventory Lists
- Header cells: height 44px, background `#FAF8F7`, bottom border `1px solid #8F8073`, text `label-md` in `--onyx` uppercase.
- Body rows: height 52px, alternating or white rows with `1px solid #EAE6E5` bottom borders. Hover state shifts row background to `#FAF8F7`.
- Zero emojis; indicators rely on Material Symbols Outlined (`check_circle`, `error`, `terminal`, `dns`, `memory`).