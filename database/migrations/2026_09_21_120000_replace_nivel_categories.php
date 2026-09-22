<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('niveles')->where('categoria_edad', 'Bebés')->update(['categoria_edad' => 'Niños']);
        DB::table('niveles')->where('categoria_edad', 'Adultos')->update(['categoria_edad' => 'Adultos mujeres']);
        // MySQL keeps this legacy column non-nullable. It is no longer shown
        // or used for classification, so retain it as an empty string.
        DB::table('niveles')->update(['categoria' => '']);
    }

    public function down(): void
    {
        DB::table('niveles')->where('categoria_edad', 'Niños')->update(['categoria_edad' => 'Bebés']);
        DB::table('niveles')->where('categoria_edad', 'Adultos mujeres')->update(['categoria_edad' => 'Adultos']);
    }
};
