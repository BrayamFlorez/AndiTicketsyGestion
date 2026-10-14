<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tickets vinculados a un equipo o a una impresora (nunca a ambos).
 * El estado pasa a string para admitir "esperando_partes" sin depender de un enum.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->string('estado', 30)->default('abierto')->change();

            $table->foreignId('equipo_id')->nullable()->after('sede_id')
                ->constrained('equipos')->nullOnDelete();
            $table->foreignId('impresora_id')->nullable()->after('equipo_id')
                ->constrained('impresoras')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropConstrainedForeignId('impresora_id');
            $table->dropConstrainedForeignId('equipo_id');
        });
    }
};
