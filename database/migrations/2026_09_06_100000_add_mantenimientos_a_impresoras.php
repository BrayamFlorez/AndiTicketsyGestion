<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // 1. Campos nuevos en impresoras
        Schema::table('impresoras', function (Blueprint $table) {
            $table->unsignedSmallInteger('intervalo_mantenimiento_dias')->nullable()
                ->after('contador_actualizado_en')
                ->comment('Días sugeridos entre mantenimientos preventivos');
            $table->date('proximo_mantenimiento_sugerido')->nullable()
                ->after('intervalo_mantenimiento_dias')
                ->comment('Fecha calculada = último mantenimiento + intervalo');
            $table->index('proximo_mantenimiento_sugerido', 'idx_proximo_mantto');
        });

        // 2. Tabla de mantenimientos
        Schema::create('impresora_mantenimientos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('impresora_id')->constrained('impresoras')->cascadeOnDelete();
            $table->enum('tipo', ['preventivo', 'correctivo', 'limpieza', 'cambio_consumible']);
            $table->date('fecha_realizado');
            $table->foreignId('realizado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->date('proximo_programado')->nullable();
            $table->unsignedInteger('contador_paginas')->nullable();
            $table->text('descripcion');
            $table->text('observaciones')->nullable();
            $table->foreignId('registrado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['impresora_id', 'fecha_realizado']);
            $table->index('proximo_programado');
        });

        // 3. Tabla de alertas enviadas
        Schema::create('impresora_alertas_enviadas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('impresora_id')->constrained('impresoras')->cascadeOnDelete();
            $table->enum('tipo', ['consumible_bajo', 'mantenimiento_proximo']);
            $table->unsignedBigInteger('referencia_id')->nullable()
                ->comment('ID del consumible o mantenimiento que disparó la alerta');
            $table->timestamp('enviada_en')->useCurrent();
            $table->timestamp('resuelta_en')->nullable();

            $table->index(['impresora_id', 'tipo', 'referencia_id']);
            $table->index('enviada_en');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('impresora_alertas_enviadas');
        Schema::dropIfExists('impresora_mantenimientos');

        Schema::table('impresoras', function (Blueprint $table) {
            $table->dropIndex('idx_proximo_mantto');
            $table->dropColumn(['intervalo_mantenimiento_dias', 'proximo_mantenimiento_sugerido']);
        });
    }
};