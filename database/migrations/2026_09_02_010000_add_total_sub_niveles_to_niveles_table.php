<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Cuántos sub-niveles tiene este nivel (p.ej. Tortuga A, B, C = 3). El
     * valor por default (1) significa "sin sub-niveles": el alumno pasa
     * directo al siguiente nivel principal, igual que antes de esta columna.
     */
    public function up(): void
    {
        Schema::table('niveles', function (Blueprint $table) {
            $table->unsignedTinyInteger('total_sub_niveles')->default(1)->after('categoria_edad');
        });
    }

    public function down(): void
    {
        Schema::table('niveles', function (Blueprint $table) {
            $table->dropColumn('total_sub_niveles');
        });
    }
};
