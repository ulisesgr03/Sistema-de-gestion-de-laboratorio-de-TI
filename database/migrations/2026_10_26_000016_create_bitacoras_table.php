
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

            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('accion', 150);
            $table->string('modulo', 100);
            $table->text('detalle')->nullable();
            $table->string('direccion_ip', 45)->nullable();
            $table->dateTime('fecha');
            $table->timestamps();

            $table->index(['modulo', 'fecha']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bitacoras');
    }
};
