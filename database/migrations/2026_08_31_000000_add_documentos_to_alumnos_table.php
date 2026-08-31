<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('alumnos', function (Blueprint $table) {
            $table->string('certificado_medico_path')->nullable()->after('observaciones');
            $table->string('identificacion_path')->nullable()->after('certificado_medico_path');
            $table->string('foto_path')->nullable()->after('identificacion_path');
        });
    }

    public function down(): void
    {
        Schema::table('alumnos', function (Blueprint $table) {
            $table->dropColumn(['certificado_medico_path', 'identificacion_path', 'foto_path']);
        });
    }
};
