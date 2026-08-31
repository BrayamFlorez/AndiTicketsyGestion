<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('workflow_instance_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workflow_instance_id')->constrained()->cascadeOnDelete();
            $table->foreignId('editado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->string('campo', 50)
                ->comment('Campo modificado: titulo, descripcion, referencia_externa');
            $table->text('valor_anterior')->nullable();
            $table->text('valor_nuevo')->nullable();
            $table->timestamp('editado_en')->useCurrent();

            $table->index('workflow_instance_id');
            $table->index('editado_por');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workflow_instance_history');
    }
};