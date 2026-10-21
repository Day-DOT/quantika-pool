<?php

namespace App\Support;

use App\Enums\EstadoAlumno;
use App\Enums\EstadoCita;
use App\Models\Alumno;
use App\Models\Cita;
use App\Models\Horario;
use App\Models\Inscripcion;
use App\Models\SolicitudReposicion;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * Reglas de la reposición de clases, compartidas por la administración y
 * por el portal de tutores para que ambos caminos se comporten igual.
 *
 * Una reposición es una clase de una sola fecha que sustituye a una falta:
 * el alumno no queda inscrito en el grupo donde repone.
 */
class Reposiciones
{
    public const MAXIMO_POR_MES = 2;

    private const CATEGORIA_SIN_REPOSICION = 'Bebés';

    /** Estados en los que la clase ya no ocurre: no bloquean esa fecha. */
    private const ESTADOS_SIN_CLASE = ['cancelada', 'reagendada'];

    /**
     * Faltas del mes en curso que todavía no tienen reposición programada.
     */
    public static function faltasPorReponer(Alumno $alumno): Builder
    {
        return Cita::query()
            ->where('alumno_id', $alumno->id)
            ->where('asistio', false)
            ->whereDoesntHave('reposicion')
            ->whereMonth('fecha', now()->month)
            ->whereYear('fecha', now()->year);
    }

    /**
     * Los bebés no reponen clases; solo niños y adultos. Se considera bebé
     * a quien tiene un nivel de bebés, tiene 3 años o menos, o está inscrito
     * en un grupo de bebés (hay bebés con un nivel de niños asignado).
     */
    public static function admiteReposicion(Alumno $alumno): bool
    {
        if ($alumno->nivel?->categoria_edad === self::CATEGORIA_SIN_REPOSICION
            || $alumno->grupoEdad() === self::CATEGORIA_SIN_REPOSICION) {
            return false;
        }

        return ! $alumno->inscripciones()
            ->where('activa', true)
            ->whereHas('horario', fn ($horario) => self::soloGruposDeBebes($horario))
            ->exists();
    }

    /**
     * Grupos de bebés: por su nivel o, si no tienen, por su nombre.
     */
    private static function soloGruposDeBebes($horario)
    {
        return $horario->where(fn ($query) => $query
            ->whereHas('nivel', fn ($nivel) => $nivel->where('categoria_edad', self::CATEGORIA_SIN_REPOSICION))
            ->orWhere('nombre_grupo', 'like', 'Beb%'));
    }

    /**
     * Reposiciones que el alumno ya tiene programadas en el mes de la fecha
     * dada (cuentan para el máximo mensual).
     */
    public static function usadasEnElMes(Alumno $alumno, Carbon $mes): int
    {
        return Cita::where('alumno_id', $alumno->id)
            ->whereNotNull('reposicion_de_id')
            ->where('estado', '!=', EstadoCita::Cancelada->value)
            ->whereMonth('fecha', $mes->month)
            ->whereYear('fecha', $mes->year)
            ->count();
    }

    /**
     * Un grupo sirve para reponer si es de la misma categoría de edad del
     * alumno. Los grupos sin nivel asignado no se descartan: su categoría
     * no se conoce, así que la decisión queda en quien programa la clase.
     */
    public static function grupoCompatible(Horario $horario, Alumno $alumno): bool
    {
        // Sin nivel asignado no hay con qué comparar: lo decide la escuela.
        if (! $alumno->nivel_id) {
            return true;
        }

        if ($horario->nivel_id) {
            return $horario->nivel_id === $alumno->nivel_id;
        }

        // Casi ningún grupo tiene nivel asignado: su nivel es el de los
        // alumnos inscritos. Se repone solo en grupos donde hay alumnos de
        // su mismo nivel.
        return self::nivelesDelGrupo($horario)->contains($alumno->nivel_id);
    }

