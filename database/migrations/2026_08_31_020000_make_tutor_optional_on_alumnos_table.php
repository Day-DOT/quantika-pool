<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Un alumno puede no tener tutor con cuenta de acceso (por ejemplo, si no
     * se capturó un correo). En ese caso se guardan sus datos de contacto
     * como texto simple, sin crear un usuario de portal.
     */
    public function up(): void
    {
        Schema::table('alumnos', function (Blueprint $table) {
            $table->foreignId('tutor_user_id')->nullable()->change();
            $table->string('tutor_contacto_nombre')->nullable()->after('tutor_user_id');
            $table->string('tutor_contacto_telefono')->nullable()->after('tutor_contacto_nombre');
        });
    }

    public function down(): void
    {
        Schema::table('alumnos', function (Blueprint $table) {
            $table->dropColumn(['tutor_contacto_nombre', 'tutor_contacto_telefono']);
            $table->foreignId('tutor_user_id')->nullable(false)->change();
        });
    }
};
