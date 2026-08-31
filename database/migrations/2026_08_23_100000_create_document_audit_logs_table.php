<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabla de auditoría documental — APPEND-ONLY por diseño.
     *
     * Decisiones intencionales (no descuidos):
     *
     * 1. Sin `updated_at`: App\Models\DocumentAuditLog declara
     *    `const UPDATED_AT = null`, porque un registro de auditoría, por
     *    definición, nunca se actualiza.
     *
     * 2. `workflow_instance_id` y `workflow_attachment_id` NO llevan
     *    restricción de llave foránea a propósito. El rastro de auditoría
     *    no debe depender del ciclo de vida de los registros de negocio
     *    que referencia: si algún día una instancia o un adjunto se purga,
     *    el registro de auditoría debe sobrevivir intacto. Por eso también
     *    se guarda una "fotografía" del documento (código y título al
     *    momento del evento) en vez de depender solo de la relación.
     *
     * 3. La inmutabilidad real se refuerza en 3 capas independientes:
     *      a) Aplicación: App\Models\DocumentAuditLog bloquea update()/delete()
     *         a nivel de instancia.
     *      b) Query builder: App\Support\Auditing\ImmutableAuditQueryBuilder
     *         bloquea updates/deletes masivos hechos por query builder.
     *      c) Base de datos: triggers creados en la migración siguiente
     *         (2026_08_23_100001_...) que rechazan UPDATE/DELETE incluso
     *         si alguien se conecta directo a la base de datos.
     */
    public function up(): void
    {
        Schema::create('document_audit_logs', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('workflow_instance_id');
            $table->unsignedBigInteger('workflow_attachment_id')->nullable();

            $table->string('accion', 40);

            // Identidad del actor: id + fotografía de nombre/correo, para que
            // el rastro siga siendo legible aunque el usuario sea eliminado
            // o renombrado más adelante.
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('user_name')->nullable();
            $table->string('user_email')->nullable();

            // Fotografía del documento al momento del evento.
            $table->string('documento_codigo')->nullable();
            $table->string('documento_titulo')->nullable();

            // Contexto forense de la petición.
            $table->string('ip_address', 45)->nullable(); // 45 = largo máximo de una IPv6
            $table->text('user_agent')->nullable();
            $table->string('metodo_http', 10)->nullable();
            $table->string('url', 2048)->nullable();

            // Detalle específico de cada acción (comentario de rechazo,
            // nombre de archivo descargado, campos editados, etc.).
            $table->json('contexto')->nullable();

            $table->timestamp('created_at')->useCurrent();

            $table->index(['workflow_instance_id', 'created_at'], 'idx_dal_instancia_fecha');
            $table->index('workflow_attachment_id', 'idx_dal_adjunto');
            $table->index('accion', 'idx_dal_accion');
            $table->index('user_id', 'idx_dal_usuario');
            $table->index('documento_codigo', 'idx_dal_codigo');
            $table->index('ip_address', 'idx_dal_ip');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_audit_logs');
    }
};
