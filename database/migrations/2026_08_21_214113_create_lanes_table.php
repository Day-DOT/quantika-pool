<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lanes', function (Blueprint $table) {
            $table->id();

            // Cada carril pertenece a una alberca
            $table->foreignId('pool_id')
                ->constrained('pools')
                ->cascadeOnDelete();

            $table->string('name');

            $table->unsignedInteger('capacity')->default(1);

            $table->text('description')->nullable();

            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->unique(['pool_id', 'name'], 'pool_lane_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lanes');
    }
};