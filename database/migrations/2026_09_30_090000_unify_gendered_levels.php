<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $niveles = DB::table('niveles')->orderBy('id')->get();
        $grupos = [];

        foreach ($niveles as $nivel) {
            $categoriaEdad = match ($nivel->categoria_edad) {
                'Bebés', 'Niños', 'Niñas' => 'Niños',
                'Adultos', 'Adultos mujeres', 'Adultos hombres' => 'Adultos',
                default => $nivel->categoria_edad,
            };

            if ($categoriaEdad !== $nivel->categoria_edad) {
                DB::table('niveles')->where('id', $nivel->id)->update(['categoria_edad' => $categoriaEdad]);
            }

            $clave = implode('|', [
                $categoriaEdad,
                $nivel->orden,
                mb_strtolower(trim($nivel->nombre)),
            ]);
            $grupos[$clave][] = (int) $nivel->id;
        }

        foreach ($grupos as $ids) {
            if (count($ids) < 2) {
                continue;
            }

            $nivelPrincipalId = array_shift($ids);

            foreach ($ids as $nivelDuplicadoId) {
                $this->unificarNivel($nivelPrincipalId, $nivelDuplicadoId);
            }
        }
    }

    public function down(): void
    {
        // Consolidated records cannot be split back into sex-specific copies without losing history.
    }

    private function unificarNivel(int $nivelPrincipalId, int $nivelDuplicadoId): void
    {
        foreach (['alumnos', 'horarios', 'evaluaciones', 'alumno_nivel_historial'] as $tabla) {
            DB::table($tabla)
                ->where('nivel_id', $nivelDuplicadoId)
                ->update(['nivel_id' => $nivelPrincipalId]);
        }

        $criteriosPrincipales = DB::table('criterios_evaluacion')
            ->where('nivel_id', $nivelPrincipalId)
            ->get();
        $criteriosDuplicados = DB::table('criterios_evaluacion')
            ->where('nivel_id', $nivelDuplicadoId)
            ->get();

        foreach ($criteriosDuplicados as $criterioDuplicado) {
            $criterioPrincipal = $criteriosPrincipales->first(
                fn ($criterio) => $criterio->orden === $criterioDuplicado->orden
                    && mb_strtolower(trim($criterio->nombre)) === mb_strtolower(trim($criterioDuplicado->nombre))
            );

            if (! $criterioPrincipal) {
                DB::table('criterios_evaluacion')
                    ->where('id', $criterioDuplicado->id)
                    ->update(['nivel_id' => $nivelPrincipalId]);
                $criteriosPrincipales->push((object) [
                    ...(array) $criterioDuplicado,
                    'nivel_id' => $nivelPrincipalId,
                ]);

                continue;
            }

            DB::table('criterios_evaluacion')
                ->where('id', $criterioPrincipal->id)
                ->update([
                    'activo' => $criterioPrincipal->activo || $criterioDuplicado->activo,
                    'descripcion' => $criterioPrincipal->descripcion ?: $criterioDuplicado->descripcion,
                ]);

            $detallesDuplicados = DB::table('evaluacion_detalles')
                ->where('criterio_evaluacion_id', $criterioDuplicado->id)
                ->get();

            foreach ($detallesDuplicados as $detalleDuplicado) {
                $detallePrincipal = DB::table('evaluacion_detalles')
                    ->where('evaluacion_id', $detalleDuplicado->evaluacion_id)
                    ->where('criterio_evaluacion_id', $criterioPrincipal->id)
                    ->first();

                if (! $detallePrincipal) {
                    DB::table('evaluacion_detalles')
                        ->where('id', $detalleDuplicado->id)
                        ->update(['criterio_evaluacion_id' => $criterioPrincipal->id]);

                    continue;
                }

                $estados = ['no_iniciado' => 0, 'en_proceso' => 1, 'logrado' => 2];
                $estado = ($estados[$detalleDuplicado->estado] ?? 0) > ($estados[$detallePrincipal->estado] ?? 0)
                    ? $detalleDuplicado->estado
                    : $detallePrincipal->estado;
                $observaciones = collect([$detallePrincipal->observaciones, $detalleDuplicado->observaciones])
                    ->filter()
                    ->unique()
                    ->implode("\n");

                DB::table('evaluacion_detalles')
                    ->where('id', $detallePrincipal->id)
                    ->update([
                        'estado' => $estado,
                        'observaciones' => $observaciones ?: null,
                    ]);
                DB::table('evaluacion_detalles')->where('id', $detalleDuplicado->id)->delete();
            }

            DB::table('criterios_evaluacion')->where('id', $criterioDuplicado->id)->delete();
        }

        DB::table('niveles')->where('id', $nivelDuplicadoId)->delete();
    }
};
