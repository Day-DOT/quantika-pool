<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Los niveles ahora se agrupan por edad (bebés, niños, adultos), cada uno
     * con su propia numeración de progresión. Por eso el "orden" deja de ser
     * único globalmente y pasa a serlo solo dentro de cada categoría de edad.
     */
    public function up(): void
    {
        Schema::table('niveles', function (Blueprint $table) {
            $table->string('categoria_edad')->default('Niños')->after('categoria');
        });

        DB::table('niveles')->update(['categoria_edad' => 'Niños']);

        Schema::table('niveles', function (Blueprint $table) {
            $table->dropUnique('niveles_orden_unique');
            $table->unique(['categoria_edad', 'orden'], 'niveles_categoria_edad_orden_unique');
        });
    }

    public function down(): void
    {
        Schema::table('niveles', function (Blueprint $table) {
            $table->dropUnique('niveles_categoria_edad_orden_unique');
            $table->unique('orden');
            $table->dropColumn('categoria_edad');
        });
    }
};
