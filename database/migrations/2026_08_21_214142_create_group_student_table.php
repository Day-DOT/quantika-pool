<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('group_student', function (Blueprint $table) {
            $table->id();

            $table->foreignId('group_id')
                ->constrained('groups')
                ->cascadeOnDelete();

            $table->foreignId('student_id')
                ->constrained('students')
                ->cascadeOnDelete();

            // Fecha en que ingresó al grupo
            $table->date('joined_at');

            // Fecha en que salió del grupo
            $table->date('left_at')->nullable();

            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->index(
                ['student_id', 'is_active'],
                'group_student_status_idx'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('group_student');
    }
};