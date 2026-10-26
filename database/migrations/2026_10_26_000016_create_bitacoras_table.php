<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bitacoras', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
    $table->string('accion');
    $table->string('modulo')->nullable();
    $table->text('detalle')->nullable();
    $table->string('direccion_ip', 45)->nullable();
    $table->timestamp('fecha')->useCurrent();
    $table->timestamps();
});
    }

    public function down(): void
    {
        Schema::dropIfExists('bitacoras');
    }
};
