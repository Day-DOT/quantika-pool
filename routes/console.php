<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('pagos:generar-mensualidades')->dailyAt('06:00');

// Cada hora: falta a quien no registró asistencia en clases ya terminadas.
Schedule::command('asistencia:marcar-faltas')->hourly();
