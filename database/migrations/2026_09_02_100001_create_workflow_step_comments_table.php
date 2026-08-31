<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('workflow_step_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workflow_instance_step_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('comentario');
            $table->timestamps();

            $table->index('workflow_instance_step_id');
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workflow_step_comments');
    }
};