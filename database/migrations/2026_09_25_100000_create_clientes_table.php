<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Clientes de proyectos.
 * Tabla independiente de `empresas` (empresas = organizaciones a las que se da soporte;
 * clientes = a quienes se les desarrollan proyectos).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clientes', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 150);
            $table->string('tipo_documento', 10)->default('nit');   // nit | cc | ce | otro
            $table->string('numero_documento', 30)->nullable()->unique();
            $table->string('contacto_nombre', 120)->nullable();
            $table->string('contacto_cargo', 80)->nullable();
            $table->string('telefono', 30)->nullable();
            $table->string('email', 150)->nullable();
            $table->string('direccion', 200)->nullable();
            $table->string('ciudad', 80)->nullable();
            $table->text('observaciones')->nullable();
            $table->boolean('activo')->default(true)->index();
            $table->foreignId('creado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index('nombre');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clientes');
    }
};
