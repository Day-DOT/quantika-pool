<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('niveles')->where('categoria_edad', 'Bebés')->update(['categoria_edad' => 'Niños']);
        DB::table('niveles')->where('categoria_edad', 'Adultos')->update(['categoria_edad' => 'Adultos mujeres']);
        DB::table('niveles')->update(['categoria' => null]);
    }

    public function down(): void
    {
        DB::table('niveles')->where('categoria_edad', 'Niños')->update(['categoria_edad' => 'Bebés']);
        DB::table('niveles')->where('categoria_edad', 'Adultos mujeres')->update(['categoria_edad' => 'Adultos']);
    }
};
