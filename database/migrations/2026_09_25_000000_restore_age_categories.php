<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('niveles')
            ->whereIn('categoria_edad', ['Niñas', 'Adultos mujeres', 'Adultos hombres'])
            ->update(['categoria_edad' => 'Adultos']);
    }

    public function down(): void
    {
        DB::table('niveles')
            ->where('categoria_edad', 'Adultos')
            ->update(['categoria_edad' => 'Adultos']);
    }
};
