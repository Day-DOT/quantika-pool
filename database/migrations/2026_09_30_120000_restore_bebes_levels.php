<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * La unificación de niveles por sexo movió la categoría "Bebés" a
     * "Niños". Bebés es una categoría por edad (no por sexo), así que sus
     * niveles vuelven a ella conservando alumnos, horarios y evaluaciones.
     */
    private const NIVELES_BEBES = ['PECECITO', 'AJOLOTITO', 'RANITA'];

    public function up(): void
    {
        DB::table('niveles')
            ->where('categoria_edad', 'Niños')
            ->whereIn(DB::raw('UPPER(TRIM(nombre))'), self::NIVELES_BEBES)
            ->update(['categoria_edad' => 'Bebés']);
    }

    public function down(): void
    {
        DB::table('niveles')
            ->where('categoria_edad', 'Bebés')
            ->whereIn(DB::raw('UPPER(TRIM(nombre))'), self::NIVELES_BEBES)
            ->update(['categoria_edad' => 'Niños']);
    }
};
