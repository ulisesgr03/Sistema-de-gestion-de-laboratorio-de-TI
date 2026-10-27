
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('estaciones', function (Blueprint $table) {
            $table->id();

            $table->foreignId('laboratorio_id')
                ->constrained('laboratorios')
                ->cascadeOnDelete();

            $table->string('nombre', 100);
            $table->string('ubicacion', 255)->nullable();
            $table->string('estado', 30)->default('disponible');
            $table->timestamps();

            $table->unique(['laboratorio_id', 'nombre']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('estaciones');
    }
};
