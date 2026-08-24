<?php

namespace App\Observers\Auditing;

use App\Enums\DocumentAuditAction;
use App\Models\WorkflowAttachment;
use App\Models\WorkflowInstance;
use App\Services\Auditing\DocumentAuditLogger;

/**
 * Se engancha directamente al ciclo de vida de Eloquent de WorkflowAttachment,
 * así que SubirAdjuntoAction y WorkflowInstanceController::eliminarAdjunto()
 * no necesitan saber que existe auditoría: created()/deleting() se disparan
 * solos con cualquier flujo de código que use $attachment->save()/->delete(),
 * hoy o en el futuro.
 *
 * Registrado en App\Providers\AuditingServiceProvider::boot().
 */
class WorkflowAttachmentAuditObserver
{
    public function __construct(private readonly DocumentAuditLogger $logger)
    {
    }

    public function created(WorkflowAttachment $attachment): void
    {
        $instancia = $this->resolverInstancia($attachment);

        if (! $instancia) {
            return;
        }

        $this->logger->record(
            accion: DocumentAuditAction::ADJUNTO_SUBIDO,
            instancia: $instancia,
            usuario: auth()->user() ?? $attachment->uploader,
            adjunto: $attachment,
            contexto: [
                'archivo'      => $attachment->nombre_original,
                'mime_type'    => $attachment->mime_type,
                'tamano_bytes' => $attachment->tamano_bytes,
                'revision'     => $attachment->revision,
            ],
        );
    }

    /**
     * "deleting" (no "deleted") a propósito: se dispara ANTES del borrado
     * real, así que los atributos del adjunto (nombre, ruta, instancia)
     * todavía están disponibles para dejar constancia de qué se eliminó.
     */
    public function deleting(WorkflowAttachment $attachment): void
    {
        $instancia = $this->resolverInstancia($attachment);

        if (! $instancia) {
            return;
        }

        $this->logger->record(
            accion: DocumentAuditAction::ADJUNTO_ELIMINADO,
            instancia: $instancia,
            usuario: auth()->user(),
            adjunto: $attachment,
            contexto: [
                'archivo'  => $attachment->nombre_original,
                'revision' => $attachment->revision,
            ],
        );
    }

    private function resolverInstancia(WorkflowAttachment $attachment): ?WorkflowInstance
    {
        return $attachment->relationLoaded('instance')
            ? $attachment->instance
            : WorkflowInstance::find($attachment->workflow_instance_id);
    }
}
