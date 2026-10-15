<?php

namespace App\Support;

use App\Enums\EstadoAlumno;
use App\Enums\EstadoCita;
use App\Models\Alumno;
use App\Models\Cita;
use App\Models\Horario;
use App\Models\Inscripcion;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * Las clases de un alumno se deducen de sus inscripciones: mientras esté
 * inscrito en un grupo, tiene clase cada semana el día de ese grupo. No
 * hace falta que exista una Cita por cada fecha.
 *
 * Una Cita solo se guarda cuando esa fecha tiene algo propio: asistencia o
 * falta registrada, una clase de una sola fecha (extra, reposición o
 * reagendada) o la marca de que la clase de ese día se movió a otra fecha.
 */
class ClasesDelAlumno
{
    public const TIPO_PLAN = 'plan';

    public const TIPO_EXTRA = 'extra';

    public const TIPO_REPOSICION = 'reposicion';

    public const TIPO_REAGENDADA = 'reagendada';

    /**
     * Desde esta fecha las clases sin asistencia registrada cuentan como
     * falta automáticamente; las anteriores se registran a mano.
     */
    public const INICIO_FALTAS_AUTOMATICAS = '2026-10-14';

    /** Minutos después del fin de la clase antes de marcar la falta. */
    public const MINUTOS_DE_GRACIA = 15;

    /** Estados en los que la clase ya no ocurre en esa fecha y grupo. */
    private const ESTADOS_SIN_CLASE = [EstadoCita::Cancelada, EstadoCita::Reagendada];

    private const RELACIONES_HORARIO = ['horario.nivel', 'horario.instructor.user', 'horario.carril'];

    /**
     * Clases del alumno entre dos fechas, ordenadas por día y hora. Cada
     * elemento trae la Cita (guardada o calculada), su tipo y si ya existe
     * en la base de datos.
     *
     * @return Collection<int, array{cita: Cita, tipo: string, registrada: bool}>
     */
    public static function enRango(Alumno $alumno, Carbon $desde, Carbon $hasta): Collection
    {
        $desde = $desde->copy()->startOfDay();
        $hasta = $hasta->copy()->startOfDay();

        $registradas = $alumno->citas()
            ->with(self::RELACIONES_HORARIO)
            ->whereDate('fecha', '>=', $desde)
            ->whereDate('fecha', '<=', $hasta)
            ->get();

        $inscripciones = $alumno->inscripciones()
            ->where('activa', true)
            ->with(self::RELACIONES_HORARIO)
            ->get();

        return self::armar($alumno, $registradas, $inscripciones, $desde, $hasta);
    }

    /**
     * Combina las Citas guardadas con las fechas que le tocan al alumno
     * por sus inscripciones (las que no tienen Cita propia).
     *
     * @param  Collection<int, Cita>  $registradas
     * @param  Collection<int, Inscripcion>  $inscripciones
     * @return Collection<int, array{cita: Cita, tipo: string, registrada: bool}>
     */
    private static function armar(Alumno $alumno, Collection $registradas, Collection $inscripciones, Carbon $desde, Carbon $hasta): Collection
    {
        $horariosDeSuPlan = $inscripciones->pluck('horario_id')->all();

        $fechasConRegistro = $registradas
            ->mapWithKeys(fn (Cita $cita) => [self::clave($cita->horario_id, $cita->fecha) => true])
            ->all();

        $clases = $registradas
            ->reject(fn (Cita $cita) => in_array($cita->estado, self::ESTADOS_SIN_CLASE, true))
            ->map(fn (Cita $cita) => [
                'cita' => $cita,
                'tipo' => self::tipo($cita, $horariosDeSuPlan),
                'registrada' => true,
            ])
            ->values();

        foreach ($inscripciones as $inscripcion) {
            foreach (self::fechasDeLaInscripcion($inscripcion, $desde, $hasta) as $fecha) {
                if (isset($fechasConRegistro[self::clave($inscripcion->horario_id, $fecha)])) {
                    continue;
                }

                $clases->push([
                    'cita' => self::citaCalculada($alumno, $inscripcion->horario, $fecha),
                    'tipo' => self::TIPO_PLAN,
                    'registrada' => false,
                ]);
            }
        }

        return $clases
            ->sortBy(fn (array $clase) => $clase['cita']->fecha->format('Y-m-d').' '.$clase['cita']->hora_inicio)
            ->values();
    }

