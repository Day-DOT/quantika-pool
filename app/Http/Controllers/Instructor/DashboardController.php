<?php

namespace App\Http\Controllers\Instructor;

use App\Enums\DiaSemana;
use App\Http\Controllers\Controller;
use App\Models\Alumno;
use App\Models\Cita;
use App\Models\Horario;
use App\Models\Inscripcion;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Agenda docente: resumen de grupos por día de la semana, clases y
     * citas de hoy, próximas clases y estadísticas rápidas del instructor
     * autenticado. Si el usuario todavía no tiene perfil de instructor
     * asignado, se muestra un estado vacío en lugar de un error, ya que
     * esta es la primera pantalla a la que llega tras iniciar sesión.
     */
    public function index(Request $request): View
    {
        $instructor = $request->user()->instructor;

        if ($instructor === null) {
            return view('quantika.instructor.dashboard', [
                'sinPerfil' => true,
            ]);
        }

        $hoy = today();
        $diaHoy = DiaSemana::from($hoy->dayOfWeekIso);

        $horarios = Horario::with(['nivel', 'carril'])
            ->delInstructor($instructor->id)
            ->where('activo', true)
            ->orderBy('dia_semana')
            ->orderBy('hora_inicio')
            ->get();

        $horarioIds = $horarios->pluck('id');

        $alumnosPorHorario = Inscripcion::activas()
            ->whereIn('horario_id', $horarioIds)
            ->get()
            ->groupBy('horario_id')
            ->map->count();

        $alumnosIdsUnicos = Inscripcion::activas()
            ->whereIn('horario_id', $horarioIds)
            ->pluck('alumno_id')
            ->unique();

        $horariosPorDia = $horarios->groupBy(fn (Horario $h) => $h->dia_semana->value);

        $horariosHoy = $horarios->where('dia_semana', $diaHoy);

        $citasHoy = Cita::with('alumno')
            ->whereIn('horario_id', $horariosHoy->pluck('id'))
            ->whereDate('fecha', $hoy)
            ->get()
            ->groupBy('horario_id');

        $proximasClases = $horarios
            ->sortBy(fn (Horario $h) => sprintf('%d-%s', ($h->dia_semana->value - $diaHoy->value + 7) % 7, $h->hora_inicio))
            ->values()
            ->take(6);

        $pendientesEvaluacion = Alumno::whereIn('id', $alumnosIdsUnicos)
            ->whereNotNull('nivel_id')
            ->whereDoesntHave('evaluaciones', function ($query) use ($instructor) {
                $query->where('instructor_id', $instructor->id)
                    ->whereColumn('nivel_id', 'alumnos.nivel_id');
            })
            ->count();

        return view('quantika.instructor.dashboard', [
            'sinPerfil' => false,
            'instructor' => $instructor,
            'hoy' => $hoy,
            'diaHoy' => $diaHoy,
            'stats' => [
                'grupos' => $horarios->count(),
                'alumnos' => $alumnosIdsUnicos->count(),
                'clasesHoy' => $horariosHoy->count(),
                'pendientesEvaluacion' => $pendientesEvaluacion,
            ],
            'horariosPorDia' => $horariosPorDia,
            'horariosHoy' => $horariosHoy,
            'citasHoy' => $citasHoy,
            'alumnosPorHorario' => $alumnosPorHorario,
            'proximasClases' => $proximasClases,
        ]);
    }
}
