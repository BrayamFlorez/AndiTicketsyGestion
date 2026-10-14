<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Mantenimientos con firma del técnico y del cliente.
 * Se registran "pendiente_firma" y pasan a "finalizado" al firmar; el PDF se habilita al finalizar.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('impresora_mantenimientos', function (Blueprint $table) {
            $table->string('estado', 20)->default('pendiente_firma')->after('tipo');

            $table->string('firma_tecnico')->nullable();
            $table->timestamp('firma_tecnico_at')->nullable();

            $table->string('firma_cliente')->nullable();
            $table->string('cliente_nombre', 150)->nullable();
            $table->string('cliente_cargo', 100)->nullable();
            $table->string('cliente_documento', 30)->nullable();
            $table->timestamp('firma_cliente_at')->nullable();

            $table->timestamp('finalizado_at')->nullable();
            $table->foreignId('finalizado_por')->nullable()->constrained('users')->nullOnDelete();
        });

        // Los mantenimientos que ya existían quedan cerrados (registros anteriores sin firma)
        DB::table('impresora_mantenimientos')->update(['estado' => 'finalizado']);

        Schema::create('impresora_mantenimiento_evidencias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mantenimiento_id')->constrained('impresora_mantenimientos')->cascadeOnDelete();
            $table->string('momento', 10); // antes | despues | otra
            $table->string('archivo');
            $table->string('descripcion', 255)->nullable();
            $table->foreignId('subido_por')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['mantenimiento_id', 'momento'], 'idx_imp_mant_evid_momento');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('impresora_mantenimiento_evidencias');

        Schema::table('impresora_mantenimientos', function (Blueprint $table) {
            $table->dropConstrainedForeignId('finalizado_por');
            $table->dropColumn([
                'estado', 'firma_tecnico', 'firma_tecnico_at', 'firma_cliente', 'cliente_nombre',
                'cliente_cargo', 'cliente_documento', 'firma_cliente_at', 'finalizado_at',
            ]);
        });
    }
};