    /**
     * Clases que el alumno tiene por delante: las que aún no tienen
     * asistencia y las faltas avisadas de hoy en adelante (para poder
     * deshacer el aviso).
     *
     * @return Collection<int, array{cita: Cita, tipo: string, registrada: bool}>
     */
    public static function proximas(Alumno $alumno, int $dias = 28, ?int $limite = null): Collection
    {
        $clases = self::porDelante(self::enRango($alumno, today(), today()->addDays($dias)));

        return $limite ? $clases->take($limite)->values() : $clases;
    }

    /**
     * @param  Collection<int, array{cita: Cita, tipo: string, registrada: bool}>  $clases
     * @return Collection<int, array{cita: Cita, tipo: string, registrada: bool}>
     */
    private static function porDelante(Collection $clases): Collection
    {
        return $clases
            ->filter(fn (array $clase) => $clase['cita']->asistio === null || $clase['cita']->falta_avisada)
            ->values();
    }

    /**
     * Cuántas clases tiene el alumno en un periodo (las de su plan más las
     * de una sola fecha), sin contar canceladas ni las que se movieron.
     */
    public static function contarEnRango(Alumno $alumno, Carbon $desde, Carbon $hasta): int
    {
        return self::enRango($alumno, $desde, $hasta)->count();
    }

    /**
     * Clases por delante de todos los alumnos activos, para elegir cuál
     * reagendar desde el tablero de horarios.
     *
     * @return Collection<int, array{cita: Cita, tipo: string, registrada: bool}>
     */
    public static function proximasDeTodos(?int $sucursalId, int $dias = 14): Collection
    {
        $desde = today();
        $hasta = today()->addDays($dias);

        $alumnos = Alumno::query()
            ->where('estado', EstadoAlumno::Activo->value)
            ->when($sucursalId, fn ($query) => $query->where('sucursal_id', $sucursalId))
            ->orderBy('nombre')
            ->orderBy('apellidos')
            ->get();

        // Se carga todo de una vez y se reparte por alumno en memoria.
        $registradas = Cita::query()
            ->with(self::RELACIONES_HORARIO)
            ->whereIn('alumno_id', $alumnos->modelKeys())
            ->whereDate('fecha', '>=', $desde)
            ->whereDate('fecha', '<=', $hasta)
            ->get()
            ->groupBy('alumno_id');

        $inscripciones = Inscripcion::query()
            ->with(self::RELACIONES_HORARIO)
            ->whereIn('alumno_id', $alumnos->modelKeys())
            ->where('activa', true)
            ->get()
            ->groupBy('alumno_id');

        return $alumnos
            ->flatMap(function (Alumno $alumno) use ($registradas, $inscripciones, $desde, $hasta) {
                $clases = self::armar(
                    $alumno,
                    $registradas->get($alumno->id, collect()),
                    $inscripciones->get($alumno->id, collect()),
                    $desde,
                    $hasta,
                );

                return self::porDelante($clases)
                    ->reject(fn (array $clase) => $clase['cita']->falta_avisada)
                    ->each(fn (array $clase) => $clase['cita']->setRelation('alumno', $alumno));
            })
            ->values();
    }

    /**
     * Ubica la clase del alumno en ese grupo y fecha: la Cita guardada si
     * existe, o una nueva (sin guardar) si ese día le toca clase por su
     * inscripción. Devuelve null si ese día no tiene clase en ese grupo.
     */
    public static function localizar(Alumno $alumno, Horario $horario, Carbon $fecha): ?Cita
    {
        $registrada = Cita::where('alumno_id', $alumno->id)
            ->where('horario_id', $horario->id)
            ->whereDate('fecha', $fecha)
            ->first();

        if ($registrada) {
            return in_array($registrada->estado, self::ESTADOS_SIN_CLASE, true) ? null : $registrada;
        }

        $inscripcion = Inscripcion::where('alumno_id', $alumno->id)
            ->where('horario_id', $horario->id)
            ->where('activa', true)
            ->first();

        if (! $inscripcion || ! $horario->activo || $fecha->isoWeekday() !== $horario->dia_semana->value) {
            return null;
        }

        return self::citaCalculada($alumno, $horario, $fecha);
    }

