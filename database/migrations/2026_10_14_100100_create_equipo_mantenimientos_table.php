<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Servicio técnico a un PC (preventivo o correctivo), con los campos del formato
 * "Contrato de prestación de servicio": unidades revisadas, cambio de partes, fotos y firmas.
 * Reemplaza al módulo anterior de mantenimiento preventivo.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('equipo_mantenimientos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('equipo_id')->constrained('equipos')->cascadeOnDelete();
            $table->foreignId('ticket_id')->nullable()->constrained('tickets')->nullOnDelete(); // orden de trabajo

            $table->string('tipo', 20);                 // preventivo | correctivo
            $table->string('modalidad', 40)->nullable(); // contrato de arrendamiento, garantía...
            $table->string('deposito', 100)->nullable();
            $table->string('estado', 20)->default('pendiente_firma');

            $table->date('fecha_ingreso')->nullable();
            $table->date('fecha_servicio');
            $table->time('hora_inicio')->nullable();
            $table->time('hora_fin')->nullable();
            $table->string('contador_inicial', 50)->nullable();
            $table->string('contador_final', 50)->nullable();

            $table->foreignId('tecnico_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('falla');
            $table->text('observaciones')->nullable();

            // Firmas (salida del cliente y técnico)
            $table->string('firma_tecnico')->nullable();
            $table->timestamp('firma_tecnico_at')->nullable();
            $table->string('firma_cliente')->nullable();
            $table->string('cliente_nombre', 150)->nullable();
            $table->string('cliente_cargo', 100)->nullable();
            $table->string('cliente_documento', 30)->nullable();
            $table->timestamp('firma_cliente_at')->nullable();

            $table->timestamp('finalizado_at')->nullable();
            $table->foreignId('finalizado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('registrado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['equipo_id', 'fecha_servicio']);
            $table->index(['tecnico_id', 'estado']);
        });

        // Unidades revisadas: componentes del PC, periféricos o filas agregadas a mano
        Schema::create('equipo_mantenimiento_unidades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mantenimiento_id')->constrained('equipo_mantenimientos')->cascadeOnDelete();
            $table->foreignId('periferico_id')->nullable()->constrained('equipo_perifericos')->nullOnDelete();
            $table->unsignedSmallInteger('orden')->default(0);
            $table->string('unidad', 150);
            $table->boolean('limpieza')->default(false);
            $table->boolean('ajuste')->default(false);
            $table->boolean('lubricacion')->default(false);
            $table->boolean('cambio_instalacion')->default(false);
            $table->boolean('reparacion')->default(false);
            $table->boolean('revision')->default(false);
        });

        Schema::create('equipo_mantenimiento_partes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mantenimiento_id')->constrained('equipo_mantenimientos')->cascadeOnDelete();
            $table->string('numero_parte', 60)->nullable();
            $table->string('nombre', 150);
            $table->string('serial_original', 100)->nullable();
            $table->string('serial_reemplazo', 100)->nullable();
        });

        Schema::create('equipo_mantenimiento_evidencias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mantenimiento_id')->constrained('equipo_mantenimientos')->cascadeOnDelete();
            $table->string('momento', 10); // antes | despues | otra
            $table->string('archivo');
            $table->string('descripcion', 255)->nullable();
            $table->foreignId('subido_por')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['mantenimiento_id', 'momento']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('equipo_mantenimiento_evidencias');
        Schema::dropIfExists('equipo_mantenimiento_partes');
        Schema::dropIfExists('equipo_mantenimiento_unidades');
        Schema::dropIfExists('equipo_mantenimientos');
    }
};
