<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Una reposición es una Cita nueva (en otro horario/fecha) que compensa
     * una clase perdida (asistio = false). Se guarda la referencia a la cita
     * original para llevar el conteo de reposiciones usadas por mes y evitar
     * duplicarlas.
     */
    public function up(): void
    {
        Schema::table('citas', function (Blueprint $table) {
            $table->foreignId('reposicion_de_id')
                ->nullable()
                ->after('estado')
                ->constrained('citas')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('citas', function (Blueprint $table) {
            $table->dropConstrainedForeignId('reposicion_de_id');
        });
    }
};
