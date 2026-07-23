<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('workflow_step_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workflow_instance_step_id')
                ->constrained('workflow_instance_steps')
                ->cascadeOnDelete();

            // Puede quedar nulo si el stage_actor original fue borrado (mantenemos historial)
            $table->foreignId('workflow_stage_actor_id')
                ->nullable()
                ->constrained('workflow_stage_actors')
                ->nullOnDelete();

            // Nulo hasta que alguien del rol la tome; ya viene lleno si el actor era un usuario específico
            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->enum('estado', [
                'pendiente',
                'aprobado',
                'rechazado',
                'devuelto',
            ])->default('pendiente');

            $table->text('comentario')->nullable();
            $table->timestamp('decidido_en')->nullable();
            $table->timestamps();

            $table->index(['workflow_instance_step_id', 'estado']);
            $table->index(['user_id', 'estado']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workflow_step_assignments');
    }
};