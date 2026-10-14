<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('impresora_modelos', function (Blueprint $table) {
            $table->id();
            $table->string('marca', 80);
            $table->string('modelo', 120);
            $table->enum('tipo_tecnologia', ['laser', 'inyeccion', 'termica', 'matricial'])
                ->default('laser');
            $table->boolean('color')->default(false);
            $table->boolean('es_multifuncional')->default(false);
            $table->json('consumibles_sugeridos')->nullable()
                ->comment('Ej: [{"tipo":"toner","color":"negro"},{"tipo":"toner","color":"cyan"}]');
            $table->boolean('activo')->default(true);
            $table->timestamps();

            $table->unique(['marca', 'modelo'], 'uq_marca_modelo');
            $table->index('activo');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('impresora_modelos');
    }
};