    /**
     * Mueve una clase a otro grupo o fecha. Devuelve [campo, motivo] si no
     * se puede, o null si quedó reagendada.
     *
     * Si la clase es de su plan, la fecha original se conserva marcada como
     * "reagendada" (para que no vuelva a aparecer como clase normal) y se
     * crea la clase nueva de una sola fecha. Si ya era una clase de una
     * sola fecha, simplemente se mueve.
     *
     * @return array{0: string, 1: string}|null
     */
    public static function reagendar(Cita $clase, Horario $nuevoHorario, Carbon $nuevaFecha, User $quien): ?array
    {
        $alumno = $clase->alumno;
        $nuevaFecha = $nuevaFecha->copy()->startOfDay();

        if ($clase->asistio !== null) {
            return ['fecha', 'Esa clase ya tiene asistencia o falta registrada y no se puede reagendar.'];
        }

        if ($nuevoHorario->sucursal_id !== $clase->sucursal_id) {
            return ['horario_id', 'El nuevo horario debe pertenecer a la misma sucursal que la clase.'];
        }

        if ($nuevaFecha->isoWeekday() !== $nuevoHorario->dia_semana->value) {
            return ['fecha', "La fecha elegida no coincide con el día ({$nuevoHorario->dia_semana->label()}) del horario seleccionado."];
        }

        if ($nuevoHorario->id === $clase->horario_id && $nuevaFecha->isSameDay($clase->fecha)) {
            return ['fecha', 'Elige un grupo o una fecha distintos a los que la clase ya tiene.'];
        }

        $enDestino = Cita::where('alumno_id', $alumno->id)
            ->where('horario_id', $nuevoHorario->id)
            ->whereDate('fecha', $nuevaFecha)
            ->first();

        $inscritoEnDestino = self::estaInscrito($alumno, $nuevoHorario->id);

        if ($enDestino && ! in_array($enDestino->estado, self::ESTADOS_SIN_CLASE, true)) {
            return ['horario_id', 'El alumno ya tiene una clase en ese grupo para esa fecha.'];
        }

        if (! $enDestino && $inscritoEnDestino) {
            return ['horario_id', 'El alumno ya tiene clase ese día en ese grupo porque está inscrito en él.'];
        }

        $esDeSuPlan = self::estaInscrito($alumno, $clase->horario_id) && ! $clase->es_extra && ! $clase->reposicion_de_id;

        // El destino es una fecha de su plan que antes se había movido: la
        // clase regresa a su lugar original y basta con quitar la marca.
        $regresaASuLugar = $enDestino?->estado === EstadoCita::Reagendada && $inscritoEnDestino;

        $enDestino?->delete();

        if ($esDeSuPlan) {
            $origen = "{$clase->fecha->format('d/m/Y')} en {$clase->horario?->nombre_grupo}";

            $clase->fill([
                'estado' => EstadoCita::Reagendada->value,
                'registrado_por' => $quien->id,
            ])->save();

            if ($regresaASuLugar) {
                return null;
            }

            Cita::create([
                'horario_id' => $nuevoHorario->id,
                'alumno_id' => $alumno->id,
                'sucursal_id' => $nuevoHorario->sucursal_id,
                'fecha' => $nuevaFecha->toDateString(),
                'hora_inicio' => $nuevoHorario->hora_inicio,
                'hora_fin' => $nuevoHorario->hora_fin,
                'estado' => EstadoCita::Programada->value,
                'notas' => "Reagendada: era el {$origen}.",
                'registrado_por' => $quien->id,
            ]);

            return null;
        }

        if ($regresaASuLugar && ! $clase->es_extra && ! $clase->reposicion_de_id) {
            $clase->delete();

            return null;
        }

        $clase->update([
            'horario_id' => $nuevoHorario->id,
            'fecha' => $nuevaFecha->toDateString(),
            'hora_inicio' => $nuevoHorario->hora_inicio,
            'hora_fin' => $nuevoHorario->hora_fin,
        ]);

        return null;
    }

    /**
     * Quita las clases futuras apartadas en un grupo cuando el alumno deja
     * de estar inscrito en él. Solo borra lo que nunca se usó; la
     * asistencia, las faltas y las clases de una sola fecha se conservan.
     */
    public static function liberarClasesFuturas(int $alumnoId, int $horarioId): void
    {
        Cita::where('alumno_id', $alumnoId)
            ->where('horario_id', $horarioId)
            ->whereDate('fecha', '>=', today())
            ->whereNull('asistio')
            ->where('es_extra', false)
            ->whereNull('reposicion_de_id')
            ->delete();
    }

