
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asignacion_equipos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('equipo_id')
                ->constrained('equipos')
                ->restrictOnDelete();

            $table->foreignId('user_id')
                ->constrained('users')
                ->restrictOnDelete();

            $table->foreignId('laboratorio_id')
                ->constrained('laboratorios')
                ->restrictOnDelete();

            $table->foreignId('asignado_por')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->dateTime('fecha_asignacion');
            $table->dateTime('fecha_devolucion')->nullable();
            $table->string('estado', 30)->default('asignado');
            $table->text('observaciones')->nullable();
            $table->timestamps();

            $table->index(['equipo_id', 'estado']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asignacion_equipos');
    }
};
