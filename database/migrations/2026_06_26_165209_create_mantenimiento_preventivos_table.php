<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('mantenimientos_preventivos', function (Blueprint $table) {
            $table->id();

            // ── Relaciones ──
            $table->foreignId('equipo_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tecnico_id')->nullable()->constrained('users')->nullOnDelete();

            // ── Fechas y Estado ──
            $table->date('fecha_programada')->nullable();
            $table->datetime('fecha_inicio')->nullable();
            $table->datetime('fecha_finalizacion')->nullable();
            $table->enum('estado', ['programado', 'en_progreso', 'completado', 'cancelado'])->default('programado');

            // ── Checklist de Hardware (Booleanos) ──
            $table->boolean('hw_limpieza_interna')->default(false);
            $table->boolean('hw_limpieza_externa')->default(false);
            $table->boolean('hw_cambio_pasta_termica')->default(false);
            $table->boolean('hw_revision_ventiladores')->default(false);
            $table->boolean('hw_verificacion_cables')->default(false);

            // ── Checklist de Software (Booleanos) ──
            $table->boolean('sw_limpieza_temporales')->default(false);
            $table->boolean('sw_actualizacion_so')->default(false);
            $table->boolean('sw_revision_antivirus')->default(false);
            $table->boolean('sw_revision_disco_duro')->default(false);
            $table->boolean('sw_desinstalacion_basura')->default(false);

            // ── Campos de texto / Observaciones ──
            $table->text('observaciones_tecnicas')->nullable();
            $table->text('recomendaciones_cliente')->nullable();

            // ── Evidencia ──
            $table->string('firma_cliente_path')->nullable();
            $table->string('evidencia_fotografica_path')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mantenimiento_preventivos');
    }
};
