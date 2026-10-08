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

    // Dirección corta que se imprime en los códigos QR: menos caracteres
    // dan un código menos denso y más fácil de leer. Los códigos ya
    // impresos con la dirección larga siguen funcionando.
    Route::get('/q/{token}', [AsistenciaQrController::class, 'registrar'])
        ->name('asistencia.qr');
    Route::post('/asistencia/qr/{token}', [AsistenciaQrController::class, 'confirmar'])
        ->name('asistencia.confirmar');

});
