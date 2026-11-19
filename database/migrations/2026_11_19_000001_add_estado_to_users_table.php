<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'estado')) {
                $table->enum('estado', ['pendiente', 'aprobado', 'rechazado'])
                    ->default('pendiente')
                    ->after('activo');
            }
        });

        // Actualizar a 'aprobado' a todos los usuarios y administradores existentes
        // para no bloquear el acceso actual.
        DB::table('users')->update(['estado' => 'aprobado']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'estado')) {
                $table->dropColumn('estado');
            }
        });
    }
};
