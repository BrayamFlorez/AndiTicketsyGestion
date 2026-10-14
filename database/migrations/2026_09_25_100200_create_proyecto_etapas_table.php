<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Cada fila es un REGISTRO de etapa (no una etapa fija): un proyecto puede tener
 * varias entregas, varias rondas de corrección, varios pagos... en cualquier orden.
 * Los campos opcionales solo aplican a ciertos tipos (ver ProyectoEtapa::CAMPOS_POR_TIPO).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('proyecto_etapas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proyecto_id')->constrained('proyectos')->cascadeOnDelete();
            $table->string('tipo', 30);                              // levantamiento, cotizacion, pago, entrega...
            $table->string('titulo', 150);
            $table->text('descripcion');
            $table->date('fecha');                                   // Fecha real del hecho (puede ser pasada)
            $table->string('estado', 20)->default('completada');     // planeada | en_curso | completada

            // Campos específicos por tipo
            $table->decimal('monto', 14, 2)->nullable();             // cotizacion, pago
            $table->date('fecha_validez')->nullable();               // cotizacion
            $table->string('medio_pago', 30)->nullable();            // pago
            $table->string('version', 30)->nullable();               // entrega
            $table->unsignedSmallInteger('ronda')->nullable();       // correccion
            $table->unsignedTinyInteger('avance')->nullable();       // desarrollo (0-100)
            $table->string('enlace', 500)->nullable();               // entrega, desarrollo, diseño, pruebas

            $table->foreignId('registrado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['proyecto_id', 'tipo']);
            $table->index(['proyecto_id', 'fecha']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('proyecto_etapas');
    }
};
