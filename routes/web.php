<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PanelOperativoController;
use App\Http\Controllers\PublicController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes · MetalNet · Laboratorios de Ingeniería en Sistemas
|--------------------------------------------------------------------------
*/

// ==========================================
// VISTAS PÚBLICAS (NAVEGACIÓN PRINCIPAL)
// ==========================================
Route::get('/', [PublicController::class, 'index'])->name('home');
Route::get('/inicio', [PublicController::class, 'index'])->name('inicio');
Route::get('/salas', [PublicController::class, 'salas'])->name('public.salas');
Route::get('/horarios', [PublicController::class, 'horarios'])->name('public.horarios');
Route::get('/software', [PublicController::class, 'software'])->name('software.index');
Route::post('/software/solicitar', [PanelOperativoController::class, 'solicitarRecurso'])->name('software.solicitar');

// Reporte de averías / fallos
Route::get('/reportar-averia', [PublicController::class, 'reportarAveria'])->name('public.averia');
Route::post('/reportar-averia', [PublicController::class, 'guardarAveria'])->name('public.averia.store');

// ==========================================
// AUTENTICACIÓN Y REGISTRO INSTITUCIONAL
// ==========================================
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ==========================================
// PANELES PRIVADOS PROTEGIDOS POR ROL
// ==========================================
Route::middleware('auth')->group(function () {
    // Enrutador automático según el rol del usuario autenticado
    Route::get('/panel-operativo', [PanelOperativoController::class, 'index'])->name('panel.operativo');

    // 1. Panel Privado del Administrador (role:admin)
    Route::get('/panel/admin', [PanelOperativoController::class, 'admin'])
        ->middleware('role:admin')
        ->name('panel.admin');

    // 2. Redirección de rol Encargado (absorbido por Admin)
    Route::get('/panel/encargado', fn() => redirect()->route('panel.admin'))->name('panel.encargado');

    // 3. Panel Privado del Docente / Maestro (role:docente)
    Route::get('/panel/docente', [PanelOperativoController::class, 'docente'])
        ->middleware('role:docente,admin')
        ->name('panel.docente');

    // 4. Panel Privado del Alumno (role:alumno)
    Route::get('/panel/alumno', [PanelOperativoController::class, 'alumno'])
        ->middleware('role:alumno,docente,admin')
        ->name('panel.alumno');

    // Acciones Operativas Interactivas (Protegidas por Rol)
    Route::post('/panel/pc-estado', [PanelOperativoController::class, 'cambiarEstadoPc'])
        ->middleware('role:admin')
        ->name('panel.pc-estado');

    Route::post('/panel/solicitud-resolver', [PanelOperativoController::class, 'resolverSolicitud'])
        ->middleware('role:admin')
        ->name('panel.solicitud-resolver');

    Route::post('/panel/usuario-rol', [PanelOperativoController::class, 'actualizarUsuario'])
        ->middleware('role:admin')
        ->name('panel.usuario-rol');

    Route::post('/panel/reservar-sala', [PanelOperativoController::class, 'reservarSala'])
        ->middleware('role:docente,admin')
        ->name('panel.reservar-sala');

    Route::post('/panel/reservar-pc', [PanelOperativoController::class, 'reservarPc'])
        ->middleware('role:alumno,docente,admin')
        ->name('panel.reservar-pc');

    Route::post('/panel/solicitar-recurso', [PanelOperativoController::class, 'solicitarRecurso'])
        ->middleware('role:alumno,docente,admin')
        ->name('panel.solicitar-recurso');

    // Redirecciones internas seguras
    Route::get('/malla-topologica', fn() => redirect()->route('public.salas'))->name('admin.malla');
    Route::get('/mesa-incidencias', fn() => redirect()->route('panel.operativo'))->name('admin.incidencias');
    Route::get('/inventario', fn() => redirect()->route('panel.operativo'))->name('admin.inventario');
    Route::get('/admin/horarios', fn() => redirect()->route('public.horarios'))->name('admin.horarios');
    Route::get('/admin/software', fn() => redirect()->route('software.index'))->name('admin.software');
});
