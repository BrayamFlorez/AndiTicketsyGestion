<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('workflow_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workflow_instance_id')
                ->constrained('workflow_instances')
                ->cascadeOnDelete();

            // Nulo si el adjunto pertenece a la instancia (documento principal del iniciador)
            $table->foreignId('workflow_instance_step_id')
                ->nullable()
                ->constrained('workflow_instance_steps')
                ->cascadeOnDelete();

            $table->foreignId('subido_por')
                ->constrained('users')
                ->restrictOnDelete();

            $table->unsignedInteger('revision')->default(1);
            $table->string('nombre_original');
            $table->string('ruta');
            $table->string('mime_type', 100);
            $table->unsignedInteger('tamano_bytes');
            $table->timestamps();

            $table->index(['workflow_instance_id', 'revision']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workflow_attachments');
    }
};