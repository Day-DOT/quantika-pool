<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('groups', function (Blueprint $table) {
            $table->id();

            // Sucursal
            $table->foreignId('branch_id')
                ->constrained('branches')
                ->cascadeOnDelete();

            // Instructor responsable
            $table->foreignId('instructor_id')
                ->nullable()
                ->constrained('instructors')
                ->nullOnDelete();

            // Nivel del grupo
            $table->foreignId('level_id')
                ->nullable()
                ->constrained('levels')
                ->nullOnDelete();

            // Carril asignado
            $table->foreignId('lane_id')
                ->nullable()
                ->constrained('lanes')
                ->nullOnDelete();

            // Bloque de horario
            $table->foreignId('schedule_block_id')
                ->nullable()
                ->constrained('schedule_blocks')
                ->nullOnDelete();

            $table->string('name');

            $table->unsignedInteger('capacity')->default(1);

            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->index(
                ['branch_id', 'is_active'],
                'group_branch_status_idx'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('groups');
    }
};