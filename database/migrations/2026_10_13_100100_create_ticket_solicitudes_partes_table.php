<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Solicitudes de partes de un ticket. Un ticket puede esperar partes más de una vez:
 * la solicitud abierta es la que no tiene recibido_at.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ticket_solicitudes_partes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained('tickets')->cascadeOnDelete();
            $table->text('descripcion');
            $table->date('fecha_estimada')->nullable();
            $table->decimal('costo_estimado', 14, 2)->nullable();
            $table->string('evidencia')->nullable();
            $table->foreignId('solicitado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('recibido_at')->nullable();
            $table->foreignId('recibido_por')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notas_recepcion')->nullable();
            $table->timestamps();

            $table->index(['ticket_id', 'recibido_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ticket_solicitudes_partes');
    }
};
