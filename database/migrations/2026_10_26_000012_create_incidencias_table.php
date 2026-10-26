<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('incidencias', function (Blueprint $table) {
    $table->id();
    $table->foreignId('equipo_id')->nullable()->constrained('equipos')->nullOnDelete();
    $table->foreignId('laboratorio_id')->constrained('laboratorios')->cascadeOnDelete();
    $table->foreignId('reportado_por')->constrained('users')->restrictOnDelete();
    $table->foreignId('asignado_a')->nullable()->constrained('users')->nullOnDelete();
    $table->string('titulo');
    $table->text('descripcion');
    $table->string('prioridad')->default('media');
    $table->string('estado')->default('abierta');
    $table->dateTime('fecha_resolucion')->nullable();
    $table->timestamps();
});
    }

    public function down(): void
    {
        Schema::dropIfExists('incidencias');
    }
};
