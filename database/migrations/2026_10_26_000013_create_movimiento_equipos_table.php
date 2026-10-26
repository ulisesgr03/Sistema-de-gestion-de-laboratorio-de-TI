<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('movimiento_equipos', function (Blueprint $table) {
    $table->id();
    $table->foreignId('equipo_id')->constrained('equipos')->restrictOnDelete();
    $table->foreignId('laboratorio_origen_id')->nullable()->constrained('laboratorios')->nullOnDelete();
    $table->foreignId('laboratorio_destino_id')->nullable()->constrained('laboratorios')->nullOnDelete();
    $table->foreignId('realizado_por')->constrained('users')->restrictOnDelete();
    $table->dateTime('fecha_movimiento');
    $table->text('motivo')->nullable();
    $table->timestamps();
});
    }

    public function down(): void
    {
        Schema::dropIfExists('movimiento_equipos');
    }
};
