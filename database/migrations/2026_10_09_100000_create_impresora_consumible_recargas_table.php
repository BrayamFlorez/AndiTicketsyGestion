<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        /*
         * Punto de referencia del nivel. Sin recargas parciales es NULL y el consumible
         * se comporta como antes: 100 % en la fecha de instalación.
         * Tras una recarga parcial: "tenía X % en esta fecha" y desde ahí baja.
         */
        Schema::table('impresora_consumibles', function (Blueprint $table) {
            $table->decimal('nivel_referencia', 5, 2)->nullable()->after('dias_estimados');
            $table->timestamp('fecha_referencia_nivel')->nullable()->after('nivel_referencia');
        });

        // Historial de recargas parciales con el cálculo que se hizo en cada una
        Schema::create('impresora_consumible_recargas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('impresora_id')->constrained('impresoras')->cascadeOnDelete();
            $table->foreignId('consumible_id')->constrained('impresora_consumibles')->cascadeOnDelete();

            // Lo que medía el técnico y lo que estimaba el sistema
            $table->decimal('nivel_estimado_sistema', 5, 2);
            $table->decimal('nivel_antes', 5, 2);
            $table->decimal('nivel_despues', 5, 2);
            $table->decimal('porcion_recargada', 5, 2);

            // Desviación de la estimación (positiva: se gastó más rápido de lo estimado)
            $table->decimal('desviacion_porcentaje', 6, 2);
            $table->integer('desviacion_dias');
            $table->decimal('dias_desde_referencia', 8, 2);

            // Recalibración
            $table->unsignedInteger('dias_estimados_anteriores');
            $table->unsignedInteger('dias_estimados_nuevos');
            $table->boolean('recalibrado');
            $table->unsignedInteger('dias_restantes_estimados');

            $table->text('observaciones')->nullable();
            $table->foreignId('registrado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['consumible_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('impresora_consumible_recargas');

        Schema::table('impresora_consumibles', function (Blueprint $table) {
            $table->dropColumn(['nivel_referencia', 'fecha_referencia_nivel']);
        });
    }
};
