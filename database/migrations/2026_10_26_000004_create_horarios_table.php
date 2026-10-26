<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('horarios', function (Blueprint $table) {
    $table->id();
    $table->foreignId('laboratorio_id')->constrained('laboratorios')->cascadeOnDelete();
    $table->string('dia_semana', 15);
    $table->time('hora_inicio');
    $table->time('hora_fin');
    $table->boolean('activo')->default(true);
    $table->timestamps();
});
    }

    public function down(): void
    {
        Schema::dropIfExists('horarios');
    }
};
