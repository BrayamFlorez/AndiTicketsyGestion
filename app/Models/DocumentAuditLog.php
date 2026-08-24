<?php

namespace App\Models;

use App\Enums\DocumentAuditAction;
use App\Exceptions\Auditing\ImmutableAuditLogException;
use App\Support\Auditing\ImmutableAuditQueryBuilder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Registro de auditoría documental — de SOLO INSERCIÓN.
 *
 * Este modelo es intencionalmente el único punto de escritura permitido
 * hacia `document_audit_logs`, y únicamente a través de
 * App\Services\Auditing\DocumentAuditLogger. Nadie más en la aplicación
 * debería llamar a DocumentAuditLog::create() directamente; hacerlo sigue
 * siendo válido (la inmutabilidad se refuerza DESPUÉS de la creación, no
 * en la creación misma), pero pasar por el logger asegura que IP, user
 * agent y demás metadatos de contexto queden siempre completos.
 *
 * Inmutabilidad reforzada en 3 capas independientes:
 *   1) Aquí: los eventos updating/deleting del modelo lanzan excepción.
 *   2) App\Support\Auditing\ImmutableAuditQueryBuilder: bloquea
 *      updates/deletes masivos hechos por query builder.
 *   3) Base de datos: triggers creados en la migración
 *      2026_08_23_100001_add_immutability_triggers_to_document_audit_logs_table
 *      que rechazan UPDATE/DELETE aunque alguien se conecte directo a la BD.
 *
 * @property int $id
 * @property int $workflow_instance_id
 * @property int|null $workflow_attachment_id
 * @property DocumentAuditAction $accion
 * @property int|null $user_id
 * @property string|null $user_name
 * @property string|null $user_email
 * @property string|null $documento_codigo
 * @property string|null $documento_titulo
 * @property string|null $ip_address
 * @property string|null $user_agent
 * @property string|null $metodo_http
 * @property string|null $url
 * @property array|null $contexto
 * @property \Illuminate\Support\Carbon $created_at
 */
class DocumentAuditLog extends Model
{
    protected $table = 'document_audit_logs';

    /**
     * No existe columna updated_at (ver migración): un registro de auditoría
     * nunca se actualiza, así que Laravel no debe intentar mantenerla.
     */
    const UPDATED_AT = null;

    protected $fillable = [
        'workflow_instance_id',
        'workflow_attachment_id',
        'accion',
        'user_id',
        'user_name',
        'user_email',
        'documento_codigo',
        'documento_titulo',
        'ip_address',
        'user_agent',
        'metodo_http',
        'url',
        'contexto',
    ];

    protected function casts(): array
    {
        return [
            'accion'     => DocumentAuditAction::class,
            'contexto'   => 'array',
            'created_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::updating(function (): void {
            throw new ImmutableAuditLogException(
                'document_audit_logs es de solo inserción: no se permite modificar un registro existente.'
            );
        });

        static::deleting(function (): void {
            throw new ImmutableAuditLogException(
                'document_audit_logs es de solo inserción: no se permite eliminar un registro de auditoría.'
            );
        });
    }

    /** @param \Illuminate\Database\Query\Builder $query */
    public function newEloquentBuilder($query): Builder
    {
        return new ImmutableAuditQueryBuilder($query);
    }

    /**
     * Relación de SOLO LECTURA. A propósito no lleva llave foránea real
     * (ver migración): el rastro de auditoría no debe depender del ciclo
     * de vida del registro de negocio que referencia.
     */
    public function instancia(): BelongsTo
    {
        return $this->belongsTo(WorkflowInstance::class, 'workflow_instance_id');
    }

    public function adjunto(): BelongsTo
    {
        return $this->belongsTo(WorkflowAttachment::class, 'workflow_attachment_id');
    }
}
