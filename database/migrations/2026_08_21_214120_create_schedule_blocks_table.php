<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('schedule_blocks', function (Blueprint $table) {
            $table->id();

            // El horario pertenece a una sucursal
            $table->foreignId('branch_id')
                ->constrained('branches')
                ->cascadeOnDelete();

            // Nombre identificador del bloque
            $table->string('name');

            // 1 = lunes, 2 = martes ... 7 = domingo
            $table->unsignedTinyInteger('day_of_week');

            $table->time('start_time');

            $table->time('end_time');

            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->index(
                ['branch_id', 'day_of_week'],
                'schedule_branch_day_idx'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('schedule_blocks');
    }
};