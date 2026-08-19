<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| QUANTIKA POOL
|--------------------------------------------------------------------------
*/

// =============================
// DASHBOARD ADMINISTRADOR
// =============================
Route::get('/admin', function () {
    return view('quantika.admin.dashboard');
})->name('admin.dashboard');


// =============================
// ALUMNOS
// =============================
Route::get('/alumnos', function () {
    return view('quantika.alumnos.index');
})->name('alumnos.index');


// =============================
// CREAR ALUMNO
// =============================
Route::get('/alumnos/crear', function () {
    return view('quantika.alumnos.create');
})->name('alumnos.create');


// =============================
// PORTAL
// =============================
Route::get('/portal', function () {
    return view('quantika.portal.dashboard');
})->name('portal.dashboard');


// =============================
// INSTRUCTOR
// =============================
Route::get('/instructor', function () {
    return view('quantika.instructor.dashboard');
})->name('instructor.dashboard');


// =============================
// SUPER ADMINISTRADOR
// =============================
Route::get('/super-administrador/alumnos', function () {
    return view('quantika.super-admin.alumnos.index');
})->name('super-admin.alumnos.index');

Route::get('/super-administrador/alumnos/crear', function () {
    return view('quantika.super-admin.alumnos.create');
})->name('super-admin.alumnos.create');

Route::get('/super-administrador/alumnos/ver', function () {
    return view('quantika.super-admin.alumnos.show');
})->name('super-admin.alumnos.show');


// =============================
// SUCURSAL 2
// =============================
Route::get('/super-admin/sucursal-2', function () {
    return view('quantika.super-admin.sucursales.sucursal-2');
})->name('super-admin.sucursal-2');

Route::get('/instructores', function () {
    return view('quantika.instructores.index');
})->name('instructores.index');

Route::get('/evaluaciones', function () {
    return view('quantika.evaluaciones.index');
})->name('evaluaciones.index');

Route::get('/evaluaciones/instructor/{id}', function ($id) {
    return view('quantika.evaluaciones.instructor', [
        'instructorId' => $id
    ]);
})->name('evaluaciones.instructor');

Route::get('/evaluaciones/alumno/{id}', function ($id) {
    return view('quantika.evaluaciones.alumno', [
        'alumnoId' => $id
    ]);
})->name('evaluaciones.alumno');
Route::get('/pagos', function () {
    return view('quantika.pagos.index');
})->name('pagos.index');

Route::get('/pagos/registrar', function () {
    return view('quantika.pagos.registrar');
})->name('pagos.registrar');

Route::get('/pagos/alumno/{id}', function ($id) {
    return view('quantika.pagos.alumno', [
        'alumnoId' => $id
    ]);
})->name('pagos.alumno');

Route::get('/pagos/deudores', function () {
    return view('quantika.pagos.deudores');
})->name('pagos.deudores');
Route::get('/horarios', function () {
    return view('quantika.horarios.index');
})->name('horarios.index');
Route::get('/niveles', function () {
    return view('quantika.niveles.index');
})->name('niveles.index');
Route::get('/configuracion', function () {
    return view('quantika.configuracion.index');
})->name('configuracion.index');