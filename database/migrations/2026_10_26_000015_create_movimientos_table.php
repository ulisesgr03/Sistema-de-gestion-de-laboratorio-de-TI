<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('movimientos', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
    $table->string('entidad');
    $table->unsignedBigInteger('entidad_id')->nullable();
    $table->string('tipo_movimiento');
    $table->text('descripcion')->nullable();
    $table->timestamp('fecha_movimiento')->useCurrent();
    $table->timestamps();
});
    }

    public function down(): void
    {
        Schema::dropIfExists('movimientos');
    }
};