    /**
     * Clase del alumno que ese día se cruza en horario con el grupo dado (no
     * se puede estar en dos clases a la vez). No cuentan las faltas: si no
     * va a esa clase, la hora está libre.
     */
    public static function claseQueSeEmpalma(Alumno $alumno, Horario $horario, Carbon $fecha): ?Cita
    {
        $inicio = substr((string) $horario->hora_inicio, 0, 5);
        $fin = substr((string) $horario->hora_fin, 0, 5);

        return self::enRango($alumno, $fecha, $fecha)
            ->map(fn (array $clase) => $clase['cita'])
            ->first(fn (Cita $cita) => $cita->horario_id !== $horario->id
                && $cita->asistio !== false
                && substr((string) $cita->hora_inicio, 0, 5) < $fin
                && $inicio < substr((string) $cita->hora_fin, 0, 5));
    }

    /**
     * Marca falta a quienes tenían clase ese día y no registraron asistencia
     * (no escanearon su QR ni el instructor la marcó). Solo para clases que
     * ya terminaron. Devuelve cuántas faltas marcó.
     */
    public static function marcarFaltasSinRegistro(Carbon $fecha): int
    {
        $fecha = $fecha->copy()->startOfDay();

        if ($fecha->lt(Carbon::parse(self::INICIO_FALTAS_AUTOMATICAS)) || $fecha->gt(today())) {
            return 0;
        }

        $horarios = Horario::query()
            ->where('activo', true)
            ->where('dia_semana', $fecha->isoWeekday())
            ->get()
            ->filter(fn (Horario $horario) => $fecha->lt(today())
                || $fecha->copy()->setTimeFromTimeString($horario->hora_fin)->addMinutes(self::MINUTOS_DE_GRACIA)->lte(now()));

        $marcadas = 0;

        foreach ($horarios as $horario) {
            $registradas = Cita::where('horario_id', $horario->id)->whereDate('fecha', $fecha)->get()->keyBy('alumno_id');

            // Clases ya guardadas que quedaron sin asistencia (de una sola
            // fecha o de su grupo): pasan a falta.
            foreach ($registradas as $cita) {
                if ($cita->asistio === null && in_array($cita->estado, [EstadoCita::Programada, EstadoCita::Confirmada], true)) {
                    $cita->update(self::datosDeFaltaAutomatica());
                    $marcadas++;
                }
            }

            $inscritos = Inscripcion::where('horario_id', $horario->id)
                ->where('activa', true)
                ->where(fn ($query) => $query->whereNull('fecha_inicio')->orWhereDate('fecha_inicio', '<=', $fecha))
                ->whereHas('alumno', fn ($query) => $query->where('estado', EstadoAlumno::Activo->value))
                ->pluck('alumno_id');

            foreach ($inscritos as $alumnoId) {
                if ($registradas->has($alumnoId)) {
                    continue;
                }

                Cita::create([
                    'horario_id' => $horario->id,
                    'alumno_id' => $alumnoId,
                    'sucursal_id' => $horario->sucursal_id,
                    'fecha' => $fecha->toDateString(),
                    'hora_inicio' => $horario->hora_inicio,
                    'hora_fin' => $horario->hora_fin,
                    ...self::datosDeFaltaAutomatica(),
                ]);
                $marcadas++;
            }
        }

        return $marcadas;
    }

