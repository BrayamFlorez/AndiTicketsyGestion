<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('workflow_stage_actors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workflow_stage_id')
                ->constrained('workflow_stages')
                ->cascadeOnDelete();

            // Polimorfismo ligero: apunta a un rol Spatie o a un usuario específico
            $table->enum('actor_type', ['role', 'user']);
            $table->unsignedBigInteger('actor_id');

            $table->string('etiqueta')->nullable()
                ->comment('Etiqueta legible; ej. "Aprobación Financiera"');
            $table->timestamps();

            $table->index(['workflow_stage_id', 'actor_type']);
            $table->index(['actor_type', 'actor_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workflow_stage_actors');
    }
};