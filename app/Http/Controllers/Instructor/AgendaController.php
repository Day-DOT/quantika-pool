<?php

namespace App\Http\Controllers\Instructor;

use App\Enums\DiaSemana;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Instructor\Concerns\ResuelveInstructor;
use App\Models\Horario;
use App\Models\Inscripcion;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AgendaController extends Controller
{
    use ResuelveInstructor;

    /**
     * Agenda semanal completa: todos los grupos del instructor,
     * agrupados por día (Lunes a Domingo), con el número de alumnos
     * inscritos vigentes en cada uno.
     */
    public function index(Request $request): View
    {
        $instructor = $this->instructorActivo($request);

        $horarios = Horario::with(['nivel', 'carril', 'sucursal'])
            ->delInstructor($instructor->id)
            ->orderBy('dia_semana')
            ->orderBy('hora_inicio')
            ->get();

        $alumnosPorHorario = Inscripcion::activas()
            ->whereIn('horario_id', $horarios->pluck('id'))
            ->get()
            ->groupBy('horario_id')
            ->map->count();

        $horariosPorDia = collect(DiaSemana::cases())
            ->mapWithKeys(fn (DiaSemana $dia) => [
                $dia->value => $horarios->where('dia_semana', $dia)->values(),
            ]);

        return view('quantika.instructor.agenda.index', [
            'horariosPorDia' => $horariosPorDia,
            'alumnosPorHorario' => $alumnosPorHorario,
        ]);
    }
}
