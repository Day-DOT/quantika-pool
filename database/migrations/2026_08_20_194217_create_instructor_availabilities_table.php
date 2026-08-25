<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('instructor_availabilities', function (Blueprint $table) {
            $table->id();

            $table->foreignId('instructor_id')
                ->constrained('instructors')
                ->cascadeOnDelete();

            $table->foreignId('branch_id')
                ->nullable()
                ->constrained('branches')
                ->cascadeOnDelete();

            $table->unsignedTinyInteger('day_of_week');

            $table->time('start_time');

            $table->time('end_time');

            $table->boolean('is_active')
                ->default(true);

            $table->timestamps();

            $table->index(
                ['instructor_id', 'branch_id', 'day_of_week'],
                'inst_avail_idx'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('instructor_availabilities');
    }
};