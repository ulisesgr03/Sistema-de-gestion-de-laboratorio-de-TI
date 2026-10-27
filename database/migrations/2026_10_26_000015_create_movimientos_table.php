
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

            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('entidad', 100);
            $table->unsignedBigInteger('entidad_id')->nullable();
            $table->string('tipo_movimiento', 100);
            $table->text('descripcion')->nullable();
            $table->dateTime('fecha_movimiento');
            $table->timestamps();

            $table->index(['entidad', 'entidad_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('movimientos');
    }
};
