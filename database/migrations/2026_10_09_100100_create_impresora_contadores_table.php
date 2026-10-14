<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Histórico de lecturas del contador de páginas (la fecha es la del registro)
        Schema::create('impresora_contadores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('impresora_id')->constrained('impresoras')->cascadeOnDelete();
            $table->unsignedBigInteger('contador');
            // Diferencia con la lectura anterior; null en la primera o tras un reinicio del contador
            $table->bigInteger('paginas_desde_anterior')->nullable();
            $table->boolean('reinicio')->default(false);
            $table->foreignId('registrado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['impresora_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('impresora_contadores');
    }
};
