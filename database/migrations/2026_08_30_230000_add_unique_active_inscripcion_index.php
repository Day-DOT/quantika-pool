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
     * columna generada es NULL cuando la inscripción no está activa, y MySQL
     * no considera que dos NULL choquen en un índice único: así el índice
     * solo restringe los pares (alumno, horario) que sí están activos.
     */
    public function up(): void
    {
        Schema::table('inscripciones', function (Blueprint $table) {
            $table->unsignedBigInteger('horario_id_si_activa')
                ->virtualAs('IF(activa = 1, horario_id, NULL)')
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
