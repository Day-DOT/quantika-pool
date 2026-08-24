<?php

use App\Http\Controllers\Admin\AlumnoController;
use App\Http\Controllers\Admin\CarrilController;
use App\Http\Controllers\Admin\ConfiguracionController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EvaluacionMonitorController;
use App\Http\Controllers\Admin\HorarioController;
use App\Http\Controllers\Admin\InstructorController;
use App\Http\Controllers\Admin\NivelController;
use App\Http\Controllers\Admin\PagoController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| ADMINISTRADOR / RECEPCIÓN (por sucursal) — también accesible por Super Admin
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:admin,super_admin'])->group(function () {

    Route::get('/admin', [DashboardController::class, 'index'])->name('admin.dashboard');

    // --- Alumnos ---
    Route::get('/alumnos', [AlumnoController::class, 'index'])->name('alumnos.index');
    Route::get('/alumnos/crear', [AlumnoController::class, 'create'])->name('alumnos.create');
    Route::post('/alumnos', [AlumnoController::class, 'store'])->name('alumnos.store');
    Route::get('/alumnos/{alumno}', [AlumnoController::class, 'show'])->name('alumnos.show');
    Route::get('/alumnos/{alumno}/editar', [AlumnoController::class, 'edit'])->name('alumnos.edit');
    Route::put('/alumnos/{alumno}', [AlumnoController::class, 'update'])->name('alumnos.update');
    Route::patch('/alumnos/{alumno}/baja', [AlumnoController::class, 'baja'])->name('alumnos.baja');
    Route::patch('/alumnos/{alumno}/reactivar', [AlumnoController::class, 'reactivar'])->name('alumnos.reactivar');

    // --- Instructores ---
    Route::get('/instructores', [InstructorController::class, 'index'])->name('instructores.index');
    Route::post('/instructores', [InstructorController::class, 'store'])->name('instructores.store');
    Route::put('/instructores/{instructor}', [InstructorController::class, 'update'])->name('instructores.update');
    Route::patch('/instructores/{instructor}/estado', [InstructorController::class, 'toggleEstado'])->name('instructores.toggle-estado');

    // --- Evaluaciones (monitoreo, solo lectura) ---
    Route::get('/evaluaciones', [EvaluacionMonitorController::class, 'index'])->name('evaluaciones.index');
    Route::get('/evaluaciones/instructor/{instructor}', [EvaluacionMonitorController::class, 'instructor'])->name('evaluaciones.instructor');
    Route::get('/evaluaciones/alumno/{alumno}', [EvaluacionMonitorController::class, 'alumno'])->name('evaluaciones.alumno');

    // --- Pagos ---
    Route::get('/pagos', [PagoController::class, 'index'])->name('pagos.index');
    Route::get('/pagos/registrar', [PagoController::class, 'create'])->name('pagos.registrar');
    Route::post('/pagos', [PagoController::class, 'store'])->name('pagos.store');
    Route::get('/pagos/alumno/{alumno}', [PagoController::class, 'alumno'])->name('pagos.alumno');
    Route::get('/pagos/deudores', [PagoController::class, 'deudores'])->name('pagos.deudores');
    Route::patch('/pagos/{pago}/marcar-pagado', [PagoController::class, 'marcarPagado'])->name('pagos.marcar-pagado');

    // --- Horarios ---
    Route::get('/horarios', [HorarioController::class, 'index'])->name('horarios.index');
    Route::post('/horarios', [HorarioController::class, 'store'])->name('horarios.store');
    Route::patch('/horarios/{horario}/reagendar', [HorarioController::class, 'reagendar'])->name('horarios.reagendar');
    Route::post('/inscripciones', [HorarioController::class, 'asignarAlumno'])->name('inscripciones.store');
    Route::patch('/inscripciones/cambiar-grupo', [HorarioController::class, 'cambiarGrupo'])->name('inscripciones.cambiar-grupo');

    // --- Niveles (solo lectura, datos fijos del sistema) ---
    Route::get('/niveles', [NivelController::class, 'index'])->name('niveles.index');

    // --- Configuración ---
    Route::get('/configuracion', [ConfiguracionController::class, 'index'])->name('configuracion.index');
    Route::get('/configuracion/carriles', [CarrilController::class, 'index'])->name('carriles.index');
    Route::post('/configuracion/carriles', [CarrilController::class, 'store'])->name('carriles.store');
    Route::put('/configuracion/carriles/{carril}', [CarrilController::class, 'update'])->name('carriles.update');
    Route::delete('/configuracion/carriles/{carril}', [CarrilController::class, 'destroy'])->name('carriles.destroy');

});
