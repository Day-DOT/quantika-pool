<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * El "orden" ya no necesita ser único: la escuela quiere poder marcar
     * varios niveles como "1" (por ejemplo, distintas variantes de un mismo
     * escalón) dentro del mismo grupo de edad.
     */
    public function up(): void
    {
        Schema::table('niveles', function (Blueprint $table) {
            $table->dropUnique('niveles_categoria_edad_orden_unique');
        });
    }

    public function down(): void
    {
        Schema::table('niveles', function (Blueprint $table) {
            $table->unique(['categoria_edad', 'orden'], 'niveles_categoria_edad_orden_unique');
        });
    }
};
