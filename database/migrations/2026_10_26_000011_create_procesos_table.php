<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('procesos', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
    $table->foreignId('laboratorio_id')->nullable()->constrained('laboratorios')->nullOnDelete();
    $table->string('tipo');
    $table->text('descripcion')->nullable();
    $table->string('estado')->default('registrado');
    $table->timestamps();
});
    }

    public function down(): void
    {
        Schema::dropIfExists('procesos');
    }
};
