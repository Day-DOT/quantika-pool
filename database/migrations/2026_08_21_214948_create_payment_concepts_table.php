<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_concepts', function (Blueprint $table) {
            $table->id();

            // Concepto disponible para una sucursal específica.
            // Si es null, puede utilizarse de forma general.
            $table->foreignId('branch_id')
                ->nullable()
                ->constrained('branches')
                ->nullOnDelete();

            // Ejemplo: Mensualidad, Inscripción, Clase adicional, etc.
            $table->string('name');

            $table->text('description')->nullable();

            // Monto sugerido del concepto
            $table->decimal('default_amount', 10, 2)
                ->nullable();

            /*
            Tipos:
            monthly = Mensualidad
            registration = Inscripción
            additional = Adicional
            other = Otro
            */
            $table->enum('type', [
                'monthly',
                'registration',
                'additional',
                'other'
            ])->default('other');

            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->index(
                ['branch_id', 'is_active'],
                'pay_concept_branch_idx'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_concepts');
    }
};