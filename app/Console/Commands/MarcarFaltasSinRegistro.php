<?php

namespace App\Console\Commands;

use App\Support\ClasesDelAlumno;
use Illuminate\Console\Command;

class MarcarFaltasSinRegistro extends Command
{
    protected $signature = 'asistencia:marcar-faltas {--dias=1 : Días anteriores a hoy que también se revisan}';

    protected $description = 'Marca falta a los alumnos que no registraron asistencia en clases que ya terminaron';

    public function handle(): int
    {
        $hoy = today();
        $marcadas = 0;

        for ($dias = (int) $this->option('dias'); $dias >= 0; $dias--) {
            $marcadas += ClasesDelAlumno::marcarFaltasSinRegistro($hoy->copy()->subDays($dias));
        }

        $this->info("Se marcaron {$marcadas} faltas por clases sin asistencia registrada.");

        return self::SUCCESS;
    }
}
