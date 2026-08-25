<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('branch_instructor', function (Blueprint $table) {
            $table->id();

            $table->foreignId('branch_id')
                ->constrained('branches')
                ->cascadeOnDelete();

            $table->foreignId('instructor_id')
                ->constrained('instructors')
                ->cascadeOnDelete();

            // Indica si esta es la sucursal principal del instructor
            $table->boolean('is_primary')
                ->default(false);

            $table->boolean('is_active')
                ->default(true);

            $table->timestamps();

            // Evita duplicar la misma asignación
            $table->unique([
                'branch_id',
                'instructor_id'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('branch_instructor');
    }
};