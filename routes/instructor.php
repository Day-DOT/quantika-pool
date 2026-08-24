<?php

use App\Http\Controllers\Instructor\AgendaController;
use App\Http\Controllers\Instructor\AlumnoController;
use App\Http\Controllers\Instructor\DashboardController;
use App\Http\Controllers\Instructor\EvaluacionController;
use App\Http\Controllers\Instructor\GrupoController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| PORTAL DEL INSTRUCTOR
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:instructor'])->group(function () {

    Route::get('/instructor', [DashboardController::class, 'index'])
        ->name('instructor.dashboard');

    Route::get('/instructor/agenda', [AgendaController::class, 'index'])
        ->name('instructor.agenda');

    Route::get('/instructor/grupos/{horario}', [GrupoController::class, 'show'])
        ->name('instructor.grupos.show');

    Route::post('/instructor/grupos/{horario}/alumnos/{alumno}/asistencia', [GrupoController::class, 'marcarAsistencia'])
        ->name('instructor.grupos.asistencia');

    Route::get('/instructor/alumnos', [AlumnoController::class, 'index'])
        ->name('instructor.alumnos.index');

    Route::get('/instructor/alumnos/{alumno}', [AlumnoController::class, 'show'])
        ->name('instructor.alumnos.show');

    Route::patch('/instructor/alumnos/{alumno}/promover', [AlumnoController::class, 'promover'])
        ->name('instructor.alumnos.promover');

    Route::get('/instructor/alumnos/{alumno}/evaluar', [EvaluacionController::class, 'create'])
        ->name('instructor.evaluaciones.create');

    Route::post('/instructor/alumnos/{alumno}/evaluaciones', [EvaluacionController::class, 'store'])
        ->name('instructor.evaluaciones.store');

    Route::get('/instructor/evaluaciones', [EvaluacionController::class, 'index'])
        ->name('instructor.evaluaciones.index');

    Route::get('/instructor/evaluaciones/{evaluacion}/editar', [EvaluacionController::class, 'edit'])
        ->name('instructor.evaluaciones.edit');

    Route::put('/instructor/evaluaciones/{evaluacion}', [EvaluacionController::class, 'update'])
        ->name('instructor.evaluaciones.update');

});
