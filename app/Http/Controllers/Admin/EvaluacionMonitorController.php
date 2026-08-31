<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\ScopesSucursal;
use App\Http\Controllers\Controller;
use App\Models\Alumno;
use App\Models\Evaluacion;
use App\Models\Instructor;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\View\View;

class EvaluacionMonitorController extends Controller
{
    use AuthorizesRequests;
    use ScopesSucursal;

    public function index(): View
    {
        $this->authorize('viewAny', Evaluacion::class);

        $query = Instructor::query()->with('user', 'sucursal');
        $this->aplicarSucursal($query);

        $instructores = $query->get()->map(function (Instructor $instructor) {
            $alumnosIds = Alumno::query()
                ->whereHas('inscripciones', function ($q) use ($instructor) {
                    $q->where('activa', true)->whereHas('horario', function ($q2) use ($instructor) {
                        $q2->where('instructor_id', $instructor->id);
                    });
                })
                ->pluck('id');

            $evaluadosIds = Evaluacion::query()
                ->where('instructor_id', $instructor->id)
                ->whereIn('alumno_id', $alumnosIds)
                ->pluck('alumno_id')
                ->unique();

            return [
                'instructor' => $instructor,
                'iniciales' => $this->iniciales($instructor->user?->name ?? '??'),
                'totalAlumnos' => $alumnosIds->count(),
                'totalEvaluados' => $evaluadosIds->count(),
            ];
        });

        return view('quantika.evaluaciones.index', [
            'instructores' => $instructores,
        ]);
    }

    public function instructor(Instructor $instructor): View
    {
        $this->authorize('viewAny', Evaluacion::class);

        $instructor->load('user', 'sucursal');

        $alumnos = Alumno::query()
            ->whereHas('inscripciones', function ($q) use ($instructor) {
                $q->where('activa', true)->whereHas('horario', function ($q2) use ($instructor) {
                    $q2->where('instructor_id', $instructor->id);
                });
            })
            ->with(['nivel', 'evaluaciones' => function ($q) use ($instructor) {
                $q->where('instructor_id', $instructor->id)->orderByDesc('fecha');
            }])
            ->orderBy('nombre')
            ->get()
            ->map(function (Alumno $alumno) {
                $ultimaEvaluacion = $alumno->evaluaciones->first();

                return [
                    'alumno' => $alumno,
                    'ultimaEvaluacion' => $ultimaEvaluacion,
                    'porcentaje' => $ultimaEvaluacion?->porcentajeAvance(),
                ];
            });

        return view('quantika.evaluaciones.instructor', [
            'instructor' => $instructor,
            'alumnos' => $alumnos,
            'totalEvaluados' => $alumnos->filter(fn ($fila) => $fila['ultimaEvaluacion'] !== null)->count(),
        ]);
    }

    public function alumno(Alumno $alumno): View
    {
        $this->authorize('viewAny', Evaluacion::class);

        $alumno->load('nivel', 'sucursal', 'tutorUser');

        $evaluaciones = Evaluacion::query()
            ->where('alumno_id', $alumno->id)
            ->with(['instructor.user', 'nivel', 'detalles.criterio'])
            ->orderByDesc('fecha')
            ->get()
            ->map(fn (Evaluacion $evaluacion) => [
                'evaluacion' => $evaluacion,
                'porcentaje' => $evaluacion->porcentajeAvance(),
            ]);

        return view('quantika.evaluaciones.alumno', [
            'alumno' => $alumno,
            'evaluaciones' => $evaluaciones,
        ]);
    }

    private function iniciales(string $nombre): string
    {
        $partes = preg_split('/\s+/', trim($nombre));
        $primero = mb_substr($partes[0] ?? '', 0, 1);
        $segundo = mb_substr($partes[1] ?? '', 0, 1);

        return mb_strtoupper($primero.$segundo);
    }
}
