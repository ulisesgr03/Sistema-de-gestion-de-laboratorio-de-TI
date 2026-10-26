<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('equipos', function (Blueprint $table) {
    $table->id();
    $table->foreignId('categoria_equipo_id')->constrained('categoria_equipos')->restrictOnDelete();
    $table->foreignId('estacion_id')->nullable()->constrained('estaciones')->nullOnDelete();
    $table->string('codigo_inventario')->unique();
    $table->string('nombre');
    $table->string('marca')->nullable();
    $table->string('modelo')->nullable();
    $table->string('numero_serie')->nullable()->unique();
    $table->date('fecha_adquisicion')->nullable();
    $table->string('estado')->default('disponible');
    $table->text('observaciones')->nullable();
    $table->timestamps();
});
    }

    public function down(): void
    {
        Schema::dropIfExists('equipos');
    }
};
