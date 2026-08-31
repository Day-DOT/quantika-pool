<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('alumnos', function (Blueprint $table) {
            $table->string('qr_token', 40)->nullable()->unique()->after('fecha_inscripcion');
        });

        // Los alumnos que ya existían antes de este módulo no tienen token
        // todavía: se los generamos aquí para que su código QR funcione de
        // inmediato sin esperar a que se guarden de nuevo. Se usa el query
        // builder (no el modelo Eloquent) para no acoplar la migración al
        // estado futuro del modelo (scopes, casts, observers).
        DB::table('alumnos')->whereNull('qr_token')->select('id')->orderBy('id')
            ->chunkById(200, function ($alumnos) {
                foreach ($alumnos as $alumno) {
                    DB::table('alumnos')->where('id', $alumno->id)->update(['qr_token' => Str::random(40)]);
                }
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('alumnos', function (Blueprint $table) {
            $table->dropColumn('qr_token');
        });
    }
};
