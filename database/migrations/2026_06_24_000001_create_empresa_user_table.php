<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Pivot empresa_user — asigna técnicos a empresas Y opcionalmente a sedes.
 *
 * Semántica de sede_id:
 *  - NULL  → el técnico cubre TODA la empresa (todas sus sedes)
 *  - X     → el técnico cubre solo la sede X de esa empresa
 *
 * Un mismo técnico puede tener varias filas para una empresa,
 * una por cada sede que cubre. O una sola con sede_id=NULL si cubre todo.
 *
 * Nota sobre users.empresa_id / users.sede_id:
 *  - Se conservan: indican dónde TRABAJA el usuario (empleo).
 *  - Esta pivot es para ASIGNACIONES de soporte técnico (cobertura).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('empresa_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('empresa_id')->constrained()->cascadeOnDelete();

            // NULL = asignacion a TODA la empresa
            // X    = asignacion solo a la sede X
            $table->foreignId('sede_id')->nullable()->constrained('sedes')->cascadeOnDelete();

            $table->timestamp('asignado_at')->useCurrent();
            $table->timestamps();

            // Evita duplicados exactos (mismo user+empresa+sede)
            // Nota: MySQL trata NULLs como distintos, asi que el caso
            // (user, empresa, NULL) puede repetirse y se valida a nivel app.
            $table->unique(['user_id', 'empresa_id', 'sede_id'], 'empresa_user_asignacion_unique');

            // Indices para queries comunes
            $table->index(['user_id', 'empresa_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('empresa_user');
    }
};