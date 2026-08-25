<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('instructors', function (Blueprint $table) {
            $table->id();

            // Usuario con el que el instructor iniciará sesión
            $table->foreignId('user_id')
                ->unique()
                ->constrained('users')
                ->cascadeOnDelete();

            // Datos específicos del instructor
            $table->string('employee_number')
                ->nullable()
                ->unique();

            $table->string('specialty')
                ->nullable();

            $table->text('notes')
                ->nullable();

            $table->date('hire_date')
                ->nullable();

            $table->boolean('is_active')
                ->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('instructors');
    }
};