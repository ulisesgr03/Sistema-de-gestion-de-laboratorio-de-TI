<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Agregar matricula y estado activo a usuarios
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'matricula')) {
                $table->string('matricula', 50)->nullable()->unique()->after('email');
            }
            if (!Schema::hasColumn('users', 'activo')) {
                $table->boolean('activo')->default(true)->after('password');
            }
            if (!Schema::hasColumn('users', 'telefono')) {
                $table->string('telefono', 20)->nullable()->after('activo');
            }
        });

        // 2. Agregar fecha limite y estados fisicos a asignacion_equipos
        Schema::table('asignacion_equipos', function (Blueprint $table) {
            if (!Schema::hasColumn('asignacion_equipos', 'fecha_limite')) {
                $table->dateTime('fecha_limite')->nullable()->after('fecha_asignacion');
            }
            if (!Schema::hasColumn('asignacion_equipos', 'estado_fisico_entrega')) {
                $table->string('estado_fisico_entrega', 255)->nullable()->after('observaciones');
            }
            if (!Schema::hasColumn('asignacion_equipos', 'estado_fisico_devolucion')) {
                $table->string('estado_fisico_devolucion', 255)->nullable()->after('estado_fisico_entrega');
            }
        });

        // 3. Crear tabla mantenimientos sugerida en los requerimientos del proyecto
        if (!Schema::hasTable('mantenimientos')) {
            Schema::create('mantenimientos', function (Blueprint $table) {
                $table->id();

                $table->foreignId('equipo_id')
                    ->nullable()
                    ->constrained('equipos')
                    ->nullOnDelete();

                $table->foreignId('laboratorio_id')
                    ->nullable()
                    ->constrained('laboratorios')
                    ->nullOnDelete();

                $table->foreignId('user_id')
                    ->constrained('users')
                    ->restrictOnDelete();

                $table->string('tipo', 50)->default('correctivo'); // preventivo, correctivo
                $table->string('titulo', 150);
                $table->text('actividades');
                $table->text('resultado')->nullable();
                $table->dateTime('fecha_programada');
                $table->dateTime('fecha_inicio')->nullable();
                $table->dateTime('fecha_fin')->nullable();
                $table->string('estado', 30)->default('programado'); // programado, en_proceso, completado, cancelado
                $table->decimal('costo', 10, 2)->nullable();
                $table->timestamps();

                $table->index(['estado', 'tipo']);
                $table->index(['equipo_id', 'estado']);
                $table->index(['laboratorio_id', 'estado']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('mantenimientos');

        Schema::table('asignacion_equipos', function (Blueprint $table) {
            if (Schema::hasColumn('asignacion_equipos', 'estado_fisico_devolucion')) {
                $table->dropColumn('estado_fisico_devolucion');
            }
            if (Schema::hasColumn('asignacion_equipos', 'estado_fisico_entrega')) {
                $table->dropColumn('estado_fisico_entrega');
            }
            if (Schema::hasColumn('asignacion_equipos', 'fecha_limite')) {
                $table->dropColumn('fecha_limite');
            }
        });

        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'telefono')) {
                $table->dropColumn('telefono');
            }
            if (Schema::hasColumn('users', 'activo')) {
                $table->dropColumn('activo');
            }
            if (Schema::hasColumn('users', 'matricula')) {
                $table->dropColumn('matricula');
            }
        });
    }
};
