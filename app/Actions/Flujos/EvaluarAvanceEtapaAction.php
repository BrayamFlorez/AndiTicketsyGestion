<?php

namespace App\Actions\Flujos;

use App\Models\WorkflowInstanceStep;
use App\Models\WorkflowStage;
use App\Models\WorkflowStepAssignment;

class EvaluarAvanceEtapaAction
{
    public function __construct(
        private AvanzarEtapaAction $avanzar,
        private DevolverEtapaAction $devolver,
    ) {}

    public function ejecutar(WorkflowInstanceStep $step): void
    {
        $step->refresh()->load('assignments', 'stage');

        // Prioridad 1: cualquier rechazo o devolución dispara la política de rechazo.
        $decisionRechazo = $step->assignments->first(fn (WorkflowStepAssignment $a) =>
            in_array($a->estado, [
                WorkflowStepAssignment::ESTADO_RECHAZADO,
                WorkflowStepAssignment::ESTADO_DEVUELTO,
            ], true)
        );

        if ($decisionRechazo) {
            $this->devolver->ejecutar($step, $decisionRechazo);
            return;
        }

        // Prioridad 2: ¿se cumplió la condición de avance según el modo?
        $puedeAvanzar = match ($step->stage->modo) {
            WorkflowStage::MODO_SECUENCIAL,
            WorkflowStage::MODO_PARALELO_TODOS      => $step->assignments->every(
                fn ($a) => $a->estado === WorkflowStepAssignment::ESTADO_APROBADO
            ),
            WorkflowStage::MODO_PARALELO_CUALQUIERA => $step->assignments->contains(
                fn ($a) => $a->estado === WorkflowStepAssignment::ESTADO_APROBADO
            ),
            default => false,
        };

        if ($puedeAvanzar) {
            $this->avanzar->ejecutar($step);
        }
        // Si no puede, no hacemos nada: seguimos esperando más asignaciones.
    }
}