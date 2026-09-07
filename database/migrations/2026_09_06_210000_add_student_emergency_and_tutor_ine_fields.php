<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('alumnos', function (Blueprint $table) {
            $table->string('tipo_sangre', 5)->nullable()->after('fecha_nacimiento');
            $table->string('contacto_emergencia_nombre')->nullable()->after('tipo_sangre');
            $table->string('contacto_emergencia_telefono', 20)->nullable()->after('contacto_emergencia_nombre');
            $table->text('observaciones_medicas')->nullable()->after('contacto_emergencia_telefono');
            $table->string('ine_tutor_path')->nullable()->after('identificacion_path');
        });
    }

    public function down(): void
    {
        Schema::table('alumnos', function (Blueprint $table) {
            $table->dropColumn([
                'tipo_sangre',
                'contacto_emergencia_nombre',
                'contacto_emergencia_telefono',
                'observaciones_medicas',
                'ine_tutor_path',
            ]);
        });
    }
};
