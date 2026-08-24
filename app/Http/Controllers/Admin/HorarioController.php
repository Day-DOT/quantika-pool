<?php

namespace App\Http\Controllers\Admin;

use App\Enums\DiaSemana;
use App\Http\Controllers\Admin\Concerns\ScopesSucursal;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AsignarAlumnoRequest;
use App\Http\Requests\Admin\CambiarGrupoRequest;
use App\Http\Requests\Admin\ReagendarHorarioRequest;
use App\Http\Requests\Admin\StoreHorarioRequest;
use App\Models\Alumno;
use App\Models\Carril;
use App\Models\Horario;
use App\Models\Inscripcion;
use App\Models\Instructor;
use App\Models\Nivel;
use App\Models\Sucursal;
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

        $horariosQuery = Horario::query()->where('activo', true)->with(['nivel', 'instructor.user', 'carril', 'inscripciones' => fn ($q) => $q->where('activa', true)]);
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

        $carrilesSelectQuery = Carril::query()->where('activo', true)->with('sucursal');
        $this->aplicarSucursal($carrilesSelectQuery);

        $alumnosQuery = Alumno::query()->with('inscripciones.horario')->orderBy('nombre');
        $this->aplicarSucursal($alumnosQuery);
        $alumnosTodos = $alumnosQuery->get();

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
            'instructoresDisponibles' => $instructoresQuery->get(),
            'carrilesDisponibles' => $carrilesSelectQuery->get(),
            'diasSemana' => DiaSemana::cases(),
            'horariosExistentes' => $horarios,
            'alumnosConInscripcion' => $alumnosTodos->filter(fn (Alumno $a) => $a->inscripciones->where('activa', true)->isNotEmpty()),
            // Un alumno puede tomar varias clases distintas la misma semana, así
            // que "Asignar alumno" se ofrece a todos, no solo a los que aún no
            // tienen ninguna inscripción activa; el backend evita duplicarlo en
            // el mismo grupo.
            'alumnosParaAsignar' => $alumnosTodos,
            'esVistaGlobal' => $this->sucursalId() === null,
            'sucursalesTodas' => Sucursal::orderBy('nombre')->get(),
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
            'nivel_id' => $datos['nivel_id'],
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

        $horario->update([
            'dia_semana' => $datos['dia_semana'],
            'hora_inicio' => $datos['hora_inicio'],
            'hora_fin' => $datos['hora_fin'],
            'carril_id' => $datos['carril_id'],
        ]);

        return redirect()->route('horarios.index')->with('status', "Clase \"{$horario->nombre_grupo}\" reagendada correctamente.");
    }

    public function asignarAlumno(AsignarAlumnoRequest $request): RedirectResponse
    {
        $datos = $request->validated();
        $alumno = Alumno::findOrFail($datos['alumno_id']);

        try {
            DB::transaction(function () use ($datos, $alumno) {
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
            DB::transaction(function () use ($alumno, $datos) {
                $horario = Horario::where('id', $datos['horario_id'])->lockForUpdate()->first();

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

                Inscripcion::where('alumno_id', $alumno->id)
                    ->where('activa', true)
                    ->update(['activa' => false, 'fecha_fin' => now()->toDateString()]);

                Inscripcion::create([
                    'horario_id' => $horario->id,
                    'alumno_id' => $alumno->id,
                    'fecha_inicio' => now()->toDateString(),
                    'fecha_fin' => null,
                    'activa' => true,
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
}
