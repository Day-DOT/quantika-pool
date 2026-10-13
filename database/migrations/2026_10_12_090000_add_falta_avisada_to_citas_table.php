<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('citas', function (Blueprint $table) {
            // Falta que la familia avisó con anticipación y la administración
            // registró antes de la clase, para que el instructor lo sepa.
            $table->boolean('falta_avisada')->default(false)->after('es_extra');
        });
    }

    public function down(): void
    {
        Schema::table('citas', function (Blueprint $table) {
            $table->dropColumn('falta_avisada');
        });
    }
};
