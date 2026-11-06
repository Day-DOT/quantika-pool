<?php

namespace App\Support;

use App\Enums\ConceptoPago;
use App\Enums\EstadoAlumno;
use App\Enums\EstadoPago;
use App\Models\Alumno;
use App\Models\Pago;

/**
 * La anualidad es el pago con concepto "Inscripción". Cubre un año a partir
 * de su fecha (la de vencimiento o, si no tiene, la de pago); al pagarse se
 * agenda la del año siguiente por el mismo monto, como las mensualidades.
 */
class Anualidades
{
    /**
     * Agenda como pendiente la anualidad del año siguiente a una que ya se
     * pagó, si el alumno sigue activo y aún no tiene una posterior. Devuelve
     * la nueva o null si no hizo falta.
     */
    public static function agendarSiguiente(Pago $pagada): ?Pago
    {
        $base = $pagada->fechaCalendario();

        if (! $pagada->esAnualidad()
            || $pagada->estado !== EstadoPago::Pagado
            || ! $base
            || $pagada->alumno?->estado !== EstadoAlumno::Activo) {
            return null;
        }

        $yaHayPosterior = self::delAlumno($pagada->alumno)
            ->contains(fn (Pago $pago) => $pago->id !== $pagada->id && $pago->fechaCalendario()?->gt($base));

        if ($yaHayPosterior) {
            return null;
        }

        $siguiente = $base->copy()->addYearNoOverflow();

        return Pago::create([
            'alumno_id' => $pagada->alumno_id,
            'sucursal_id' => $pagada->sucursal_id,
            'concepto' => ConceptoPago::Inscripcion->value,
            'periodo' => $siguiente->format('Y-m'),
            'monto' => $pagada->monto,
            'fecha_vencimiento' => $siguiente->toDateString(),
            'estado' => EstadoPago::Pendiente->value,
        ]);
    }

    /**
     * Para cada alumno activo cuya anualidad más reciente ya está pagada,
     * agenda la del año siguiente. Lo usa la tarea diaria, para que también
     * la tengan quienes la pagaron antes de que existiera esta regla.
     */
    public static function agendarPendientes(): int
    {
        $agendadas = 0;

        Alumno::query()
            ->where('estado', EstadoAlumno::Activo->value)
            ->whereHas('pagos', fn ($query) => $query->where('concepto', ConceptoPago::Inscripcion->value))
            ->chunkById(100, function ($alumnos) use (&$agendadas) {
                foreach ($alumnos as $alumno) {
                    $ultima = self::delAlumno($alumno)->last();

                    if ($ultima && self::agendarSiguiente($ultima->setRelation('alumno', $alumno))) {
                        $agendadas++;
                    }
                }
            });

        return $agendadas;
    }

    /**
     * Anualidad vencida y sin pagar del alumno (la más antigua), o null.
     */
    public static function vencida(Alumno $alumno): ?Pago
    {
        return self::delAlumno($alumno)->first(fn (Pago $pago) => $pago->estaVencido());
    }

    /**
     * Anualidades del alumno, de la más antigua a la más reciente.
     *
     * @return \Illuminate\Support\Collection<int, Pago>
     */
    private static function delAlumno(Alumno $alumno)
    {
        return $alumno->pagos()
            ->where('concepto', ConceptoPago::Inscripcion->value)
            ->get()
            ->filter(fn (Pago $pago) => $pago->fechaCalendario() !== null)
            ->sortBy(fn (Pago $pago) => $pago->fechaCalendario()->toDateString())
            ->values();
    }
}
