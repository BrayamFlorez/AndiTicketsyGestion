<?php

namespace App\Listeners\Auditing;

use App\Enums\DocumentAuditAction;
use App\Events\Auditing\AdjuntoDescargado;
use App\Events\Auditing\DocumentoVisualizado;
use App\Events\Flujos\AsignacionActuada;
use App\Events\Flujos\EtapaAvanzada;
use App\Events\Flujos\FlujoCerrado;
use App\Events\Flujos\FlujoIniciado;
use App\Events\Flujos\FlujoRequiereCorreccion;
use App\Models\WorkflowInstance;
use App\Models\WorkflowStepAssignment;
use App\Services\Auditing\DocumentAuditLogger;
use Illuminate\Events\Dispatcher;

/**
 * Traduce los eventos de dominio que YA dispara el motor de flujos (más los
 * dos eventos nuevos de visualización/descarga) en entradas de auditoría.
 *
 * Este es el ÚNICO lugar donde se interpreta "qué significa" cada evento
 * de negocio en términos de auditoría. El motor de flujos (app/Actions/Flujos,
 * app/Http/Controllers/WorkflowInstanceController) no sabe que este
 * subscriber existe: solo sigue disparando los eventos que ya disparaba
 * antes de esta mejora.
 *
 * Registrado en App\Providers\AuditingServiceProvider::boot() vía
 * Event::subscribe(), NO en App\Providers\EventServiceProvider, para que
 * el módulo de auditoría no requiera tocar ningún archivo de arranque
 * compartido con el resto de la aplicación.
 */
class DocumentAuditSubscriber
{
    public function __construct(private readonly DocumentAuditLogger $logger)
    {
    }

    /**
     * @return array<class-string, string>
     */
    public function subscribe(Dispatcher $events): array
    {
        return [
            FlujoIniciado::class           => 'manejarFlujoIniciado',
            AsignacionActuada::class       => 'manejarAsignacionActuada',
            EtapaAvanzada::class           => 'manejarEtapaAvanzada',
            FlujoRequiereCorreccion::class => 'manejarFlujoRequiereCorreccion',
            FlujoCerrado::class            => 'manejarFlujoCerrado',
            DocumentoVisualizado::class    => 'manejarDocumentoVisualizado',
            AdjuntoDescargado::class       => 'manejarAdjuntoDescargado',
        ];
    }

    public function manejarFlujoIniciado(FlujoIniciado $event): void
    {
        $instancia = $event->instance->loadMissing(['iniciador', 'template', 'documentType']);

        $this->logger->record(
            accion: DocumentAuditAction::CREADO,
            instancia: $instancia,
            usuario: $instancia->iniciador,
            contexto: array_filter([
                'plantilla'      => $instancia->template?->nombre,
                'tipo_documento' => $instancia->documentType?->nombre,
            ]),
        );
    }

    public function manejarAsignacionActuada(AsignacionActuada $event): void
    {
        $assignment = $event->assignment->loadMissing(['step.instance', 'step.stage', 'user']);
        $instancia  = $assignment->step?->instance;

        if (! $instancia) {
            return;
        }

        $accion = match ($assignment->estado) {
            WorkflowStepAssignment::ESTADO_APROBADO  => DocumentAuditAction::APROBADO,
            WorkflowStepAssignment::ESTADO_RECHAZADO => DocumentAuditAction::RECHAZADO,
            WorkflowStepAssignment::ESTADO_DEVUELTO  => DocumentAuditAction::DEVUELTO,
            default                                  => null,
        };

        if (! $accion) {
            return;
        }

        $this->logger->record(
            accion: $accion,
            instancia: $instancia,
            usuario: $assignment->user,
            contexto: array_filter([
                'etapa'      => $assignment->step?->stage?->nombre,
                'comentario' => $assignment->comentario,
            ]),
        );
    }

    public function manejarEtapaAvanzada(EtapaAvanzada $event): void
    {
        $this->logger->record(
            accion: DocumentAuditAction::ETAPA_AVANZADA,
            instancia: $event->instance,
            usuario: auth()->user(),
            contexto: array_filter([
                'etapa_anterior' => $event->stepAnterior->stage?->nombre,
                'etapa_nueva'    => $event->stepNuevo->stage?->nombre,
            ]),
        );
    }

    public function manejarFlujoRequiereCorreccion(FlujoRequiereCorreccion $event): void
    {
        $this->logger->record(
            accion: DocumentAuditAction::CORRECCION_SOLICITADA,
            instancia: $event->instance,
            usuario: auth()->user(),
            contexto: array_filter([
                'etapa_rechazo' => $event->stepRechazo->stage?->nombre,
            ]),
        );
    }

    public function manejarFlujoCerrado(FlujoCerrado $event): void
    {
        $accion = $event->instance->estado === WorkflowInstance::ESTADO_CANCELADO
            ? DocumentAuditAction::FLUJO_CANCELADO
            : DocumentAuditAction::FLUJO_FINALIZADO;

        $this->logger->record(
            accion: $accion,
            instancia: $event->instance,
            usuario: $event->actor,
            contexto: array_filter([
                'motivo' => $event->motivo,
            ]),
        );
    }

    public function manejarDocumentoVisualizado(DocumentoVisualizado $event): void
    {
        $this->logger->record(
            accion: DocumentAuditAction::VISUALIZADO,
            instancia: $event->instancia,
            usuario: $event->usuario,
        );
    }

    public function manejarAdjuntoDescargado(AdjuntoDescargado $event): void
    {
        $this->logger->record(
            accion: DocumentAuditAction::DESCARGADO,
            instancia: $event->instancia,
            usuario: $event->usuario,
            adjunto: $event->adjunto,
            contexto: [
                'archivo' => $event->adjunto->nombre_original,
            ],
        );
    }
}