    /**
     * Registra a mano la asistencia o la falta de una clase de hoy o de un
     * día anterior (también corrige una falta automática). Devuelve
     * [campo, motivo] si no se puede, o null si quedó registrada.
     *
     * @return array{0: string, 1: string}|null
     */
    public static function registrarAsistencia(Alumno $alumno, Horario $horario, Carbon $fecha, bool $asistio, User $quien): ?array
    {
        $fecha = $fecha->copy()->startOfDay();

        if ($horario->sucursal_id !== $alumno->sucursal_id) {
            return ['horario_id', 'Ese grupo no pertenece a la sucursal del alumno.'];
        }

        if ($fecha->isoWeekday() !== $horario->dia_semana->value) {
            return ['fecha', "Ese grupo da clase los {$horario->dia_semana->label()}: elige una fecha de ese día."];
        }

        $cita = Cita::where('alumno_id', $alumno->id)
            ->where('horario_id', $horario->id)
            ->whereDate('fecha', $fecha)
            ->first();

        if ($cita && in_array($cita->estado, self::ESTADOS_SIN_CLASE, true)) {
            return ['fecha', 'Esa clase se canceló o se reagendó a otra fecha.'];
        }

        if (! $cita) {
            $estabaInscrito = Inscripcion::where('alumno_id', $alumno->id)
                ->where('horario_id', $horario->id)
                ->where(fn ($query) => $query->whereNull('fecha_inicio')->orWhereDate('fecha_inicio', '<=', $fecha))
                ->where(fn ($query) => $query->where('activa', true)->orWhereDate('fecha_fin', '>=', $fecha))
                ->exists();

            if (! $estabaInscrito) {
                return ['fecha', 'El alumno no tenía clase en ese grupo ese día.'];
            }

            $cita = self::citaCalculada($alumno, $horario, $fecha);
        }

        if ($asistio && $cita->exists && $cita->reposicion()->exists()) {
            return ['fecha', 'Esa falta ya tiene una reposición programada; no se puede cambiar a asistencia.'];
        }

        $cita->fill([
            'asistio' => $asistio,
            'falta_avisada' => false,
            'estado' => EstadoCita::Completada->value,
            'notas' => $asistio ? null : 'Falta registrada por la administración.',
            'registrado_por' => $quien->id,
        ])->save();

        // Si en realidad sí asistió, ya no hay nada que reponer.
        if ($asistio) {
            Reposiciones::cerrarSolicitudesPendientes($cita->id, $quien->id);
        }

        return null;
    }

    /**
     * @return array<string, mixed>
     */
    private static function datosDeFaltaAutomatica(): array
    {
        return [
            'asistio' => false,
            'falta_avisada' => false,
            'estado' => EstadoCita::Completada->value,
            'notas' => 'Sin registro de asistencia: falta automática.',
        ];
    }

    public static function estaInscrito(Alumno $alumno, int $horarioId): bool
    {
        return Inscripcion::where('alumno_id', $alumno->id)
            ->where('horario_id', $horarioId)
            ->where('activa', true)
            ->exists();
    }

    /**
     * @param  list<int>  $horariosDeSuPlan
     */
    private static function tipo(Cita $cita, array $horariosDeSuPlan): string
    {
        return match (true) {
            $cita->es_extra => self::TIPO_EXTRA,
            $cita->reposicion_de_id !== null => self::TIPO_REPOSICION,
            in_array($cita->horario_id, $horariosDeSuPlan, true) => self::TIPO_PLAN,
            default => self::TIPO_REAGENDADA,
        };
    }

    /**
     * Días en que la inscripción da clase dentro del rango.
     *
     * @return list<Carbon>
     */
    private static function fechasDeLaInscripcion(Inscripcion $inscripcion, Carbon $desde, Carbon $hasta): array
    {
        $horario = $inscripcion->horario;

        if (! $horario?->activo) {
            return [];
        }

        $fecha = $desde->copy();

        if ($inscripcion->fecha_inicio && $inscripcion->fecha_inicio->gt($fecha)) {
            $fecha = $inscripcion->fecha_inicio->copy()->startOfDay();
        }

        $fecha->addDays(($horario->dia_semana->value - $fecha->isoWeekday() + 7) % 7);

        $fechas = [];

        for (; $fecha->lte($hasta); $fecha = $fecha->copy()->addWeek()) {
            if ($inscripcion->fecha_fin && $fecha->gt($inscripcion->fecha_fin)) {
                break;
            }

            $fechas[] = $fecha->copy();
        }

        return $fechas;
    }

    private static function citaCalculada(Alumno $alumno, Horario $horario, Carbon $fecha): Cita
    {
        $cita = new Cita([
            'horario_id' => $horario->id,
            'alumno_id' => $alumno->id,
            'sucursal_id' => $horario->sucursal_id,
            'fecha' => $fecha->toDateString(),
            'hora_inicio' => $horario->hora_inicio,
            'hora_fin' => $horario->hora_fin,
            'estado' => EstadoCita::Programada,
        ]);

        $cita->setRelation('horario', $horario);
        $cita->setRelation('alumno', $alumno);

        return $cita;
    }

    private static function clave(int $horarioId, Carbon $fecha): string
    {
        return $horarioId.'|'.$fecha->format('Y-m-d');
    }
}
