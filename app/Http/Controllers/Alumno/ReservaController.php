<?php

namespace App\Http\Controllers\Alumno;

use App\Enums\EstadoInscripcion;
use App\Http\Controllers\Alumno\Concerns\ResuelveAlumnoActivo;
use App\Http\Controllers\Controller;
use App\Http\Requests\Alumno\ReservarClaseRequest;
use App\Models\Alumno;
use App\Models\Horario;
use App\Models\SolicitudReposicion;
use App\Support\Reposiciones;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class ReservaController extends Controller
{
    use AuthorizesRequests;
    use ResuelveAlumnoActivo;

    /**
     * Pantalla "Recuperar clase": el tutor ve las faltas del mes que puede
     * reponer, elige una fecha y se le muestran los grupos de la sucursal y
     * categoría de edad del alumno que dan clase ese día, con su cupo.
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Horario::class);

        $alumnos = $this->alumnosDelTutor($request);
        $alumno = $this->alumnoActivo($request, $alumnos);

        if (! $alumno) {
            return view('quantika.portal.reservar', [
                'alumnos' => $alumnos,
                'alumno' => null,
            ]);
        }

        $faltas = Reposiciones::faltasPorReponer($alumno)->with('horario')->orderBy('fecha')->get();

        $solicitudes = SolicitudReposicion::where('alumno_id', $alumno->id)
            ->with(['horario', 'falta'])
            ->latest()
            ->limit(8)
            ->get();
        $pendientes = $solicitudes->where('estado', EstadoInscripcion::Pendiente);

        // Una falta con solicitud en espera no se puede volver a pedir.
        $faltasLibres = $faltas->whereNotIn('id', $pendientes->pluck('cita_id'))->values();

        $restantesDelMes = max(
            0,
            Reposiciones::MAXIMO_POR_MES - Reposiciones::usadasEnElMes($alumno, now()) - $pendientes->count()
        );

        $admiteReposicion = Reposiciones::admiteReposicion($alumno);
        $puedeSolicitar = $admiteReposicion && $faltasLibres->isNotEmpty() && $restantesDelMes > 0;

        $fecha = $this->fechaElegida($request);

        return view('quantika.portal.reservar', [
            'alumnos' => $alumnos,
            'alumno' => $alumno,
            'faltas' => $faltas,
            'faltasLibres' => $faltasLibres,
            'solicitudes' => $solicitudes,
            'restantesDelMes' => $restantesDelMes,
            'maximoPorMes' => Reposiciones::MAXIMO_POR_MES,
            'admiteReposicion' => $admiteReposicion,
            'puedeSolicitar' => $puedeSolicitar,
            'fecha' => $fecha,
            'finDeMes' => today()->endOfMonth(),
            'grupos' => $puedeSolicitar ? Reposiciones::gruposParaReponer($alumno, $fecha) : collect(),
        ]);
    }

    /**
     * Envía la solicitud de reposición. No ocupa lugar ni agenda la clase
     * todavía: la administración la aprueba (ver Admin\ReposicionController)
     * y entonces se crea la clase para esa única fecha.
     */
    public function store(ReservarClaseRequest $request): RedirectResponse
    {
        $datos = $request->validated();

        $alumno = Alumno::findOrFail($datos['alumno_id']);
        $this->authorize('view', $alumno);

        $falta = Reposiciones::faltasPorReponer($alumno)->find($datos['cita_id']);

        if (! $falta) {
            return back()->withErrors(['cita_id' => 'Esa falta ya no está disponible para reponer.'])->withInput();
        }

        $pendientes = SolicitudReposicion::pendientes()->where('alumno_id', $alumno->id)->get();

        if ($pendientes->contains('cita_id', $falta->id)) {
            return back()->withErrors(['cita_id' => 'Ya enviaste una solicitud para reponer esa falta. Espera la respuesta de la escuela.'])->withInput();
        }

        if (Reposiciones::usadasEnElMes($alumno, $falta->fecha) + $pendientes->count() >= Reposiciones::MAXIMO_POR_MES) {
            return back()->withErrors([
                'cita_id' => 'Ya alcanzaste el máximo de '.Reposiciones::MAXIMO_POR_MES.' reposiciones por mes (contando las solicitudes en espera).',
            ])->withInput();
        }

        $horario = Horario::findOrFail($datos['horario_id']);
        $fecha = Carbon::parse($datos['fecha'])->startOfDay();

        if ($motivo = Reposiciones::motivoDeRechazo($falta, $horario, $fecha)) {
            return back()->withErrors([$motivo[0] => $motivo[1]])->withInput();
        }

        SolicitudReposicion::create([
            'cita_id' => $falta->id,
            'alumno_id' => $alumno->id,
            'horario_id' => $horario->id,
            'fecha' => $fecha->toDateString(),
            'estado' => EstadoInscripcion::Pendiente->value,
        ]);

        return redirect()
            ->route('portal.reservar.index', ['alumno' => $alumno->id])
            ->with('status', 'Solicitud de reposición enviada para '.$alumno->nombreCompleto().'. Queda pendiente de aprobación por la escuela.');
    }

    /**
     * La reposición debe caer en el mismo mes de la falta, así que la
     * fecha se limita a lo que resta del mes en curso.
     */
    private function fechaElegida(Request $request): Carbon
    {
        $hoy = today();

        try {
            $fecha = $request->filled('fecha') ? Carbon::parse($request->query('fecha'))->startOfDay() : $hoy;
        } catch (\Throwable) {
            return $hoy;
        }

        return $fecha->between($hoy, $hoy->copy()->endOfMonth()) ? $fecha : $hoy;
    }
}
