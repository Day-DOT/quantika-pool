<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('alumno')->after('password');
            $table->foreignId('sucursal_id')->nullable()->after('role')
                ->constrained('sucursales')->nullOnDelete();
            $table->string('telefono')->nullable()->after('sucursal_id');
            $table->string('avatar_path')->nullable()->after('telefono');
            $table->boolean('activo')->default(true)->after('avatar_path');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('sucursal_id');
            $table->dropColumn(['role', 'telefono', 'avatar_path', 'activo']);
        });
    }
};
