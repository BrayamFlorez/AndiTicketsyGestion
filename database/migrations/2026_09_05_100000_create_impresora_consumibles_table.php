<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('impresora_consumibles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('impresora_id')->constrained('impresoras')->cascadeOnDelete();

            $table->enum('tipo', [
                'toner', 'cartucho', 'tanque_tinta',
                'tambor', 'fusor', 'cabezal',
                'kit_mantenimiento', 'otro',
            ]);
            $table->enum('color', [
                'negro', 'cyan', 'magenta', 'amarillo', 'unico', 'no_aplica',
            ])->default('no_aplica');

            $table->string('etiqueta', 80)->nullable()->comment('Nombre libre. Ej: Tóner HP 83A');

            $table->unsignedSmallInteger('dias_estimados')->default(30)
                ->comment('Duración esperada en días para ESTA impresora');

            $table->date('fecha_instalacion')->nullable();
            $table->date('fecha_reemplazo')->nullable();
            $table->unsignedSmallInteger('duracion_real_dias')->nullable()
                ->comment('Calculado al reemplazar');

            $table->boolean('activo')->default(true);
            $table->foreignId('consumible_anterior_id')->nullable()
                ->constrained('impresora_consumibles')->nullOnDelete();

            $table->text('observaciones')->nullable();
            $table->foreignId('registrado_por')->nullable()
                ->constrained('users')->nullOnDelete();

            $table->timestamps();

            $table->index(['impresora_id', 'activo']);
            $table->index(['tipo', 'color']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('impresora_consumibles');
    }
};