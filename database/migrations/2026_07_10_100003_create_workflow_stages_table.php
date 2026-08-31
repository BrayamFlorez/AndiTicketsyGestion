<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('workflow_stages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workflow_template_id')
                ->constrained('workflow_templates')
                ->cascadeOnDelete();
            $table->string('nombre');
            $table->text('descripcion')->nullable();
            $table->unsignedInteger('orden');

            $table->string('codigo_externo', 50)->nullable()
                ->comment('Identificador definido por el cliente para búsqueda rápida (ej: APR-FIN, REV-TEC-01)');

            // Modo de evaluación de la etapa
            $table->enum('modo', [
                'secuencial',           // Un solo actor, decisión única
                'paralelo_todos',       // Todos deben aprobar para avanzar
                'paralelo_cualquiera',  // Basta con uno para avanzar
            ])->default('secuencial');

            // Qué hacer cuando se rechaza en esta etapa
            $table->enum('politica_rechazo', [
                'devolver_iniciador',
                'devolver_etapa_anterior',
                'devolver_etapa_especifica',
                'finalizar',
            ])->default('devolver_iniciador');

            // Solo aplica si politica_rechazo = devolver_etapa_especifica
            $table->foreignId('etapa_destino_rechazo_id')
                ->nullable()
                ->constrained('workflow_stages')
                ->nullOnDelete();

            $table->boolean('requiere_adjunto')->default(false);
            $table->unsignedSmallInteger('sla_horas')->nullable();
            $table->timestamps();

            $table->unique(['workflow_template_id', 'orden']);
            $table->index('codigo_externo');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workflow_stages');
    }
};