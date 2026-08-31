<?php

use App\Http\Controllers\SuperAdmin\CarrilController;
use App\Http\Controllers\SuperAdmin\CriterioEvaluacionController;
use App\Http\Controllers\SuperAdmin\DashboardController;
use App\Http\Controllers\SuperAdmin\NivelController;
use App\Http\Controllers\SuperAdmin\PlanController;
use App\Http\Controllers\SuperAdmin\SeguridadController;
use App\Http\Controllers\SuperAdmin\SucursalContextController;
use App\Http\Controllers\SuperAdmin\SucursalController;
use App\Http\Controllers\SuperAdmin\UsuarioController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| SUPER ADMINISTRADOR
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:super_admin'])->group(function () {

    // Dashboard (global o filtrado por la sucursal elegida en el selector).
    Route::get('/super-admin', [DashboardController::class, 'index'])
        ->name('super-admin.dashboard');

    // Selector de sucursal del topbar (alterna entre sucursales o vista global).
    Route::post('/super-admin/sucursal-actual', [SucursalContextController::class, 'store'])
        ->name('super-admin.sucursal-actual');

    /*
    |----------------------------------------------------------------------
    | GESTIÓN MULTISUCURSAL
    |----------------------------------------------------------------------
    */

    Route::get('/super-administrador/sucursales', [SucursalController::class, 'index'])
        ->name('super-admin.sucursales.index');
    Route::get('/super-administrador/sucursales/crear', [SucursalController::class, 'create'])
        ->name('super-admin.sucursales.create');
    Route::post('/super-administrador/sucursales', [SucursalController::class, 'store'])
        ->name('super-admin.sucursales.store');
    Route::get('/super-administrador/sucursales/{sucursal}', [SucursalController::class, 'show'])
        ->name('super-admin.sucursales.show');
    Route::put('/super-administrador/sucursales/{sucursal}', [SucursalController::class, 'update'])
        ->name('super-admin.sucursales.update');
    Route::delete('/super-administrador/sucursales/{sucursal}', [SucursalController::class, 'destroy'])
        ->name('super-admin.sucursales.destroy');

    // Ruta reservada original: detalle/edición de la Sucursal 2 (vista generalizada).
    Route::get('/super-admin/sucursal-2', [SucursalController::class, 'showSucursalDos'])
        ->name('super-admin.sucursal-2');

    /*
    |----------------------------------------------------------------------
    | GESTIÓN DE USUARIOS (los 4 roles)
    |----------------------------------------------------------------------
    */

    Route::get('/super-administrador/usuarios', [UsuarioController::class, 'index'])
        ->name('super-admin.usuarios.index');
    Route::get('/super-administrador/usuarios/crear', [UsuarioController::class, 'create'])
        ->name('super-admin.usuarios.create');
    Route::post('/super-administrador/usuarios', [UsuarioController::class, 'store'])
        ->name('super-admin.usuarios.store');
    Route::get('/super-administrador/usuarios/{usuario}/editar', [UsuarioController::class, 'edit'])
        ->name('super-admin.usuarios.edit');
    Route::put('/super-administrador/usuarios/{usuario}', [UsuarioController::class, 'update'])
        ->name('super-admin.usuarios.update');
    Route::patch('/super-administrador/usuarios/{usuario}/estado', [UsuarioController::class, 'estado'])
        ->name('super-admin.usuarios.estado');

    /*
    |----------------------------------------------------------------------
    | SEGURIDAD (cambio de contraseña de la cuenta propia)
    |----------------------------------------------------------------------
    */

    Route::get('/super-administrador/seguridad', [SeguridadController::class, 'index'])
        ->name('super-admin.seguridad.index');
    Route::put('/super-administrador/seguridad', [SeguridadController::class, 'update'])
        ->name('super-admin.seguridad.update');

    /*
    |----------------------------------------------------------------------
    | NIVELES DE NATACIÓN
    |----------------------------------------------------------------------
    */

    Route::get('/super-administrador/niveles/crear', [NivelController::class, 'create'])
        ->name('super-admin.niveles.create');
    Route::post('/super-administrador/niveles', [NivelController::class, 'store'])
        ->name('super-admin.niveles.store');
    Route::get('/super-administrador/niveles/{nivel}/editar', [NivelController::class, 'edit'])
        ->name('super-admin.niveles.edit');
    Route::put('/super-administrador/niveles/{nivel}', [NivelController::class, 'update'])
        ->name('super-admin.niveles.update');

    /*
    |----------------------------------------------------------------------
    | CRITERIOS DE EVALUACIÓN (por nivel)
    |----------------------------------------------------------------------
    */

    Route::get('/super-administrador/criterios', [CriterioEvaluacionController::class, 'index'])
        ->name('super-admin.criterios.index');
    Route::get('/super-administrador/criterios/crear', [CriterioEvaluacionController::class, 'create'])
        ->name('super-admin.criterios.create');
    Route::post('/super-administrador/criterios', [CriterioEvaluacionController::class, 'store'])
        ->name('super-admin.criterios.store');
    Route::get('/super-administrador/criterios/{criterio}/editar', [CriterioEvaluacionController::class, 'edit'])
        ->name('super-admin.criterios.edit');
    Route::put('/super-administrador/criterios/{criterio}', [CriterioEvaluacionController::class, 'update'])
        ->name('super-admin.criterios.update');
    Route::delete('/super-administrador/criterios/{criterio}', [CriterioEvaluacionController::class, 'destroy'])
        ->name('super-admin.criterios.destroy');

    /*
    |----------------------------------------------------------------------
    | PLANES DE MENSUALIDAD
    |----------------------------------------------------------------------
    */

    Route::get('/super-administrador/planes', [PlanController::class, 'index'])
        ->name('super-admin.planes.index');
    Route::get('/super-administrador/planes/crear', [PlanController::class, 'create'])
        ->name('super-admin.planes.create');
    Route::post('/super-administrador/planes', [PlanController::class, 'store'])
        ->name('super-admin.planes.store');
    Route::get('/super-administrador/planes/{plan}/editar', [PlanController::class, 'edit'])
        ->name('super-admin.planes.edit');
    Route::put('/super-administrador/planes/{plan}', [PlanController::class, 'update'])
        ->name('super-admin.planes.update');

    /*
    |----------------------------------------------------------------------
    | CARRILES / ALBERCA (por sucursal)
    |----------------------------------------------------------------------
    */

    Route::get('/super-administrador/carriles', [CarrilController::class, 'index'])
        ->name('super-admin.carriles.index');
    Route::get('/super-administrador/carriles/crear', [CarrilController::class, 'create'])
        ->name('super-admin.carriles.create');
    Route::post('/super-administrador/carriles', [CarrilController::class, 'store'])
        ->name('super-admin.carriles.store');
    Route::get('/super-administrador/carriles/{carril}/editar', [CarrilController::class, 'edit'])
        ->name('super-admin.carriles.edit');
    Route::put('/super-administrador/carriles/{carril}', [CarrilController::class, 'update'])
        ->name('super-admin.carriles.update');
    Route::delete('/super-administrador/carriles/{carril}', [CarrilController::class, 'destroy'])
        ->name('super-admin.carriles.destroy');

});
