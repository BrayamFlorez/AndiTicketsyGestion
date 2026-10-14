<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('proyecto_solicitudes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proyecto_id')->constrained('proyectos')->cascadeOnDelete();
            $table->string('titulo', 150);
            $table->text('descripcion');
            $table->date('fecha_solicitud');                          // Cuándo lo pidió el cliente
            $table->string('canal', 20)->default('whatsapp');        // whatsapp | correo | llamada | reunion | presencial
            $table->string('prioridad', 10)->default('media');       // baja | media | alta
            $table->string('estado', 20)->default('pendiente')->index();
            $table->text('respuesta')->nullable();                   // Motivo de rechazo / nota de cierre

            // Etapa que resolvió la solicitud (corrección, entrega...)
            $table->foreignId('etapa_resolucion_id')->nullable()
                  ->constrained('proyecto_etapas')->nullOnDelete();

            $table->foreignId('registrado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['proyecto_id', 'estado']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('proyecto_solicitudes');
    }
};
