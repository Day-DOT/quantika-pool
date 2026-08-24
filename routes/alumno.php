<?php

use App\Http\Controllers\Alumno\CuentaController;
use App\Http\Controllers\Alumno\PortalDashboardController;
use App\Http\Controllers\Alumno\ProgresoController;
use App\Http\Controllers\Alumno\ReservaController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| PORTAL DE ALUMNOS / TUTORES
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:alumno'])->group(function () {

    Route::get('/portal', [PortalDashboardController::class, 'index'])
        ->name('portal.dashboard');

    Route::get('/portal/reservar', [ReservaController::class, 'index'])
        ->name('portal.reservar.index');

    Route::post('/portal/reservar', [ReservaController::class, 'store'])
        ->name('portal.reservar.store');

    Route::get('/portal/progreso', [ProgresoController::class, 'index'])
        ->name('portal.progreso');

    Route::get('/portal/cuenta', [CuentaController::class, 'index'])
        ->name('portal.cuenta');

});
