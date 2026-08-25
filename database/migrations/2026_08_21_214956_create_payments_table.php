<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();

            // Alumno al que corresponde el pago
            $table->foreignId('student_id')
                ->constrained('students')
                ->cascadeOnDelete();

            // Sucursal donde corresponde el cobro
            $table->foreignId('branch_id')
                ->constrained('branches')
                ->cascadeOnDelete();

            // Concepto del pago
            $table->foreignId('payment_concept_id')
                ->constrained('payment_concepts')
                ->restrictOnDelete();

            /*
            Usuario que creó o registró el movimiento.
            Puede ser administrador, recepción, etc.
            */
            $table->foreignId('registered_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            // Folio único para identificar el movimiento
            $table->string('folio')->unique();

            // Monto total que debe pagar
            $table->decimal('amount', 10, 2);

            // Fecha en que se generó el cobro
            $table->date('issued_date');

            // Fecha límite de pago
            $table->date('due_date')->nullable();

            // Fecha en que se realizó el pago
            $table->date('paid_at')->nullable();

            /*
            pending = Pendiente
            paid = Pagado
            under_review = En revisión
            cancelled = Cancelado
            */
            $table->enum('status', [
                'pending',
                'paid',
                'under_review',
                'cancelled'
            ])->default('pending');

            /*
            Métodos de pago iniciales.
            Podremos ampliar esto posteriormente.
            */
            $table->enum('payment_method', [
                'cash',
                'transfer',
                'card',
                'other'
            ])->nullable();

            // Referencia bancaria, número de operación, etc.
            $table->string('reference')->nullable();

            // Ruta del comprobante cuando implementemos archivos
            $table->string('receipt_path')->nullable();

            // Observaciones
            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index(
                ['student_id', 'status'],
                'payment_student_status_idx'
            );

            $table->index(
                ['branch_id', 'status'],
                'payment_branch_status_idx'
            );

            $table->index(
                ['due_date', 'status'],
                'payment_due_status_idx'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};