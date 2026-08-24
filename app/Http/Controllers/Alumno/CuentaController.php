<?php

namespace App\Http\Controllers\Alumno;

use App\Enums\EstadoCita;
use App\Enums\EstadoPago;
use App\Http\Controllers\Alumno\Concerns\ResuelveAlumnoActivo;
use App\Http\Controllers\Controller;
use App\Models\Pago;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CuentaController extends Controller
{
    use AuthorizesRequests;
    use ResuelveAlumnoActivo;

    /**
     * Estado de cuenta y clases del alumno: únicamente lectura, el
     * tutor no paga en línea aquí, solo consulta su estatus.
     */
    public function index(Request $request): View
    {
        $alumnos = $this->alumnosDelTutor($request);
        $alumno = $this->alumnoActivo($request, $alumnos);

        if (! $alumno) {
            return view('quantika.portal.cuenta', [
                'alumnos' => $alumnos,
                'alumno' => null,
                'proximasClases' => collect(),
                'pagos' => collect(),
                'pendientesCount' => 0,
            ]);
        }

        $this->authorize('viewAny', Pago::class);

        $proximasClases = $alumno->citas()
            ->with(['horario.nivel', 'horario.instructor.user', 'horario.carril'])
            ->whereIn('estado', [
                EstadoCita::Programada->value,
                EstadoCita::Confirmada->value,
                EstadoCita::Reagendada->value,
            ])
            ->whereDate('fecha', '>=', now()->toDateString())
            ->orderBy('fecha')
            ->orderBy('hora_inicio')
            ->get();

        $pagos = $alumno->pagos()
            ->orderByDesc('fecha_vencimiento')
            ->orderByDesc('created_at')
            ->get();

        $pendientesCount = $pagos->whereIn('estado', [
            EstadoPago::Pendiente,
            EstadoPago::Vencido,
        ])->count();

        return view('quantika.portal.cuenta', [
            'alumnos' => $alumnos,
            'alumno' => $alumno,
            'proximasClases' => $proximasClases,
            'pagos' => $pagos,
            'pendientesCount' => $pendientesCount,
        ]);
    }
}
