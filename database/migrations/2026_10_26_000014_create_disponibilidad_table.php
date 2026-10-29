
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('disponibilidad', function (Blueprint $table) {
            $table->id();

            $table->foreignId('laboratorio_id')
                ->nullable()
                ->constrained('laboratorios')
                ->cascadeOnDelete();

            $table->foreignId('equipo_id')
                ->nullable()
                ->constrained('equipos')
                ->cascadeOnDelete();

            $table->dateTime('inicio');
            $table->dateTime('fin');
            $table->string('estado', 30)->default('disponible');
            $table->text('observaciones')->nullable();
            $table->timestamps();

            $table->index(['inicio', 'fin']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('disponibilidad');
    }
};