    /**
     * Niveles de los alumnos activos inscritos en el grupo.
     *
     * @return Collection<int, int>
     */
    private static function nivelesDelGrupo(Horario $horario): Collection
    {
        $inscripciones = $horario->relationLoaded('inscripciones')
            ? $horario->inscripciones->where('activa', true)
            : $horario->inscripciones()->where('activa', true)->with('alumno')->get();

        return $inscripciones
            ->map(fn (Inscripcion $inscripcion) => $inscripcion->alumno)
            ->filter(fn (?Alumno $alumno) => $alumno?->estado === EstadoAlumno::Activo)
            ->pluck('nivel_id')
            ->filter()
            ->unique()
            ->values();
    }

    /**
     * Lugares libres de un grupo en una fecha: descuenta a los inscritos y
     * a quienes van solo ese día (reposiciones y clases extra).
     */
    public static function cupoDisponible(Horario $horario, Carbon $fecha): int
    {
        $inscritos = Inscripcion::where('horario_id', $horario->id)->where('activa', true)->count();
        $visitantes = Cita::visitantesDelDia($horario->id, $fecha)->count();

        return max(0, $horario->capacidad_maxima - $inscritos - $visitantes);
    }

    /**
     * Revisa todas las reglas y devuelve [campo, mensaje] con el primer
     * motivo por el que la falta no se puede reponer en ese grupo y fecha,
     * o null si la reposición es válida.
     *
     * @return array{0: string, 1: string}|null
     */
    public static function motivoDeRechazo(Cita $falta, Horario $horario, Carbon $fecha): ?array
    {
        $alumno = $falta->alumno;

        if ($alumno->estado !== EstadoAlumno::Activo) {
            return ['fecha', "{$alumno->nombreCompleto()} no está activo: solo los alumnos activos pueden reponer clases."];
        }

        if ($falta->asistio !== false) {
            return ['fecha', 'Esa clase no está marcada como falta.'];
        }

        if ($falta->reposicion()->exists()) {
            return ['fecha', 'Esta falta ya tiene una reposición programada.'];
        }

        if (! self::admiteReposicion($alumno)) {
            return ['fecha', 'Las clases de bebés no tienen reposición.'];
        }

        if (! $fecha->isSameMonth($falta->fecha)) {
            return ['fecha', 'La reposición debe realizarse dentro del mismo mes calendario en que se produjo la falta.'];
        }

        if (self::usadasEnElMes($alumno, $falta->fecha) >= self::MAXIMO_POR_MES) {
            return ['fecha', "{$alumno->nombreCompleto()} ya alcanzó el máximo de ".self::MAXIMO_POR_MES.' reposiciones este mes.'];
        }

        if (! $horario->activo || $horario->sucursal_id !== $falta->sucursal_id) {
            return ['horario_id', 'El horario debe estar activo y pertenecer a la misma sucursal.'];
        }

        if (! self::grupoCompatible($horario, $alumno)) {
            return ['horario_id', 'Solo se puede reponer en un grupo del mismo nivel del alumno.'];
        }

        if ($fecha->isoWeekday() !== $horario->dia_semana->value) {
            return ['fecha', "La fecha elegida no coincide con el día ({$horario->dia_semana->label()}) del horario seleccionado."];
        }

        if (self::registroEnEsaFecha($alumno->id, $horario, $fecha)->whereNotIn('estado', self::ESTADOS_SIN_CLASE)->exists()) {
            return ['horario_id', 'El alumno ya tiene una clase registrada en ese grupo para esa fecha.'];
        }

        if (Inscripcion::where('horario_id', $horario->id)->where('alumno_id', $alumno->id)->where('activa', true)->exists()) {
            return ['horario_id', 'El alumno ya está inscrito en ese grupo: esa clase ya forma parte de su plan.'];
        }

        if ($empalme = ClasesDelAlumno::claseQueSeEmpalma($alumno, $horario, $fecha)) {
            return ['horario_id', "Ese día el alumno ya tiene clase a esa hora ({$empalme->horario?->nombre_grupo}, ".substr((string) $empalme->hora_inicio, 0, 5).').'];
        }

        if (self::cupoDisponible($horario, $fecha) <= 0) {
            return ['horario_id', 'Ese horario ya no tiene cupo disponible para esa fecha.'];
        }

        return null;
    }

