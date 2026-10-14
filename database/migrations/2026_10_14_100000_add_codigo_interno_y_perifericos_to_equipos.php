<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * - equipos.codigo_interno: el código o placa de inventario del cliente (aparece en el reporte).
 * - equipo_perifericos: pantalla, teclado, mouse, UPS... vinculados a un PC.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('equipos', function (Blueprint $table) {
            $table->string('codigo_interno', 60)->nullable()->after('numero_serie');
            $table->index(['empresa_id', 'codigo_interno']);
        });

        Schema::create('equipo_perifericos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('equipo_id')->constrained('equipos')->cascadeOnDelete();
            $table->string('tipo', 30);
            $table->string('marca', 100)->nullable();
            $table->string('modelo', 100)->nullable();
            $table->string('numero_serie', 100)->nullable();
            $table->string('notas', 255)->nullable();
            $table->timestamps();

            $table->index(['equipo_id', 'tipo']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('equipo_perifericos');

        Schema::table('equipos', function (Blueprint $table) {
            $table->dropIndex(['empresa_id', 'codigo_interno']);
            $table->dropColumn('codigo_interno');
        });
    }
};
