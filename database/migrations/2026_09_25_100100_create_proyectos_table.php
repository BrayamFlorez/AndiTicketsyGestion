<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('proyectos', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 20)->unique();                 // PRY-2026-000001
            $table->string('nombre', 150);
            $table->text('descripcion')->nullable();
            $table->foreignId('cliente_id')->constrained('clientes')->restrictOnDelete();
            $table->foreignId('responsable_id')->constrained('users')->restrictOnDelete();
            $table->string('estado', 20)->default('activo')->index(); // activo | pausado | finalizado | cancelado
            $table->date('fecha_inicio');
            $table->date('fecha_entrega_estimada')->nullable();
            $table->date('fecha_cierre')->nullable();
            $table->foreignId('creado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        // Equipo del proyecto (el responsable también queda aquí)
        Schema::create('proyecto_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proyecto_id')->constrained('proyectos')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('rol', 60)->nullable();                  // Texto libre: "Desarrollo", "Diseño"...
            $table->timestamps();

            $table->unique(['proyecto_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('proyecto_user');
        Schema::dropIfExists('proyectos');
    }
};
