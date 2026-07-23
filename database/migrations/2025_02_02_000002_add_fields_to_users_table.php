<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Empresa a la que pertenece el usuario (principalmente rol: usuario final)
            // Nullable: admins y super-admins no pertenecen a una empresa específica
            $table->foreignId('empresa_id')
                ->nullable()
                ->after('remember_token')
                ->constrained('empresas')
                ->nullOnDelete();

            // Sede específica (opcional, refinamiento del usuario final)
            $table->foreignId('sede_id')
                ->nullable()
                ->after('empresa_id')
                ->constrained('sedes')
                ->nullOnDelete();

            // Control de acceso sin necesidad de eliminar el registro
            $table->boolean('activo')
                ->default(true)
                ->after('sede_id');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('empresa_id');
            $table->dropConstrainedForeignId('sede_id');
            $table->dropColumn('activo');
        });
    }
};
