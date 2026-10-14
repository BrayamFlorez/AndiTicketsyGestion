<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Llave del agente por empresa. Reemplaza el código corto de la versión anterior.
 *  - agente_llave_hash:     sha256 de la llave, para buscar la empresa sin guardarla en claro
 *  - agente_llave_cifrada:  la llave cifrada con APP_KEY, para volver a mostrarla al administrador
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('empresas', 'codigo_agente')) {
            Schema::table('empresas', function (Blueprint $table) {
                $table->dropUnique(['codigo_agente']);
                $table->dropColumn('codigo_agente');
            });
        }

        Schema::table('empresas', function (Blueprint $table) {
            $table->char('agente_llave_hash', 64)->nullable()->unique();
            $table->text('agente_llave_cifrada')->nullable();
            $table->timestamp('agente_llave_generada_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('empresas', function (Blueprint $table) {
            $table->dropUnique(['agente_llave_hash']);
            $table->dropColumn(['agente_llave_hash', 'agente_llave_cifrada', 'agente_llave_generada_at']);
        });
    }
};
