<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('workflow_instance_steps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workflow_instance_id')
                ->constrained('workflow_instances')
                ->cascadeOnDelete();
            $table->foreignId('workflow_stage_id')
                ->constrained('workflow_stages')
                ->restrictOnDelete();

            $table->unsignedInteger('revision')->default(1);
            $table->string('codigo_referencia', 50)->nullable()
                ->comment('Etiqueta personal del iniciador para búsqueda rápida (ej: COT-ACME-Q3)');
            $table->enum('estado', [
                'pendiente',
                'en_curso',
                'aprobado',
                'rechazado',
                'devuelto',
                'omitido',
            ])->default('en_curso');

            $table->timestamp('inicio_en')->useCurrent();
            $table->timestamp('cierre_en')->nullable();
            $table->timestamps();

            $table->index(['workflow_instance_id', 'revision']);
            $table->index(['workflow_instance_id', 'estado']);
            $table->index('codigo_referencia');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workflow_instance_steps');
    }
};