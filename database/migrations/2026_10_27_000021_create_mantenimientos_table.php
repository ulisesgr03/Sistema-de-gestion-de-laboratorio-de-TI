<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mantenimientos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('equipo_id')
                ->constrained('equipos')
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('incidencia_id')
                ->nullable()
                ->constrained('incidencias')
                ->nullOnDelete();

            $table->foreignId('laboratorio_id')
                ->nullable()
                ->constrained('laboratorios')
                ->nullOnDelete();

            $table->string('tipo', 50)->default('preventivo'); // preventivo, correctivo
            $table->text('descripcion');
            $table->text('actividades_realizadas')->nullable();
            $table->string('resultado', 100)->nullable(); // exitoso, reparado, baja_sugerida, etc.
            $table->dateTime('fecha_inicio');
            $table->dateTime('fecha_fin')->nullable();
            $table->string('estado', 30)->default('programado'); // programado, en_proceso, completado, cancelado
            $table->decimal('costo', 10, 2)->nullable();
            $table->timestamps();

            $table->index(['equipo_id', 'estado']);
            $table->index(['fecha_inicio', 'estado']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mantenimientos');
    }
};
