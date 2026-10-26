<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('licencias', function (Blueprint $table) {
    $table->id();
    $table->foreignId('software_id')->constrained('software')->cascadeOnDelete();
    $table->string('tipo')->nullable();
    $table->string('clave_licencia')->nullable()->unique();
    $table->unsignedInteger('cantidad_licencias')->default(1);
    $table->date('fecha_inicio')->nullable();
    $table->date('fecha_vencimiento')->nullable();
    $table->string('estado')->default('activa');
    $table->timestamps();
});
    }

    public function down(): void
    {
        Schema::dropIfExists('licencias');
    }
};
