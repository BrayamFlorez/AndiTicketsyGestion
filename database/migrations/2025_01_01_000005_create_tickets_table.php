<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();

            // ── Solicitante y asignación ──────────────────────────────────
            $table->foreignId('solicitante_id')
                ->constrained('users')
                ->restrictOnDelete();

            $table->foreignId('asignado_a')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            // ── Contexto del ticket ───────────────────────────────────────
            $table->foreignId('empresa_id')
                ->nullable()
                ->constrained('empresas')
                ->nullOnDelete();

            $table->foreignId('sede_id')
                ->nullable()
                ->constrained('sedes')
                ->nullOnDelete();

            // ── Contenido ─────────────────────────────────────────────────
            $table->string('titulo');

            $table->enum('categoria', [
                'hardware',
                'software',
                'red',
                'impresora',
                'telefonia',
                'acceso',
                'otro',
            ])->default('otro');

            $table->enum('prioridad', [
                'baja',
                'media',
                'alta',
                'critica',
            ])->default('media');

            $table->enum('estado', [
                'abierto',
                'asignado',
                'en_progreso',
                'resuelto',
                'cerrado',
                'cancelado',
            ])->default('abierto');

            $table->text('descripcion');

            // ── Archivos adjuntos (rutas relativas al disco privado) ───────
            $table->string('foto_solicitud')->nullable();       // Sube el usuario
            $table->string('evidencia_resolucion')->nullable(); // Sube el técnico

            // ── Resolución ────────────────────────────────────────────────
            $table->text('notas_resolucion')->nullable();

            // ── Timestamps de ciclo de vida ───────────────────────────────
            $table->timestamp('asignado_at')->nullable();
            $table->timestamp('iniciado_at')->nullable();
            $table->timestamp('resuelto_at')->nullable();
            $table->timestamp('cerrado_at')->nullable();

            $table->timestamps(); // created_at = fecha de apertura del ticket
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
