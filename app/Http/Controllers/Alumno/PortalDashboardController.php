<?php

namespace App\Http\Controllers\Alumno;

use App\Enums\EstadoCita;
use App\Enums\EstadoPago;
use App\Http\Controllers\Alumno\Concerns\ResuelveAlumnoActivo;
use App\Http\Controllers\Controller;
use App\Models\Alumno;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PortalDashboardController extends Controller
{
    use AuthorizesRequests;
    use ResuelveAlumnoActivo;

    /**
     * Dashboard del portal de tutores.
     *
     * Si el tutor tiene un solo alumno, se muestra directo su resumen.
     * Si tiene varios, se muestra un selector con una tarjeta resumida
     * por cada uno, además del resumen detallado del que esté activo.
     */
    public function index(Request $request): View
    {
        $alumnos = $this->alumnosDelTutor($request);

        if ($alumnos->isEmpty()) {
            return view('quantika.portal.dashboard', [
                'alumnos' => $alumnos,
                'alumno' => null,
                'resumenes' => collect(),
                'resumenActivo' => null,
            ]);
        }

        $alumno = $this->alumnoActivo($request, $alumnos);

        $resumenes = $alumnos->mapWithKeys(
            fn (Alumno $unAlumno) => [$unAlumno->id => $this->resumenDe($unAlumno)]
        );

        return view('quantika.portal.dashboard', [
            'alumnos' => $alumnos,
            'alumno' => $alumno,
            'resumenes' => $resumenes,
            'resumenActivo' => $resumenes->get($alumno->id),
        ]);
    }

    /**
     * @return array{alumno: Alumno, porcentaje: float, proximasClases: \Illuminate\Support\Collection, pago: ?\App\Models\Pago}
     */
    private function resumenDe(Alumno $alumno): array
    {
        $alumno->loadMissing(['nivel', 'sucursal']);

        $ultimaEvaluacion = $alumno->evaluaciones()
            ->when($alumno->nivel_id, fn ($q) => $q->where('nivel_id', $alumno->nivel_id))
            ->latest('fecha')
            ->first();

        $proximasClases = $alumno->citas()
            ->with(['horario.nivel', 'horario.instructor.user'])
            ->whereIn('estado', [
                EstadoCita::Programada->value,
                EstadoCita::Confirmada->value,
                EstadoCita::Reagendada->value,
            ])
            ->whereDate('fecha', '>=', now()->toDateString())
            ->orderBy('fecha')
            ->orderBy('hora_inicio')
            ->limit(3)
            ->get();

        $pago = $alumno->pagos()
            ->whereIn('estado', [
                EstadoPago::Pendiente->value,
                EstadoPago::Vencido->value,
                EstadoPago::EnRevision->value,
            ])
            ->orderBy('fecha_vencimiento')
            ->first()
            ?? $alumno->pagos()->latest('fecha_vencimiento')->first();

        return [
            'alumno' => $alumno,
            'porcentaje' => $ultimaEvaluacion?->porcentajeAvance() ?? 0.0,
            'proximasClases' => $proximasClases,
            'pago' => $pago,
        ];
    }
}
