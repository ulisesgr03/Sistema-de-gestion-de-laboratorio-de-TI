<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PanelOperativoController;
use App\Http\Controllers\PublicController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes · Sistema de Gestión de Laboratorios de TI · UPTex
|--------------------------------------------------------------------------
*/

// ==========================================
// VISTAS PÚBLICAS Y SERVICIOS TECNOLÓGICOS
// ==========================================
Route::get('/', [PublicController::class, 'index'])->name('home');
Route::get('/inicio', [PublicController::class, 'index'])->name('inicio');
Route::get('/salas', [PublicController::class, 'salas'])->name('salas.index');
Route::get('/horarios', [PublicController::class, 'horarios'])->name('horarios.index');
Route::get('/software', [PublicController::class, 'software'])->name('software.index');
Route::get('/prestamos', [PublicController::class, 'prestamos'])->name('prestamos.index');
Route::get('/reglamento', [PublicController::class, 'reglamento'])->name('reglamento.index');

// Reporte de Averías Técnicas (Público / Estudiantes y Docentes)
Route::get('/reportar-averia', [PublicController::class, 'reportarAveria'])->name('averia.create');
Route::post('/reportar-averia', [PublicController::class, 'guardarAveria'])->name('averia.store');

// ==========================================
// INICIO DE SESIÓN INSTITUCIONAL (AUTH)
// ==========================================
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ==========================================
// ACCESO PRIVADO / CONSOLA OPERATIVA
// ==========================================
Route::middleware('auth')->group(function () {
    Route::get('/panel-operativo', [PanelOperativoController::class, 'index'])->name('panel.operativo');

    // Accesos y enlaces rápidos de la barra de gestión
    Route::get('/malla-topologica', fn() => redirect()->route('salas.index'))->name('admin.malla');
    Route::get('/mesa-incidencias', fn() => redirect()->route('averia.create'))->name('admin.incidencias');
    Route::get('/inventario', fn() => redirect()->route('prestamos.index'))->name('admin.inventario');
    Route::get('/admin/horarios', fn() => redirect()->route('horarios.index'))->name('admin.horarios');
    Route::get('/admin/software', fn() => redirect()->route('software.index'))->name('admin.software');
});
