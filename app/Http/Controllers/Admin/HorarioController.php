<?php

namespace App\Http\Controllers\Admin;

use App\Enums\DiaSemana;
use App\Enums\EstadoInscripcion;
use App\Http\Controllers\Admin\Concerns\ScopesSucursal;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AsignarAlumnoRequest;
use App\Http\Requests\Admin\CambiarGrupoRequest;
use App\Http\Requests\Admin\CambiarInstructorRequest;
use App\Http\Requests\Admin\ReagendarHorarioRequest;
use App\Http\Requests\Admin\StoreHorarioRequest;
use App\Models\Alumno;
use App\Models\Carril;
use App\Models\Horario;
use App\Models\Inscripcion;
use App\Models\Instructor;
use App\Models\Nivel;
use App\Models\Sucursal;
use App\Support\ClasesDelAlumno;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class HorarioController extends Controller
{
    use AuthorizesRequests;
    use ScopesSucursal;

    public function index(): View
    {
        $this->authorize('viewAny', Horario::class);

        $hoy = Carbon::now();
        $inicioSemana = $hoy->copy()->startOfWeek(Carbon::MONDAY);

        $dias = collect(DiaSemana::cases())->map(function (DiaSemana $dia) use ($inicioSemana, $hoy) {
            $fecha = $inicioSemana->copy()->addDays($dia->value - 1);

            return [
                'dia' => $dia,
                'fecha' => $fecha,
                'esHoy' => $fecha->isSameDay($hoy),
            ];
        });

        $horariosQuery = Horario::query()->where('activo', true)->with([
            'nivel',
            'instructor.user',
            'carril',
            'inscripciones' => fn ($q) => $q->where('activa', true)->with('alumno'),
        ]);
        $this->aplicarSucursal($horariosQuery);
        $horarios = $horariosQuery->get();

        $horasBase = collect(range(8, 19))->map(fn ($h) => sprintf('%02d:00', $h));
        $horasReales = $horarios->map(fn (Horario $h) => substr($h->hora_inicio, 0, 5));
        $horas = $horasBase->merge($horasReales)->unique()->sort()->values();

        $grid = [];
        foreach ($horas as $hora) {
            foreach ($dias as $fila) {
                $grid[$hora][$fila['dia']->value] = $horarios->filter(function (Horario $h) use ($hora, $fila) {
                    return substr($h->hora_inicio, 0, 5) === $hora && $h->dia_semana === $fila['dia'];
                })->sort(function (Horario $a, Horario $b) {
                    $comparacion = strnatcasecmp($a->carril?->nombre ?? '', $b->carril?->nombre ?? '');

                    return $comparacion !== 0 ? $comparacion : $a->id <=> $b->id;
                })->values();
            }
        }

        $carrilesQuery = Carril::query()->where('activo', true);
        $this->aplicarSucursal($carrilesQuery);
        $totalCarriles = $carrilesQuery->count();
        $carrilesOcupados = $horarios->pluck('carril_id')->unique()->count();

        $horariosHoy = $horarios->filter(fn (Horario $h) => $h->dia_semana->value === $hoy->dayOfWeekIso)
            ->sortBy('hora_inicio')
            ->take(6)
            ->map(function (Horario $h) use ($hoy) {
                $estado = 'Programada';
                if ($h->hora_fin <= $hoy->format('H:i:s')) {
                    $estado = 'Finalizada';
                } elseif ($h->hora_inicio <= $hoy->format('H:i:s') && $h->hora_fin > $hoy->format('H:i:s')) {
                    $estado = 'En curso';
                }

                return ['horario' => $h, 'estado' => $estado];
            });

        $instructoresQuery = Instructor::query()->where('estado', 'activo')->with('user', 'sucursal');
        $this->aplicarSucursal($instructoresQuery);
        $instructoresDisponibles = $instructoresQuery->get();

        $carrilesSelectQuery = Carril::query()->where('activo', true)->with('sucursal');
        $this->aplicarSucursal($carrilesSelectQuery);

        $alumnosQuery = Alumno::query()->with('inscripciones.horario')->orderBy('nombre');
        $this->aplicarSucursal($alumnosQuery);
        $alumnosTodos = $alumnosQuery->get();

        // Todas las clases por delante de cada alumno (las de sus grupos y
        // las de una sola fecha), para poder elegir cuál reagendar.
        $clasesParaReagendar = ClasesDelAlumno::proximasDeTodos($this->sucursalId());

        return view('quantika.horarios.index', [
            'dias' => $dias,
            'horas' => $horas,
            'grid' => $grid,
            'semanaInicio' => $inicioSemana,
            'semanaFin' => $inicioSemana->copy()->addDays(6),
            'statClasesSemana' => $horarios->count(),
            'statInstructores' => $horarios->pluck('instructor_id')->unique()->count(),
            'statCarrilesOcupados' => $carrilesOcupados,
            'statCarrilesTotal' => $totalCarriles,
            'horariosHoy' => $horariosHoy,
            'niveles' => Nivel::ordenados()->get(),
            'instructoresDisponibles' => $instructoresDisponibles,
            'carrilesDisponibles' => $carrilesSelectQuery->get(),
            'diasSemana' => DiaSemana::cases(),
            // Ordenados por día, hora y carril para ubicarlos en las listas.
            'horariosExistentes' => $horarios->sort(fn (Horario $a, Horario $b) => [$a->dia_semana->value, $a->hora_inicio] <=> [$b->dia_semana->value, $b->hora_inicio]
                ?: strnatcasecmp($a->carril?->nombre ?? '', $b->carril?->nombre ?? ''))->values(),
            'detallesClases' => $horarios->mapWithKeys(fn (Horario $horario) => [
                $horario->id => [
                    'grupo' => $horario->nombre_grupo,
                    'dia' => $horario->dia_semana->label(),
                    'hora' => substr($horario->hora_inicio, 0, 5).' - '.substr($horario->hora_fin, 0, 5),
                    'carril' => $horario->carril?->nombre ?? 'Sin carril',
                    'instructor' => $horario->instructor?->user?->name ?? 'Sin instructor',
                    'instructor_id' => $horario->instructor_id,
                    'sucursal_id' => $horario->sucursal_id,
                    'capacidad' => $horario->capacidad_maxima,
                    'alumnos' => $horario->inscripciones->map(fn (Inscripcion $inscripcion) => [
                        'inscripcion_id' => $inscripcion->id,
                        'nombre' => $inscripcion->alumno?->nombreCompleto() ?? 'Alumno no disponible',
                        'telefono' => $inscripcion->alumno?->telefono,
                        'email' => $inscripcion->alumno?->email,
                        'estado' => $inscripcion->estado?->label() ?? 'Sin estado',
                    ])->values(),
                ],
            ]),
            'alumnosConInscripcion' => $alumnosTodos->filter(fn (Alumno $a) => $a->inscripciones->where('activa', true)->isNotEmpty()),
            // Un alumno puede tomar varias clases distintas la misma semana, así
            // que "Asignar alumno" se ofrece a todos, no solo a los que aún no
            // tienen ninguna inscripción activa; el backend evita duplicarlo en
            // el mismo grupo.
            'alumnosParaAsignar' => $alumnosTodos,
            'instructoresParaCambiar' => $instructoresDisponibles->map(fn (Instructor $instructor) => [
                'id' => $instructor->id,
                'sucursal_id' => $instructor->sucursal_id,
                'nombre' => $instructor->user?->name ?? 'Instructor sin usuario',
            ])->values(),
            'esVistaGlobal' => $this->sucursalId() === null,
            'sucursalesTodas' => Sucursal::orderBy('nombre')->get(),
            'clasesParaReagendar' => $clasesParaReagendar,
        ]);
    }

    public function store(StoreHorarioRequest $request): RedirectResponse
    {
        $datos = $request->validated();
        $sucursalId = $this->sucursalId() ?? (int) $datos['sucursal_id'];

        $instructor = Instructor::findOrFail($datos['instructor_id']);
        $carril = Carril::findOrFail($datos['carril_id']);

        if ($instructor->sucursal_id !== $sucursalId) {
            throw ValidationException::withMessages([
                'instructor_id' => 'Este instructor no pertenece a la sucursal seleccionada.',
            ]);
        }

        if ($carril->sucursal_id !== $sucursalId) {
            throw ValidationException::withMessages([
                'carril_id' => 'Este carril no pertenece a la sucursal seleccionada.',
            ]);
        }

        Horario::create([
            'sucursal_id' => $sucursalId,
            'nivel_id' => $datos['nivel_id'] ?? null,
            'instructor_id' => $datos['instructor_id'],
            'carril_id' => $datos['carril_id'],
            'nombre_grupo' => $datos['nombre_grupo'],
            'dia_semana' => $datos['dia_semana'],
            'hora_inicio' => $datos['hora_inicio'],
            'hora_fin' => $datos['hora_fin'],
            'capacidad_maxima' => $datos['capacidad_maxima'],
            'activo' => true,
        ]);

        return redirect()->route('horarios.index')->with('status', "Clase \"{$datos['nombre_grupo']}\" creada correctamente.");
    }

    public function reagendar(ReagendarHorarioRequest $request, Horario $horario): RedirectResponse
    {
        $datos = $request->validated();

        $carril = Carril::findOrFail($datos['carril_id']);

        if ($carril->sucursal_id !== $horario->sucursal_id) {
            throw ValidationException::withMessages([
                'carril_id' => 'Este carril no pertenece a la sucursal de esta clase.',
            ]);
        }

        $cambiaDeDia = (int) $datos['dia_semana'] !== $horario->dia_semana->value;

        DB::transaction(function () use ($horario, $datos, $cambiaDeDia) {
            $horario->update([
                'nombre_grupo' => $datos['nombre_grupo'],
                'dia_semana' => $datos['dia_semana'],
                'hora_inicio' => $datos['hora_inicio'],
                'hora_fin' => $datos['hora_fin'],
                'carril_id' => $datos['carril_id'],
                'capacidad_maxima' => $datos['capacidad_maxima'] ?? $horario->capacidad_maxima,
            ]);

            $clasesFuturas = $horario->citas()->whereDate('fecha', '>=', today())->whereNull('asistio');

            // Las clases apartadas en el día anterior ya no corresponden al
            // grupo; las de los inscritos se recalculan solas con el día nuevo.
            if ($cambiaDeDia) {
                (clone $clasesFuturas)->where('es_extra', false)->whereNull('reposicion_de_id')->delete();
            }

            $clasesFuturas->update([
                'hora_inicio' => $horario->hora_inicio,
                'hora_fin' => $horario->hora_fin,
            ]);
        });

        return redirect()->route('horarios.index')->with('status', "Clase \"{$horario->nombre_grupo}\" reagendada correctamente.");
    }

    public function cambiarInstructor(CambiarInstructorRequest $request, Horario $horario): RedirectResponse
    {
        $datos = $request->validated();

        $instructor = Instructor::findOrFail($datos['instructor_id']);

        if ($instructor->sucursal_id !== $horario->sucursal_id || $instructor->estado !== 'activo') {
            throw ValidationException::withMessages([
                'instructor_id' => 'Selecciona un instructor activo de la sucursal de esta clase.',
            ]);
        }

        $horario->update(['instructor_id' => $instructor->id]);

        return redirect()->route('horarios.index')->with(
            'status',
            "Instructor de \"{$horario->nombre_grupo}\" actualizado correctamente."
        );
    }

    public function destroy(Horario $horario): RedirectResponse
    {
        $this->authorize('delete', $horario);

        $inscritos = $horario->inscripciones()->where('activa', true)->count();
        $citasFuturas = $horario->citas()
            ->whereDate('fecha', '>=', today()->toDateString())
            ->whereNotIn('estado', [\App\Enums\EstadoCita::Cancelada->value, \App\Enums\EstadoCita::Completada->value])
            ->count();

        if ($inscritos > 0 || $citasFuturas > 0) {
            return back()->withErrors([
                'horario' => 'No se puede eliminar una clase con alumnos inscritos o clases futuras programadas.',
            ]);
        }

        $nombre = $horario->nombre_grupo;
        $horario->update(['activo' => false]);

        return back()->with('status', "La clase \"{$nombre}\" fue desactivada correctamente.");
    }

    public function asignarAlumno(AsignarAlumnoRequest $request): RedirectResponse
    {
        $datos = $request->validated();
        $alumno = Alumno::findOrFail($datos['alumno_id']);

        try {
            DB::transaction(function () use ($datos, $alumno, $request) {
                $horario = Horario::where('id', $datos['horario_id'])->lockForUpdate()->first();

                if (! $horario->activo) {
                    throw ValidationException::withMessages([
                        'horario_id' => 'Este grupo ya no está disponible.',
                    ]);
                }

                if ($alumno->sucursal_id !== $horario->sucursal_id) {
                    throw ValidationException::withMessages([
                        'alumno_id' => 'Este alumno no pertenece a la misma sucursal que este grupo.',
                    ]);
                }

                $inscritos = Inscripcion::where('horario_id', $horario->id)
                    ->where('activa', true)
                    ->lockForUpdate()
                    ->count();

                if ($inscritos >= $horario->capacidad_maxima) {
                    throw ValidationException::withMessages([
                        'horario_id' => 'Este grupo ya no tiene cupo disponible.',
                    ]);
                }

                $yaInscrito = Inscripcion::where('horario_id', $horario->id)
                    ->where('alumno_id', $alumno->id)
                    ->where('activa', true)
                    ->exists();

                if ($yaInscrito) {
                    throw ValidationException::withMessages([
                        'alumno_id' => 'Este alumno ya está inscrito en este grupo.',
                    ]);
                }

                Inscripcion::create([
                    'horario_id' => $horario->id,
                    'alumno_id' => $alumno->id,
                    'fecha_inicio' => now()->toDateString(),
                    'activa' => true,
                    'estado' => EstadoInscripcion::Aprobada->value,
                    'aprobado_por' => $request->user()->id,
                    'aprobado_en' => now(),
                ]);
            });
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors());
        }

        return redirect()->route('horarios.index')->with(
            'status',
            "{$alumno->nombreCompleto()} fue asignado a la clase correctamente."
        );
    }

    public function cambiarGrupo(CambiarGrupoRequest $request): RedirectResponse
    {
        $datos = $request->validated();
        $alumno = Alumno::findOrFail($datos['alumno_id']);
        $nuevoHorario = Horario::findOrFail($datos['horario_id']);

        try {
            DB::transaction(function () use ($alumno, $datos, $request) {
                $horario = Horario::where('id', $datos['horario_id'])->lockForUpdate()->first();
                $inscripcionOrigen = Inscripcion::query()
                    ->when($datos['inscripcion_id'] ?? null, fn ($query, $id) => $query->whereKey($id))
                    ->where('alumno_id', $alumno->id)
                    ->where('activa', true)
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($alumno->sucursal_id !== $horario->sucursal_id) {
                    throw ValidationException::withMessages([
                        'horario_id' => 'Este alumno no pertenece a la misma sucursal que este grupo.',
                    ]);
                }

                $inscritos = Inscripcion::where('horario_id', $horario->id)
                    ->where('activa', true)
                    ->lockForUpdate()
                    ->count();

                if ($inscritos >= $horario->capacidad_maxima) {
                    throw ValidationException::withMessages([
                        'horario_id' => 'Este grupo ya no tiene cupo disponible.',
                    ]);
                }

                if ($inscripcionOrigen->horario_id === $horario->id) {
                    throw ValidationException::withMessages([
                        'horario_id' => 'La clase destino debe ser diferente a la clase de origen.',
                    ]);
                }

                $inscripcionOrigen->update([
                    'activa' => false,
                    'fecha_fin' => now()->toDateString(),
                ]);

                // Sin esto el alumno seguía apareciendo con clases en el
                // grupo que dejó.
                ClasesDelAlumno::liberarClasesFuturas($alumno->id, $inscripcionOrigen->horario_id);

                Inscripcion::create([
                    'horario_id' => $horario->id,
                    'alumno_id' => $alumno->id,
                    'fecha_inicio' => now()->toDateString(),
                    'fecha_fin' => null,
                    'activa' => true,
                    'estado' => EstadoInscripcion::Aprobada->value,
                    'aprobado_por' => $request->user()->id,
                    'aprobado_en' => now(),
                ]);
            });
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors());
        }

        return redirect()->route('horarios.index')->with(
            'status',
            "{$alumno->nombreCompleto()} fue movido al grupo \"{$nuevoHorario->nombre_grupo}\"."
        );
    }

    public function retirarAlumno(Inscripcion $inscripcion): RedirectResponse
    {
        $horario = $inscripcion->horario()->firstOrFail();
        $this->authorize('delete', $horario);

        if (! $inscripcion->activa) {
            return back()->withErrors([
                'inscripcion' => 'Este alumno ya no está inscrito en la clase seleccionada.',
            ]);
        }

        DB::transaction(function () use ($inscripcion) {
            $inscripcion->update([
                'activa' => false,
                'fecha_fin' => today()->toDateString(),
            ]);

            ClasesDelAlumno::liberarClasesFuturas($inscripcion->alumno_id, $inscripcion->horario_id);
        });

        return back()->with('status', "{$inscripcion->alumno->nombreCompleto()} fue retirado únicamente de la clase seleccionada.");
    }
}
