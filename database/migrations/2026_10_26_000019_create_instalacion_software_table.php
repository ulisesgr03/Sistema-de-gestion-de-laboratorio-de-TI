
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('instalacion_software', function (Blueprint $table) {
            $table->id();

            $table->foreignId('equipo_id')
                ->constrained('equipos')
                ->cascadeOnDelete();

            $table->foreignId('software_id')
                ->constrained('software')
                ->restrictOnDelete();

            $table->foreignId('licencia_id')
                ->nullable()
                ->constrained('licencias')
                ->nullOnDelete();

            $table->foreignId('instalado_por')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->dateTime('fecha_instalacion');
            $table->string('version_instalada', 50)->nullable();
            $table->string('estado', 30)->default('instalado');
            $table->timestamps();

            $table->unique(['equipo_id', 'software_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('instalacion_software');
    }
};
