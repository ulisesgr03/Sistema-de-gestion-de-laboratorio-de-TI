<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('practicas', function (Blueprint $table) {
    $table->id();
    $table->foreignId('laboratorio_id')->constrained('laboratorios')->cascadeOnDelete();
    $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
    $table->string('nombre');
    $table->text('descripcion')->nullable();
    $table->dateTime('fecha_inicio')->nullable();
    $table->dateTime('fecha_fin')->nullable();
    $table->string('estado')->default('programada');
    $table->timestamps();
});
    }

    public function down(): void
    {
        Schema::dropIfExists('practicas');
    }
};
