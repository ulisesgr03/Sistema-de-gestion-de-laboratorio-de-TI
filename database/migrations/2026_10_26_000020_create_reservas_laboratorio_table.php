<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reservas_laboratorio', function (Blueprint $table) {
    $table->id();
    $table->foreignId('laboratorio_id')->constrained('laboratorios')->cascadeOnDelete();
    $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
    $table->foreignId('practica_id')->nullable()->constrained('practicas')->nullOnDelete();
    $table->dateTime('inicio');
    $table->dateTime('fin');
    $table->string('estado')->default('pendiente');
    $table->text('motivo')->nullable();
    $table->timestamps();
});
    }

    public function down(): void
    {
        Schema::dropIfExists('reservas_laboratorio');
    }
};
