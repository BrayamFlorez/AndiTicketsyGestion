<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('impresoras', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 30)->unique()->comment('Autogenerado: IMP-2026-000042');
            $table->string('codigo_interno', 50)->nullable()
                ->comment('Nomenclatura de la organización: HP-CONTAB-01');

            $table->foreignId('modelo_id')->nullable()
                ->constrained('impresora_modelos')->nullOnDelete();
            $table->foreignId('empresa_id')
                ->constrained('empresas')->restrictOnDelete();
            $table->foreignId('sede_id')->nullable()
                ->constrained('sedes')->nullOnDelete();

            $table->string('nombre_asignado', 120)->nullable();
            $table->string('ubicacion_fisica', 200)->nullable();

            $table->string('ip_red', 45)->nullable();
            $table->string('hostname', 120)->nullable();
            $table->string('mac_address', 17)->nullable();

            $table->string('panel_web_usuario', 80)->nullable();
            $table->text('panel_web_password')->nullable()
                ->comment('Cifrado con Crypt de Laravel');

            $table->string('numero_serie', 80)->nullable();
            $table->date('fecha_adquisicion')->nullable();

            $table->unsignedInteger('contador_paginas_actual')->nullable();
            $table->timestamp('contador_actualizado_en')->nullable();

            $table->enum('estado', ['activa', 'en_mantenimiento', 'dada_de_baja'])->default('activa');
            $table->text('observaciones')->nullable();

            $table->foreignId('creado_por')->nullable()
                ->constrained('users')->nullOnDelete();

            $table->timestamps();

            $table->index(['empresa_id', 'sede_id']);
            $table->index('estado');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('impresoras');
    }
};