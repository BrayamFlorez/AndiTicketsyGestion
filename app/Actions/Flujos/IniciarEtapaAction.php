<?php

namespace App\Actions\Flujos;

use App\Events\Flujos\AsignacionCreada;
use App\Models\WorkflowInstance;
use App\Models\WorkflowInstanceStep;
use App\Models\WorkflowStage;
use App\Models\WorkflowStageActor;
use App\Models\WorkflowStepAssignment;

class IniciarEtapaAction
{
    /**
     * Crea un nuevo step "en_curso" en la etapa dada, materializa una
     * asignación por cada actor configurado y actualiza etapa_actual_id.
     *
     * Se asume que se ejecuta dentro de una transacción externa.
     */
    public function ejecutar(WorkflowInstance $instance, WorkflowStage $etapa): WorkflowInstanceStep
    {
        $step = WorkflowInstanceStep::create([
            'workflow_instance_id' => $instance->id,
            'workflow_stage_id'    => $etapa->id,
            'revision'             => $instance->revision,
            'estado'               => WorkflowInstanceStep::ESTADO_EN_CURSO,
            'inicio_en'            => now(),
        ]);

        $etapa->actors()->get()->each(function (WorkflowStageActor $actor) use ($step) {
            $assignment = WorkflowStepAssignment::create([
                'workflow_instance_step_id' => $step->id,
                'workflow_stage_actor_id'   => $actor->id,
                // Si el actor es un usuario específico, pre-llenamos user_id.
                // Si es un rol, queda null hasta que alguien del rol la tome.
                'user_id'                   => $actor->esUsuario() ? (int) $actor->actor_id : null,
                'estado'                    => WorkflowStepAssignment::ESTADO_PENDIENTE,
            ]);

            event(new AsignacionCreada($assignment));
        });

        $instance->update(['etapa_actual_id' => $etapa->id]);

        return $step;
    }
}