<?php

namespace App\Observers\Auditing;

use App\Enums\DocumentAuditAction;
use App\Models\WorkflowInstance;
use App\Services\Auditing\DocumentAuditLogger;
use Illuminate\Support\Collection;

/**
 * Se engancha al ciclo de vida de Eloquent de WorkflowInstance para cubrir
 * dos cosas que ningún evento de dominio existente cubre hoy:
 *
 *  1) Transiciones de estado sin evento propio: "corrección reenviada"
 *     (EnviarCorreccionAction) y "flujo reabierto" (ReabrirFlujoAction) no
 *     disparan ningún evento en el código actual. En vez de modificar esas
 *     dos Actions, este observer detecta la transición de `estado` que cada
 *     una produce y la traduce a la acción de auditoría correspondiente.
 *
 *  2) Ediciones directas del expediente (título, descripción, etc.): hoy no
 *     existe una funcionalidad de "editar documento" en la aplicación, pero
 *     si se agrega en el futuro (o si alguien corrige un dato por Tinker/
 *     soporte), queda registrada automáticamente sin tocar ningún
 *     controlador nuevo.
 *
 * Los campos que pertenecen a la máquina de estados del motor de flujos
 * (estado, etapa_actual_id, revision, cerrado_en, codigo) se excluyen del
 * diff genérico para no duplicar lo que ya cubren los eventos semánticos
 * de Flujos (vía DocumentAuditSubscriber).
 */
class WorkflowInstanceAuditObserver
{
    private const CAMPOS_EXCLUIDOS_DE_EDICION = [
        'codigo',
        'estado',
        'etapa_actual_id',
        'revision',
        'cerrado_en',
        'updated_at',
        'created_at',
    ];

    public function __construct(private readonly DocumentAuditLogger $logger)
    {
    }

    public function updated(WorkflowInstance $instancia): void
    {
        if ($instancia->wasChanged('estado')) {
            $this->registrarTransicionEspecial($instancia);
        }

        $this->registrarEdicionGenerica($instancia);
    }

    /**
     * Detecta, por la transición de origen → destino, las dos acciones que
     * hoy no tienen evento de dominio propio. Cualquier otra transición de
     * estado (aprobar, rechazar, cancelar, devolver...) ya la cubre
     * DocumentAuditSubscriber a través de los eventos existentes de Flujos,
     * así que aquí se ignora deliberadamente.
     */
    private function registrarTransicionEspecial(WorkflowInstance $instancia): void
    {
        $anterior = $instancia->getOriginal('estado');
        $actual   = $instancia->estado;

        $accion = match (true) {
            $anterior === WorkflowInstance::ESTADO_REQUIERE_CORRECCION
                && $actual === WorkflowInstance::ESTADO_EN_PROGRESO
                    => DocumentAuditAction::CORRECCION_REENVIADA,

            $anterior === WorkflowInstance::ESTADO_CANCELADO
                && $actual === WorkflowInstance::ESTADO_EN_PROGRESO
                    => DocumentAuditAction::FLUJO_REABIERTO,

            default => null,
        };

        if (! $accion) {
            return;
        }

        $this->logger->record(
            accion: $accion,
            instancia: $instancia,
            usuario: auth()->user(),
            contexto: ['revision' => $instancia->revision],
        );
    }

    private function registrarEdicionGenerica(WorkflowInstance $instancia): void
    {
        $cambios = Collection::make($instancia->getChanges())
            ->except(self::CAMPOS_EXCLUIDOS_DE_EDICION);

        if ($cambios->isEmpty()) {
            return;
        }

        $this->logger->record(
            accion: DocumentAuditAction::EDITADO,
            instancia: $instancia,
            usuario: auth()->user(),
            contexto: [
                'campos_modificados' => $cambios->keys()->all(),
                'valores_anteriores' => Collection::make($instancia->getOriginal())
                    ->only($cambios->keys())
                    ->all(),
                'valores_nuevos' => $cambios->all(),
            ],
        );
    }
}
