<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('equipos', function (Blueprint $table) {
            $table->id();

            // ── Vinculación ───────────────────────────────────────────────
            $table->foreignId('empresa_id')->constrained()->restrictOnDelete();
            $table->foreignId('sede_id')->nullable()->constrained()->nullOnDelete();

            // Usuario al que se le asignó el equipo (responsable de uso)
            $table->foreignId('responsable_id')->nullable()
                ->constrained('users')->nullOnDelete();

            // Técnico que registró el equipo (para auditoría básica)
            $table->foreignId('creado_por')->nullable()
                ->constrained('users')->nullOnDelete();

            // ── Categorización ────────────────────────────────────────────
            $table->string('categoria', 30);  // computadora, portatil, impresora...
            // No usamos ENUM nativo para permitir agregar categorías nuevas
            // sin migración. La validación vive en el modelo (CATEGORIAS).

            // ── Identificación del equipo ─────────────────────────────────
            $table->string('nombre', 150);          // Etiqueta visible: "PC-Recepcion-01"
            $table->string('marca', 100)->nullable();
            $table->string('modelo', 100)->nullable();
            $table->string('numero_serie', 100)->nullable();
            $table->string('ubicacion', 255)->nullable();  // Texto libre: "Recepción", "Oficina 203"

            // ── Estado operativo ──────────────────────────────────────────
            $table->string('estado', 30)->default('activo');
            // activo, mantenimiento, dado_de_baja

            // ── Campos específicos por categoría (JSON dinámico) ─────────
            $table->json('especificaciones')->nullable();

            // ── Notas y observaciones ─────────────────────────────────────
            $table->text('notas')->nullable();

            $table->timestamps();

            // ── Índices para queries comunes ──────────────────────────────
            $table->index(['empresa_id', 'estado']);
            $table->index('categoria');
            // Número de serie único por empresa (no global — diferentes empresas
            // pueden tener equipos con el mismo serial de fabricante)
            $table->unique(['empresa_id', 'numero_serie'], 'equipos_empresa_serie_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('equipos');
    }
};
