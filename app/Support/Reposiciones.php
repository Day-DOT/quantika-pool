<?php

namespace App\Support;

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

    public static function admiteReposicion(Alumno $alumno): bool
    {
        return $alumno->nivel?->categoria_edad !== self::CATEGORIA_SIN_REPOSICION;
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
        $categoriaGrupo = $horario->nivel?->categoria_edad;
        $categoriaAlumno = $alumno->nivel?->categoria_edad;

        return $categoriaGrupo === null || $categoriaAlumno === null || $categoriaGrupo === $categoriaAlumno;
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
            return ['horario_id', 'El horario debe pertenecer a la misma categoría de edad del alumno.'];
        }

        if ($fecha->isoWeekday() !== $horario->dia_semana->value) {
            return ['fecha', "La fecha elegida no coincide con el día ({$horario->dia_semana->label()}) del horario seleccionado."];
        }

        if (Cita::where('horario_id', $horario->id)->where('alumno_id', $alumno->id)->whereDate('fecha', $fecha)->exists()) {
            return ['horario_id', 'El alumno ya tiene una clase registrada en ese grupo para esa fecha.'];
        }

        if (Inscripcion::where('horario_id', $horario->id)->where('alumno_id', $alumno->id)->where('activa', true)->exists()) {
            return ['horario_id', 'El alumno ya está inscrito en ese grupo: esa clase ya forma parte de su plan.'];
        }

        if (self::cupoDisponible($horario, $fecha) <= 0) {
            return ['horario_id', 'Ese horario ya no tiene cupo disponible para esa fecha.'];
        }

        return null;
    }

    /**
     * Crea la clase de reposición. Quien llama debe haber validado antes
     * con motivoDeRechazo().
     */
    public static function programar(Cita $falta, Horario $horario, Carbon $fecha, ?int $registradoPor = null): Cita
    {
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
            ->filter(function (Horario $horario) use ($alumno, $categoriaAlumno) {
                if ($horario->inscripciones->contains('alumno_id', $alumno->id)) {
                    return false;
                }

                if ($horario->nivel || $categoriaAlumno === null) {
                    return self::grupoCompatible($horario, $alumno);
                }

                $categoriasDelGrupo = $horario->inscripciones
                    ->map(fn (Inscripcion $inscripcion) => $inscripcion->alumno?->nivel?->categoria_edad)
                    ->filter()
                    ->unique();

                return $categoriasDelGrupo->isEmpty() || $categoriasDelGrupo->contains($categoriaAlumno);
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
