<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Sub-nivel actual del alumno dentro de su nivel principal (1 = A, 2 = B,
     * etc.). Se reinicia a 1 cada vez que el alumno avanza de nivel
     * principal.
     */
    public function up(): void
    {
        Schema::table('alumnos', function (Blueprint $table) {
            $table->unsignedTinyInteger('sub_nivel')->default(1)->after('nivel_id');
        });
    }

    public function down(): void
    {
        Schema::table('alumnos', function (Blueprint $table) {
            $table->dropColumn('sub_nivel');
        });
    }
};
