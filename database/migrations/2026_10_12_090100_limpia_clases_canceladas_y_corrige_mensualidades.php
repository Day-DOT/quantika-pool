<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $this->marcarClasesReagendadasAMano();
        $this->eliminarClasesCanceladasSinUsar();
        $this->eliminarLugaresApartados();
        $this->corregirMensualidadesGeneradasConElPrecioDelPlan();
    }

    public function down(): void
    {
        // Corrección de datos: no hay forma de reconstruir el estado anterior.
    }

    /**
     * Antes, reagendar movía el registro de la clase a su nueva fecha sin
     * dejar rastro de la original. Como ahora las clases se calculan desde
     * la inscripción, esa fecha original volvería a aparecer como clase
     * normal. Para las clases ya reagendadas que aún no ocurren se deja la
     * marca "reagendada" en la fecha de la que salieron.
     *
     * La fecha original se deduce así: al inscribir se apartaban las 4
     * clases siguientes del grupo; la que falta es la que se movió.
     */
    private function marcarClasesReagendadasAMano(): void
    {
        $hoy = Carbon::today();

        $movidas = DB::table('citas')
            ->whereIn('estado', ['programada', 'confirmada'])
            ->whereDate('fecha', '>=', $hoy->toDateString())
            ->whereNull('asistio')
            ->where('es_extra', false)
            ->whereNull('reposicion_de_id')
            ->whereColumn('created_at', '<>', 'updated_at')
            ->whereNotExists(fn ($inscrito) => $inscrito
                ->selectRaw('1')
                ->from('inscripciones')
                ->whereColumn('inscripciones.alumno_id', 'citas.alumno_id')
                ->whereColumn('inscripciones.horario_id', 'citas.horario_id')
                ->where('inscripciones.activa', true))
            ->orderBy('fecha')
            ->get();

        foreach ($movidas as $movida) {
            $creada = Carbon::parse($movida->created_at);

            // La clase nació al inscribir al alumno: misma hora de creación.
            $inscripcion = DB::table('inscripciones')
                ->join('horarios', 'horarios.id', '=', 'inscripciones.horario_id')
                ->where('inscripciones.alumno_id', $movida->alumno_id)
                ->where('inscripciones.activa', true)
                ->whereBetween('inscripciones.created_at', [$creada->copy()->subSeconds(2), $creada->copy()->addSeconds(2)])
                ->first(['horarios.id as horario_id', 'horarios.sucursal_id', 'horarios.dia_semana', 'horarios.hora_inicio', 'horarios.hora_fin', 'inscripciones.created_at']);

            if (! $inscripcion) {
                continue;
            }

            $fecha = Carbon::parse($inscripcion->created_at)->startOfDay();
            $fecha->addDays(((int) $inscripcion->dia_semana - $fecha->isoWeekday() + 7) % 7);

            for ($semana = 0; $semana < 4; $semana++, $fecha->addWeek()) {
                $yaTieneRegistro = DB::table('citas')
                    ->where('alumno_id', $movida->alumno_id)
                    ->where('horario_id', $inscripcion->horario_id)
                    ->whereDate('fecha', $fecha->toDateString())
                    ->exists();

                if ($fecha->lt($hoy) || $yaTieneRegistro) {
                    continue;
                }

                DB::table('citas')->insert([
                    'horario_id' => $inscripcion->horario_id,
                    'alumno_id' => $movida->alumno_id,
                    'sucursal_id' => $inscripcion->sucursal_id,
                    'fecha' => $fecha->toDateString(),
                    'hora_inicio' => $inscripcion->hora_inicio,
                    'hora_fin' => $inscripcion->hora_fin,
                    'estado' => 'reagendada',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                break;
            }
        }
    }

    /**
     * Al retirar a un alumno de un grupo (o darlo de baja) sus clases
     * futuras se marcaban como canceladas. Si después se le volvía a asignar
     * el mismo grupo, esas fechas seguían canceladas y sus clases no
     * aparecían. Eran solo lugares apartados que nunca se usaron: se
     * eliminan para que las clases vuelvan a calcularse desde la inscripción.
     */
    private function eliminarClasesCanceladasSinUsar(): void
    {
        DB::table('citas')
            ->where('estado', 'cancelada')
            ->whereDate('fecha', '>=', now()->toDateString())
            ->whereNull('asistio')
            ->where('es_extra', false)
            ->whereNull('reposicion_de_id')
            ->delete();
    }

    /**
     * Antes, al inscribir a un alumno se guardaban solo sus siguientes 4
     * clases; pasado ese mes dejaba de tener clases visibles. Ahora las
     * clases se calculan desde la inscripción, así que esos registros
     * (clases futuras de alumnos inscritos, todavía sin asistencia) ya no
     * hacen falta y podrían quedar desfasados si el grupo cambia de día.
     */
    private function eliminarLugaresApartados(): void
    {
        DB::table('citas')
            ->whereIn('estado', ['programada', 'confirmada'])
            ->whereDate('fecha', '>=', now()->toDateString())
            ->whereNull('asistio')
            ->where('es_extra', false)
            ->whereNull('reposicion_de_id')
            ->whereExists(fn ($inscrito) => $inscrito
                ->selectRaw('1')
                ->from('inscripciones')
                ->whereColumn('inscripciones.alumno_id', 'citas.alumno_id')
                ->whereColumn('inscripciones.horario_id', 'citas.horario_id')
                ->where('inscripciones.activa', true))
            ->delete();
    }

    /**
     * La siguiente mensualidad se generaba con el precio del plan aunque el
     * alumno pagara otro monto (por ejemplo, con descuento). Se corrigen
     * solo los casos inequívocos: la mensualidad pendiente la creó el
     * sistema, nadie la ha editado, vale exactamente el precio del plan y el
     * pago inmediato anterior fue menor (un descuento de hasta la mitad).
     * Lo capturado o editado a mano no se toca.
     */
    private function corregirMensualidadesGeneradasConElPrecioDelPlan(): void
    {
        $pendientes = DB::table('pagos')
            ->join('alumnos', 'alumnos.id', '=', 'pagos.alumno_id')
            ->join('planes', 'planes.id', '=', 'alumnos.plan_id')
            ->where('pagos.concepto', 'mensualidad')
            ->where('pagos.estado', 'pendiente')
            ->whereNull('pagos.registrado_por')
            ->whereColumn('pagos.created_at', 'pagos.updated_at')
            ->whereColumn('pagos.monto', 'planes.precio')
            ->get(['pagos.id', 'pagos.alumno_id', 'pagos.fecha_vencimiento', 'planes.precio']);

        foreach ($pendientes as $pendiente) {
            $pagadoAntes = DB::table('pagos')
                ->where('alumno_id', $pendiente->alumno_id)
                ->where('concepto', 'mensualidad')
                ->where('estado', 'pagado')
                ->whereDate('fecha_vencimiento', '<', $pendiente->fecha_vencimiento)
                ->orderByDesc('fecha_vencimiento')
                ->orderByDesc('id')
                ->value('monto');

            if ($pagadoAntes === null) {
                continue;
            }

            $precio = (float) $pendiente->precio;
            $pagado = (float) $pagadoAntes;

            if ($pagado < $precio && $pagado >= $precio * 0.5) {
                DB::table('pagos')->where('id', $pendiente->id)->update([
                    'monto' => $pagadoAntes,
                    'updated_at' => now(),
                ]);
            }
        }
    }
};
