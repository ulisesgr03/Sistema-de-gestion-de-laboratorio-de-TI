<?php

namespace App\Http\Controllers;

use App\Models\AsignacionEquipo;
use App\Models\Equipo;
use App\Models\Estacion;
use App\Models\Horario;
use App\Models\Incidencia;
use App\Models\Laboratorio;
use App\Models\Proceso;
use App\Models\ReservaLaboratorio;
use App\Models\Rol;
use App\Models\User;
use App\Support\CatalogoLaboratorios;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PanelOperativoController extends Controller
{
    /**
     * Redirige automáticamente al panel específico según el rol del usuario autenticado.
     */
    public function index(): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();

        return redirect()->to($user->dashboardRoute());
    }

    /**
     * 1. Panel Privado del Administrador (role:admin)
     */
    public function admin(): View
    {
        $usuario = Auth::user();
        $laboratorios = CatalogoLaboratorios::laboratorios();
        $labs = $laboratorios;
        $totales = CatalogoLaboratorios::totales();
        $solicitudes = Proceso::with('usuario')->orderByDesc('id')->get();
        $incidenciasAbiertas = Incidencia::with(['laboratorio', 'estacion'])
            ->where('estado', '!=', 'resuelta')
            ->orderByDesc('id')
            ->get();
        $usuarios = User::with('roles')->orderBy('id')->get();
        $roles = Rol::orderBy('id')->get();

        return view('private.admin', compact(
            'usuario',
            'laboratorios',
            'labs',
            'totales',
            'solicitudes',
            'incidenciasAbiertas',
            'usuarios',
            'roles'
        ));
    }

    /**
     * 2. Panel Privado del Encargado de Laboratorio (FUSIONADO EN ADMIN).
     * Redirige transparentemente al panel unificado de Administrador de Laboratorios.
     */
    public function encargado(): RedirectResponse
    {
        return redirect()->route('panel.admin');
    }

    /**
     * 3. Panel Privado del Docente / Maestro (role:docente)
     */
    public function docente(): View
    {
        /** @var User $usuario */
        $usuario = Auth::user();
        $laboratorios = CatalogoLaboratorios::laboratorios();
        $labs = $laboratorios;
        $totales = CatalogoLaboratorios::totales();

        $misClases = Horario::with('laboratorio')->orderBy('id')->limit(8)->get();
        $misReservasSala = ReservaLaboratorio::with('laboratorio')->orderByDesc('id')->limit(10)->get();
        $misSolicitudes = Proceso::where('user_id', $usuario->id)->orderByDesc('id')->get();

        return view('private.docente', compact(
            'usuario',
            'laboratorios',
            'labs',
            'totales',
            'misClases',
            'misReservasSala',
            'misSolicitudes'
        ));
    }

    /**
     * 4. Panel Privado del Alumno (role:alumno)
     */
    public function alumno(): View
    {
        /** @var User $usuario */
        $usuario = Auth::user();
        $laboratorios = CatalogoLaboratorios::laboratorios();
        $labs = $laboratorios;
        $totales = CatalogoLaboratorios::totales();

        $misReservas = AsignacionEquipo::with('laboratorio')
            ->where('user_id', $usuario->id)
            ->orderByDesc('id')
            ->get();

        $misSolicitudes = Proceso::orderByDesc('id')->limit(12)->get();
        $misIncidencias = Incidencia::with(['laboratorio', 'estacion'])
            ->orderByDesc('id')
            ->limit(10)
            ->get();

        return view('private.alumno', compact(
            'usuario',
            'laboratorios',
            'labs',
            'totales',
            'misReservas',
            'misSolicitudes',
            'misIncidencias'
        ));
    }

    /**
     * Cambia el estado de cualquier PC (Libre / Ocupada / No Disponible) o repara avería en vivo (Admin).
     */
    public function cambiarEstadoPc(Request $request): JsonResponse|RedirectResponse
    {
        $estIdRaw = strtoupper(trim((string) $request->input('estacion_id', '')));
        $labId = (int) $request->input('laboratorio_id', 1);
        $codigoPc = strtoupper(trim((string) $request->input('codigo_pc', 'PC-01')));

        if (preg_match('/^LAB(\d+)-PC(\d+)$/i', $estIdRaw, $m)) {
            $labId = (int) $m[1];
            $codigoPc = sprintf('PC-%02d', (int) $m[2]);
        }

        $estado = $request->input('estado', 'disponible');
        if (!in_array($estado, ['disponible', 'ocupado', 'mantenimiento'], true)) {
            $estado = 'disponible';
        }
        $nota = trim((string) $request->input('nota', ''));

        $estacion = Estacion::updateOrCreate(
            [
                'laboratorio_id' => $labId,
                'nombre' => $codigoPc,
            ],
            [
                'estado' => $estado,
                'ubicacion' => $nota ?: match ($estado) {
                    'disponible' => 'Equipos verificados · Estación Libre',
                    'ocupado' => 'Sesión activa asignada por consola',
                    'mantenimiento' => 'En mantenimiento / reparación técnica',
                },
            ]
        );

        $equipo = Equipo::where('estacion_id', $estacion->id)->first();
        if ($equipo) {
            $equipo->update(['estado' => $estado]);
        }

        if ($estado === 'disponible') {
            $incQuery = Incidencia::where('laboratorio_id', $labId)->where('estado', '!=', 'resuelta');
            if ($equipo) {
                $incQuery->where(function ($q) use ($equipo) {
                    $q->where('equipo_id', $equipo->id)->orWhereNull('equipo_id');
                });
            }
            $incQuery->update([
                'estado' => 'resuelta',
                'fecha_resolucion' => now(),
                'asignado_a' => Auth::id(),
            ]);
        }

        CatalogoLaboratorios::clearCache();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Estado de L-" . sprintf('%02d', $labId) . " · {$codigoPc} actualizado a " . strtoupper($estado) . ".",
                'totales' => CatalogoLaboratorios::totales(),
            ]);
        }

        return back()->with('status', "Estación L-" . sprintf('%02d', $labId) . " · {$codigoPc} actualizada a estado: " . ucfirst($estado) . ".");
    }

    /**
     * Aprueba o rechaza una solicitud pendiente en la bandeja del Admin.
     */
    public function resolverSolicitud(Request $request): JsonResponse|RedirectResponse
    {
        $procesoId = $request->input('proceso_id') ?? $request->input('id');
        $accion = strtolower((string) ($request->input('accion') ?? $request->input('decision') ?? 'aprobar'));

        $estadoFinal = in_array($accion, ['aprobar', 'aprobada', 'autorizada', 'aprobado'], true) ? 'aprobado' : 'rechazado';

        if (is_numeric($procesoId)) {
            Proceso::where('id', (int) $procesoId)->update(['estado' => $estadoFinal]);
        } elseif (is_string($procesoId) && str_starts_with($procesoId, 'PROC-')) {
            $procId = (int) str_replace('PROC-', '', $procesoId);
            Proceso::where('id', $procId)->update(['estado' => $estadoFinal]);
        }

        CatalogoLaboratorios::clearCache();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Solicitud marcada como {$estadoFinal} correctamente.",
            ]);
        }

        return back()->with('status', "Solicitud marcada como {$estadoFinal} correctamente.");
    }

    /**
     * Reserva una estación individual (PC-01 a PC-20) para práctica libre (Alumno / Docente).
     */
    public function reservarPc(Request $request): JsonResponse|RedirectResponse
    {
        $estIdRaw = strtoupper(trim((string) $request->input('estacion_id', 'LAB1-PC01')));
        $labId = (int) $request->input('laboratorio_id', 1);
        $codigoPc = strtoupper(trim((string) $request->input('codigo_pc', 'PC-01')));

        if (preg_match('/^LAB(\d+)-PC(\d+)$/i', $estIdRaw, $m)) {
            $labId = (int) $m[1];
            $codigoPc = sprintf('PC-%02d', (int) $m[2]);
        }

        /** @var User $user */
        $user = Auth::user();
        $duracion = max(1, min(4, (int) $request->input('duracion', 2)));
        $actividad = trim((string) ($request->input('actividad') ?? $request->input('objetivo') ?? 'Práctica libre individual'));

        // Regla de Negocio: Alumnos no pueden reservar durante Clase o Examen activo
        $reservaActiva = ReservaLaboratorio::where('laboratorio_id', $labId)
            ->whereIn('estado', ['confirmada', 'aprobada'])
            ->where(function ($q) {
                $q->whereBetween('inicio', [now()->startOfDay(), now()->endOfDay()])
                  ->orWhere(function ($q2) {
                      $q2->where('inicio', '<=', now())
                         ->where('fin', '>=', now());
                  });
            })
            ->first();

        if ($reservaActiva && in_array(strtolower($reservaActiva->modalidad), ['clase', 'examen'], true) && $user->hasRole('alumno')) {
            $msg = "No es posible reservar estaciones en este momento: el laboratorio L-" . sprintf('%02d', $labId) . " se encuentra reservado para {$reservaActiva->modalidad}: {$reservaActiva->materia}.";
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $msg], 422);
            }
            return back()->withErrors(['reserva' => $msg]);
        }

        $estacion = Estacion::updateOrCreate(
            [
                'laboratorio_id' => $labId,
                'nombre' => $codigoPc,
            ],
            [
                'estado' => 'ocupado',
                'ubicacion' => "Reservada por {$user->name} ({$duracion}h) · {$actividad}",
            ]
        );

        $equipo = Equipo::where('estacion_id', $estacion->id)->first();
        if ($equipo) {
            $equipo->update(['estado' => 'ocupado']);
        }

        Proceso::create([
            'user_id' => $user->id,
            'laboratorio_id' => $labId,
            'tipo' => 'reserva_pc',
            'descripcion' => "Apartado de L-" . sprintf('%02d', $labId) . " · {$codigoPc} ({$duracion}h) para {$actividad}",
            'estado' => 'aprobado',
        ]);

        CatalogoLaboratorios::clearCache();
        $labCode = sprintf('L-%02d', $labId);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Estación {$labCode} · {$codigoPc} apartada exitosamente por {$duracion} horas.",
            ]);
        }

        return back()->with('status', "Estación {$labCode} · {$codigoPc} apartada exitosamente por {$duracion} horas.");
    }

    /**
     * Reserva un laboratorio completo por bloque horario marcando si es Clase, Práctica o Examen (Docente / Admin).
     */
    public function reservarSala(Request $request): JsonResponse|RedirectResponse
    {
        $labId = (int) $request->input('laboratorio_id', 1);
        $modalidad = (string) $request->input('modalidad', 'Clase');
        $materia = trim((string) ($request->input('materia') ?? $request->input('asignatura') ?? 'Práctica de Ingeniería'));
        $grupo = trim((string) $request->input('grupo', 'ITI-8A'));
        $dia = trim((string) $request->input('dia', 'Hoy'));
        $bloque = trim((string) ($request->input('bloque') ?? $request->input('bloque_horario') ?? '11:00 - 13:00'));

        /** @var User $user */
        $user = Auth::user();

        $inicio = now()->setTime(11, 0);
        $fin = now()->setTime(13, 0);
        if (preg_match('/(\d{1,2}):(\d{2})\s*[-–]\s*(\d{1,2}):(\d{2})/', $bloque, $m)) {
            $inicio = now()->setTime((int) $m[1], (int) $m[2]);
            $fin = now()->setTime((int) $m[3], (int) $m[4]);
        }

        $motivo = "[{$modalidad}] {$materia} · Grupo {$grupo} ({$dia} {$bloque}) · Docente: {$user->name}";

        ReservaLaboratorio::create([
            'laboratorio_id' => $labId,
            'user_id' => $user->id,
            'inicio' => $inicio,
            'fin' => $fin,
            'estado' => 'confirmada',
            'motivo' => $motivo,
        ]);

        // Si la reserva es de Clase o Examen, marcar automáticamente las 20 estaciones como ocupado
        // respetando las que estén en mantenimiento/avería
        if (in_array(strtolower($modalidad), ['clase', 'examen'], true)) {
            for ($i = 1; $i <= 20; $i++) {
                $codigoPc = sprintf('PC-%02d', $i);
                $est = Estacion::where('laboratorio_id', $labId)->where('nombre', $codigoPc)->first();
                if ($est && $est->estado === 'mantenimiento') {
                    continue; // Preserva equipos averiados
                }
                $estActualizada = Estacion::updateOrCreate(
                    [
                        'laboratorio_id' => $labId,
                        'nombre' => $codigoPc,
                    ],
                    [
                        'estado' => 'ocupado',
                        'ubicacion' => "{$modalidad}: {$materia} · {$user->name} ({$grupo})",
                    ]
                );

                Equipo::where('estacion_id', $estActualizada->id)->update(['estado' => 'ocupado']);
            }
        }

        CatalogoLaboratorios::clearCache();
        $labCode = sprintf('L-%02d', $labId);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Sala {$labCode} reservada para [{$modalidad}] {$materia} · Grupo {$grupo}. Las 20 estaciones han sido ocupadas.",
            ]);
        }

        return back()->with(
            'status',
            "Sala {$labCode} reservada para [{$modalidad}] {$materia} · Grupo {$grupo} ({$dia} {$bloque}). Las estaciones disponibles quedaron asignadas."
        );
    }

    /**
     * Registra una solicitud de Software (instalación / actualización), Red o Préstamo de Hardware/Periféricos.
     */
    public function solicitarRecurso(Request $request): JsonResponse|RedirectResponse
    {
        $tipoRaw = strtolower(trim((string) ($request->input('tipo') ?? $request->input('categoria_solicitud') ?? 'software')));
        $salaRaw = trim((string) ($request->input('sala') ?? $request->input('laboratorio_id') ?? 'L-01'));
        $recurso = trim((string) ($request->input('recurso') ?? $request->input('recurso_nombre') ?? 'Paquete Técnico'));
        $descripcion = trim((string) ($request->input('descripcion') ?? $request->input('justificacion') ?? 'Solicitud académica'));

        /** @var User $user */
        $user = Auth::user();

        $labInfo = CatalogoLaboratorios::buscar($salaRaw);
        $labId = $labInfo ? $labInfo['id'] : 1;

        $proceso = Proceso::create([
            'user_id' => $user->id,
            'laboratorio_id' => $labId,
            'tipo' => str_contains($tipoRaw, 'hard') ? 'prestamo_hardware' : (str_contains($tipoRaw, 'mesa') ? 'asignacion_mesa' : 'instalacion_software'),
            'descripcion' => "[{$salaRaw}] {$recurso}: {$descripcion} (Solicitado por {$user->name} · {$user->primaryRoleLabel()})",
            'estado' => 'pendiente',
        ]);

        CatalogoLaboratorios::clearCache();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Solicitud enviada a la consola del Administrador para {$salaRaw}: {$recurso}.",
            ]);
        }

        return back()->with(
            'status',
            "Solicitud enviada a la consola del Administrador para {$salaRaw}: {$recurso}."
        );
    }

    /**
     * Permite al Administrador cambiar el rol de una cuenta de usuario.
     */
    public function actualizarUsuario(Request $request): JsonResponse|RedirectResponse
    {
        $userId = (int) $request->input('user_id');
        $rolSlug = (string) ($request->input('role_slug') ?? $request->input('rol') ?? 'alumno');

        $targetUser = User::findOrFail($userId);
        $roleModel = Rol::where('slug', $rolSlug)->orWhere('nombre', $rolSlug)->first();
        if ($roleModel) {
            $targetUser->roles()->sync([$roleModel->id]);
        }

        return back()->with('status', "Usuario {$targetUser->name} actualizado al rol {$targetUser->primaryRoleLabel()}.");
    }
}
