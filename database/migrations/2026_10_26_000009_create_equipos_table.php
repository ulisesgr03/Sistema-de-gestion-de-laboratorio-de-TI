
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

            $table->foreignId('categoria_equipo_id')
                ->constrained('categoria_equipos')
                ->restrictOnDelete();

            $table->foreignId('estacion_id')
                ->nullable()
                ->constrained('estaciones')
                ->nullOnDelete();

            $table->string('codigo_inventario', 100)->unique();
            $table->string('nombre', 150);
            $table->string('marca', 100)->nullable();
            $table->string('modelo', 100)->nullable();
            $table->string('numero_serie', 150)->nullable()->unique();
            $table->date('fecha_adquisicion')->nullable();
            $table->string('estado', 30)->default('disponible');
            $table->text('observaciones')->nullable();
            $table->timestamps();

            $table->index('estado');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('equipos');
    }
};
