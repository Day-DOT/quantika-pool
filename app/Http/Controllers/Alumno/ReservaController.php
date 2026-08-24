<?php

namespace App\Http\Controllers\Alumno;

use App\Enums\EstadoCita;
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
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ReservaController extends Controller
{
    use AuthorizesRequests;
    use ResuelveAlumnoActivo;

    /**
     * Cuántas ocurrencias de "Cita" se agendan automáticamente al reservar.
     */
    private const CITAS_INICIALES = 4;

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

                $horario->cupo_disponible = max(0, $horario->capacidad_maxima - $inscritos);
                $horario->ya_inscrito = $horario->inscripciones()
                    ->activas()
                    ->where('alumno_id', $alumno->id)
                    ->exists();

                return $horario;
            });

        return view('quantika.portal.reservar', [
            'alumnos' => $alumnos,
            'alumno' => $alumno,
            'sucursales' => $sucursales,
            'sucursalId' => $sucursalId,
            'horarios' => $horarios,
        ]);
    }

    /**
     * Reserva una clase: crea la Inscripcion activa del alumno en el
     * horario elegido (si hay cupo) y agenda sus primeras Citas.
     *
     * El control de cupo se hace dentro de una transacción con bloqueo
     * de fila para evitar que dos tutores tomen a la vez el último lugar.
     */
    public function store(ReservarClaseRequest $request): RedirectResponse
    {
        $alumno = Alumno::findOrFail($request->validated('alumno_id'));
        $this->authorize('view', $alumno);

        $horarioSinBloqueo = Horario::findOrFail($request->validated('horario_id'));
        $this->authorize('view', $horarioSinBloqueo);
        $this->authorize('create', Cita::class);

        try {
            DB::transaction(function () use ($alumno, $horarioSinBloqueo, $request) {
                $horario = Horario::where('id', $horarioSinBloqueo->id)->lockForUpdate()->first();

                if (! $horario->activo) {
                    throw ValidationException::withMessages([
                        'horario_id' => 'Este grupo ya no está disponible.',
                    ]);
                }

                $inscritos = Inscripcion::where('horario_id', $horario->id)
                    ->activas()
                    ->lockForUpdate()
                    ->count();

                if ($inscritos >= $horario->capacidad_maxima) {
                    throw ValidationException::withMessages([
                        'horario_id' => 'Justo se acabó el cupo de este grupo. Elige otro horario disponible.',
                    ]);
                }

                $yaInscrito = Inscripcion::where('horario_id', $horario->id)
                    ->where('alumno_id', $alumno->id)
                    ->activas()
                    ->exists();

                if ($yaInscrito) {
                    throw ValidationException::withMessages([
                        'horario_id' => 'Este alumno ya está inscrito en este grupo.',
                    ]);
                }

                Inscripcion::create([
                    'horario_id' => $horario->id,
                    'alumno_id' => $alumno->id,
                    'fecha_inicio' => now()->toDateString(),
                    'activa' => true,
                ]);

                $this->agendarPrimerasCitas($horario, $alumno, $request->user()->id);
            });
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        }

        return redirect()
            ->route('portal.reservar.index', ['alumno' => $alumno->id, 'sucursal' => $horarioSinBloqueo->sucursal_id])
            ->with('status', 'Clase reservada. Se agendaron las primeras clases de ' . $alumno->nombreCompleto() . '.');
    }

    /**
     * Agenda las próximas ocurrencias de "Cita" para el día de la semana
     * del horario, a partir de hoy.
     */
    private function agendarPrimerasCitas(Horario $horario, Alumno $alumno, int $registradoPorUserId): void
    {
        $fecha = Carbon::today();
        $creadas = 0;

        while ($creadas < self::CITAS_INICIALES) {
            if ($fecha->isoWeekday() === $horario->dia_semana->value) {
                Cita::firstOrCreate(
                    [
                        'horario_id' => $horario->id,
                        'alumno_id' => $alumno->id,
                        'fecha' => $fecha->toDateString(),
                    ],
                    [
                        'sucursal_id' => $horario->sucursal_id,
                        'hora_inicio' => $horario->hora_inicio,
                        'hora_fin' => $horario->hora_fin,
                        'estado' => EstadoCita::Programada->value,
                        'registrado_por' => $registradoPorUserId,
                    ]
                );

                $creadas++;
            }

            $fecha = $fecha->copy()->addDay();
        }
    }
}
