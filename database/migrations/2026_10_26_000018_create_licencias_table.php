
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

            $table->foreignId('software_id')
                ->constrained('software')
                ->restrictOnDelete();

            $table->string('tipo', 50);
            $table->string('clave_licencia', 255)->nullable()->unique();
            $table->unsignedInteger('cantidad_licencias')->default(1);
            $table->date('fecha_inicio')->nullable();
            $table->date('fecha_vencimiento')->nullable();
            $table->string('estado', 30)->default('activa');
            $table->timestamps();

            $table->index(['software_id', 'estado']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('licencias');
    }
};
