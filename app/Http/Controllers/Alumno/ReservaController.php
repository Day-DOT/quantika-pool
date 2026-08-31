<?php

namespace App\Http\Controllers\Alumno;

use App\Enums\EstadoInscripcion;
use App\Http\Controllers\Alumno\Concerns\ResuelveAlumnoActivo;
use App\Http\Controllers\Controller;
use App\Http\Requests\Alumno\ReservarClaseRequest;
use App\Models\Alumno;
use App\Models\Cita;
use App\Models\Horario;
use App\Models\Inscripcion;
use App\Models\Sucursal;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ReservaController extends Controller
{
    use AuthorizesRequests;
    use ResuelveAlumnoActivo;

    /**
     * Selección de sucursal y listado de horarios/grupos disponibles
     * para el alumno activo, filtrados por su nivel actual (y niveles
     * cercanos) y con el cupo disponible calculado en vivo.
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Horario::class);

        $alumnos = $this->alumnosDelTutor($request);
        $alumno = $this->alumnoActivo($request, $alumnos);

        $sucursales = Sucursal::where('activa', true)->orderBy('nombre')->get();

        if (! $alumno || $sucursales->isEmpty()) {
            return view('quantika.portal.reservar', [
                'alumnos' => $alumnos,
                'alumno' => $alumno,
                'sucursales' => $sucursales,
                'sucursalId' => null,
                'horarios' => collect(),
                'cuposDisponibles' => $alumno?->cuposDisponiblesParaReservar() ?? 0,
                'cuposUsados' => $alumno?->inscripcionesVigentes()->count() ?? 0,
            ]);
        }

        $sucursalId = (int) ($request->query('sucursal') ?? $alumno->sucursal_id ?? $sucursales->first()->id);

        $nivelActual = $alumno->nivel;

        $ordenesRelevantes = $nivelActual
            ? range(max(1, $nivelActual->orden - 1), $nivelActual->orden + 1)
            : [];

        $horarios = Horario::query()
            ->with(['nivel', 'instructor.user', 'carril'])
            ->where('sucursal_id', $sucursalId)
            ->where('activo', true)
            ->when(
                $nivelActual,
                fn ($query) => $query->whereHas('nivel', fn ($nivel) => $nivel->whereIn('orden', $ordenesRelevantes))
            )
            ->orderBy('dia_semana')
            ->orderBy('hora_inicio')
            ->get()
            ->map(function (Horario $horario) use ($alumno) {
                $inscritos = $horario->inscripciones()->activas()->count();
                $inscripcionAlumno = $horario->inscripciones()
                    ->where('alumno_id', $alumno->id)
                    ->first();

                $horario->cupo_disponible = max(0, $horario->capacidad_maxima - $inscritos);
                $horario->ya_inscrito = $inscripcionAlumno?->activa === true;
                $horario->ya_pendiente = $inscripcionAlumno?->estado === EstadoInscripcion::Pendiente;

                return $horario;
            });

        return view('quantika.portal.reservar', [
            'alumnos' => $alumnos,
            'alumno' => $alumno,
            'sucursales' => $sucursales,
            'sucursalId' => $sucursalId,
            'horarios' => $horarios,
            'cuposDisponibles' => $alumno->cuposDisponiblesParaReservar(),
            'cuposUsados' => $alumno->inscripcionesVigentes()->count(),
        ]);
    }

    /**
     * Reserva una o varias clases a la vez: crea una Inscripcion
     * "pendiente" del alumno en cada horario elegido (si hay cupo y el
     * alumno no excede las clases por semana de su plan). No ocupan cupo
     * ni agendan Citas todavía: un Admin debe aprobarlas primero (ver
     * Admin\ReservaController).
     *
     * El control de cupo se hace dentro de una transacción con bloqueo
     * de fila para evitar que dos tutores tomen a la vez el último lugar.
     */
    public function store(ReservarClaseRequest $request): RedirectResponse
    {
        $alumno = Alumno::findOrFail($request->validated('alumno_id'));
        $this->authorize('view', $alumno);
        $this->authorize('create', Cita::class);

        if (! $alumno->plan_id) {
            return back()->withErrors([
                'plan' => 'Este alumno no tiene un plan de mensualidad asignado. Contacta a la escuela para que le asignen uno antes de reservar clases.',
            ]);
        }

        $horarioIds = $request->validated('horario_ids');
        $sucursalId = null;

        try {
            DB::transaction(function () use ($alumno, $horarioIds, &$sucursalId) {
                $cuposDisponibles = $alumno->cuposDisponiblesParaReservar();

                if (count($horarioIds) > $cuposDisponibles) {
                    throw ValidationException::withMessages([
                        'horario_ids' => "Solo puedes reservar {$cuposDisponibles} clase(s) más según tu plan ({$alumno->plan->clases_por_semana} clases/semana).",
                    ]);
                }

                foreach ($horarioIds as $horarioId) {
                    $horarioSinBloqueo = Horario::findOrFail($horarioId);
                    $this->authorize('view', $horarioSinBloqueo);

                    $horario = Horario::where('id', $horarioId)->lockForUpdate()->first();
                    $sucursalId ??= $horario->sucursal_id;

                    if (! $horario->activo) {
                        throw ValidationException::withMessages([
                            'horario_ids' => "El grupo \"{$horario->nombre_grupo}\" ya no está disponible.",
                        ]);
                    }

                    $inscritos = Inscripcion::where('horario_id', $horario->id)
                        ->activas()
                        ->lockForUpdate()
                        ->count();

                    if ($inscritos >= $horario->capacidad_maxima) {
                        throw ValidationException::withMessages([
                            'horario_ids' => "Justo se acabó el cupo de \"{$horario->nombre_grupo}\". Elige otro horario disponible.",
                        ]);
                    }

                    $yaInscrito = Inscripcion::where('horario_id', $horario->id)
                        ->where('alumno_id', $alumno->id)
                        ->where(fn ($q) => $q->activas()->orWhere('estado', EstadoInscripcion::Pendiente->value))
                        ->exists();

                    if ($yaInscrito) {
                        throw ValidationException::withMessages([
                            'horario_ids' => "Este alumno ya está inscrito (o tiene una reserva pendiente) en \"{$horario->nombre_grupo}\".",
                        ]);
                    }

                    Inscripcion::create([
                        'horario_id' => $horario->id,
                        'alumno_id' => $alumno->id,
                        'fecha_inicio' => now()->toDateString(),
                        'activa' => false,
                        'estado' => EstadoInscripcion::Pendiente->value,
                    ]);
                }
            });
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        }

        return redirect()
            ->route('portal.reservar.index', ['alumno' => $alumno->id, 'sucursal' => $sucursalId])
            ->with('status', 'Reserva enviada para ' . $alumno->nombreCompleto() . '. Queda pendiente de aprobación por el administrador.');
    }
}
