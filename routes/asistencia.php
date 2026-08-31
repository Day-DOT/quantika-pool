<?php

use App\Http\Controllers\AsistenciaQrController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| ASISTENCIA POR CÓDIGO QR (compartido: Admin y Super Admin)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:admin,super_admin'])->group(function () {

    Route::get('/asistencia/escanear', [AsistenciaQrController::class, 'escanear'])
        ->name('asistencia.escanear');

    Route::get('/asistencia/qr/{token}', [AsistenciaQrController::class, 'registrar'])
        ->name('asistencia.registrar');

});
