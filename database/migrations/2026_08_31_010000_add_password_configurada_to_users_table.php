<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Indica si el usuario ya definió su propia contraseña. Los usuarios
     * existentes se marcan como configurados (no deben verse afectados);
     * solo las cuentas de tutor que se creen a partir de ahora nacen con
     * esto en falso, hasta que el propio tutor la active en /registro.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('password_configurada')->default(true)->after('password');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('password_configurada');
        });
    }
};
