<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Un alumno no debe tener dos inscripciones ACTIVAS al mismo horario. Esa
     * regla ya se valida en la capa de aplicación, pero bajo concurrencia
     * (dos solicitudes casi simultáneas) podría insertarse un duplicado. Esta
     * columna generada es NULL cuando la inscripción no está activa, y ni
     * MySQL ni SQLite consideran que dos NULL choquen en un índice único: así
     * el índice solo restringe los pares (alumno, horario) que sí están
     * activos.
     *
     * Usa CASE WHEN (no IF(), que es sintaxis exclusiva de MySQL) para que la
     * expresión sea válida tanto en MySQL como en SQLite. También es segura de
     * volver a correr: si un intento anterior dejó la columna o el índice a
     * medias, los quita primero antes de recrearlos.
     */
    public function up(): void
    {
        if (Schema::hasIndex('inscripciones', 'inscripciones_alumno_horario_activo_unique')) {
            Schema::table('inscripciones', function (Blueprint $table) {
                $table->dropUnique('inscripciones_alumno_horario_activo_unique');
            });
        }

        if (Schema::hasColumn('inscripciones', 'horario_id_si_activa')) {
            Schema::table('inscripciones', function (Blueprint $table) {
                $table->dropColumn('horario_id_si_activa');
            });
        }

        Schema::table('inscripciones', function (Blueprint $table) {
            $table->unsignedBigInteger('horario_id_si_activa')
                ->virtualAs('CASE WHEN activa = 1 THEN horario_id ELSE NULL END')
                ->nullable()
                ->after('activa');
        });

        Schema::table('inscripciones', function (Blueprint $table) {
            $table->unique(['alumno_id', 'horario_id_si_activa'], 'inscripciones_alumno_horario_activo_unique');
        });
    }

    public function down(): void
    {
        Schema::table('inscripciones', function (Blueprint $table) {
            $table->dropUnique('inscripciones_alumno_horario_activo_unique');
            $table->dropColumn('horario_id_si_activa');
        });
    }
};
