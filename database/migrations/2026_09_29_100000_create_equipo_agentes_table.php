<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Ya existe si se ejecutó la versión anterior del módulo: tiene la misma estructura
        if (Schema::hasTable('equipo_agentes')) {
            return;
        }

        // Un agente por equipo. El token del PC se guarda solo como hash (sha256).
        Schema::create('equipo_agentes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('equipo_id')->unique()->constrained('equipos')->cascadeOnDelete();
            $table->char('token_hash', 64)->unique();

            $table->string('hostname', 100)->nullable();
            $table->string('usuario_sesion', 150)->nullable();
            $table->string('ip', 45)->nullable();
            $table->string('mac', 17)->nullable();
            $table->string('version_agente', 20)->nullable();

            $table->json('ultimo_inventario')->nullable();
            $table->timestamp('ultimo_reporte_at')->nullable()->index();
            $table->timestamp('vinculado_at')->useCurrent();
            $table->timestamp('revocado_at')->nullable();
            $table->foreignId('revocado_por')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('equipo_agentes');
    }
};
