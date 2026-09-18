<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('alumnos', function (Blueprint $table) {
            $table->string('matricula', 30)->nullable()->unique()->after('id');
        });

        DB::table('alumnos')
            ->orderBy('id')
            ->get(['id'])
            ->each(function (object $alumno): void {
                DB::table('alumnos')
                    ->where('id', $alumno->id)
                    ->update(['matricula' => 'ALU-'.str_pad((string) $alumno->id, 6, '0', STR_PAD_LEFT)]);
            });
    }

    public function down(): void
    {
        Schema::table('alumnos', function (Blueprint $table) {
            $table->dropUnique('alumnos_matricula_unique');
            $table->dropColumn('matricula');
        });
    }
};