    /**
     * Cierra las solicitudes del portal que siguen en espera para esas
     * faltas (por ejemplo, porque la escuela ya programó la reposición o el
     * alumno se dio de baja).
     *
     * @param  list<int>|int  $faltasIds
     */
    public static function cerrarSolicitudesPendientes(array|int $faltasIds, ?int $resueltaPor = null): void
    {
        SolicitudReposicion::pendientes()
            ->whereIn('cita_id', (array) $faltasIds)
            ->update([
                'estado' => \App\Enums\EstadoInscripcion::Rechazada->value,
                'resuelta_por' => $resueltaPor,
                'resuelta_en' => now(),
            ]);
    }

    /**
     * Crea la clase de reposición. Quien llama debe haber validado antes
     * con motivoDeRechazo().
     */
    public static function programar(Cita $falta, Horario $horario, Carbon $fecha, ?int $registradoPor = null): Cita
    {
        // Una clase cancelada en esa fecha ya no ocupa el lugar: se reemplaza.
        self::registroEnEsaFecha($falta->alumno_id, $horario, $fecha)->whereIn('estado', self::ESTADOS_SIN_CLASE)->delete();

        return Cita::create([
            'horario_id' => $horario->id,
            'alumno_id' => $falta->alumno_id,
            'sucursal_id' => $falta->sucursal_id,
            'fecha' => $fecha->toDateString(),
            'hora_inicio' => $horario->hora_inicio,
            'hora_fin' => $horario->hora_fin,
            'estado' => EstadoCita::Programada->value,
            'reposicion_de_id' => $falta->id,
            'registrado_por' => $registradoPor,
        ]);
    }

    private static function registroEnEsaFecha(int $alumnoId, Horario $horario, Carbon $fecha): Builder
    {
        return Cita::where('horario_id', $horario->id)->where('alumno_id', $alumnoId)->whereDate('fecha', $fecha);
    }

    /**
     * Grupos donde el alumno puede reponer en una fecha, con su cupo para
     * ese día. Se listan los de su sucursal que dan clase ese día de la
     * semana y son de su categoría de edad.
     *
     * Muchos grupos no tienen nivel asignado; para no mostrarle a un niño
     * grupos de adultos (ni al revés), en esos casos la categoría se deduce
     * de los alumnos ya inscritos. Un grupo sin nivel y sin alumnos se
     * muestra, porque no hay forma de saber a quién va dirigido.
     *
     * @return Collection<int, Horario>
     */
    public static function gruposParaReponer(Alumno $alumno, Carbon $fecha): Collection
    {
        $categoriaAlumno = $alumno->nivel?->categoria_edad;

        return Horario::query()
            ->with([
                'nivel',
                'instructor.user',
                'carril',
                'inscripciones' => fn ($query) => $query->where('activa', true)->with('alumno.nivel'),
            ])
            ->where('sucursal_id', $alumno->sucursal_id)
            ->where('activo', true)
            ->where('dia_semana', $fecha->isoWeekday())
            ->orderBy('hora_inicio')
            ->get()
            ->filter(function (Horario $horario) use ($alumno, $categoriaAlumno, $fecha) {
                if ($horario->inscripciones->contains('alumno_id', $alumno->id)) {
                    return false;
                }

                // No se ofrecen grupos a la misma hora que otra de sus clases.
                if (ClasesDelAlumno::claseQueSeEmpalma($alumno, $horario, $fecha)) {
                    return false;
                }

                return self::grupoCompatible($horario, $alumno);
            })
            ->each(function (Horario $horario) use ($fecha) {
                $horario->cupo_disponible = self::cupoDisponible($horario, $fecha);
            })
            ->values();
    }

    /**
     * Solicitudes del portal que siguen esperando respuesta.
     */
    public static function solicitudesPendientes(?int $sucursalId = null): Builder
    {
        return SolicitudReposicion::pendientes()
            ->when($sucursalId, fn ($query) => $query->whereHas('horario', fn ($h) => $h->where('sucursal_id', $sucursalId)));
    }
}
