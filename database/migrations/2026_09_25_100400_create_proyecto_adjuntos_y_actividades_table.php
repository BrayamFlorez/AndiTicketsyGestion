<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Evidencias/adjuntos de etapas y solicitudes (polimórfico).
        // Los archivos viven en el disco privado 'local' y se sirven por controlador.
        Schema::create('proyecto_adjuntos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proyecto_id')->constrained('proyectos')->cascadeOnDelete();
            $table->morphs('adjuntable');                            // ProyectoEtapa | ProyectoSolicitud
            $table->string('tipo', 15);                              // imagen | audio | video | documento
            $table->string('nombre_original', 255);
            $table->string('ruta', 500);
            $table->string('mime', 120)->nullable();
            $table->unsignedBigInteger('tamano')->default(0);        // bytes
            $table->foreignId('subido_por')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // Bitácora de eventos del proyecto (creación, cambios de estado, equipo, eliminaciones).
        Schema::create('proyecto_actividades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proyecto_id')->constrained('proyectos')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('accion', 40);
            $table->string('descripcion', 500);
            $table->timestamp('created_at')->useCurrent();

            $table->index(['proyecto_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('proyecto_actividades');
        Schema::dropIfExists('proyecto_adjuntos');
    }
};
