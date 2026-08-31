<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('workflow_instances', function (Blueprint $table) {
            $table->id();
            $table->string('codigo')->unique()
                ->comment('Código legible; ej. FLJ-2026-000123');
            $table->foreignId('workflow_template_id')
                ->constrained('workflow_templates')
                ->restrictOnDelete();
            $table->foreignId('document_type_id')
                ->nullable()
                ->constrained('document_types')
                ->nullOnDelete();
            $table->string('titulo');
            $table->text('descripcion')->nullable();
            $table->foreignId('iniciado_por')
                ->constrained('users')
                ->restrictOnDelete();
            $table->string('referencia_externa', 50)->nullable()
                ->comment('Índice opcional definido por el iniciador para búsqueda rápida (ej: OT-2026-042, PROY-ACME-Q3)');

            $table->enum('estado', [
                'en_progreso',
                'requiere_correccion',
                'aprobado',
                'cancelado',
                'archivado',
            ])->default('en_progreso');

            $table->foreignId('etapa_actual_id')
                ->nullable()
                ->constrained('workflow_stages')
                ->nullOnDelete();

            $table->unsignedInteger('revision')->default(1);
            $table->timestamp('iniciado_en')->useCurrent();
            $table->timestamp('cerrado_en')->nullable();
            $table->timestamps();

            $table->index(['estado', 'iniciado_por']);
            $table->index(['estado', 'etapa_actual_id']);
            $table->index('referencia_externa');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workflow_instances');
    }
};