<?php

/*
|--------------------------------------------------------------------------
| QUANTIKA POOL
|--------------------------------------------------------------------------
|
| Las rutas se dividen por rol en archivos independientes para que cada
| módulo se pueda desarrollar sin pisar los demás.
|
*/

require __DIR__.'/auth.php';
require __DIR__.'/super_admin.php';
require __DIR__.'/admin.php';
require __DIR__.'/instructor.php';
require __DIR__.'/alumno.php';
require __DIR__.'/asistencia.php';
