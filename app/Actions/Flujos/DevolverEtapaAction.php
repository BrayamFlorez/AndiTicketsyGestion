<?php

namespace App\Actions\Flujos;

use App\Events\Flujos\FlujoCerrado;
use App\Models\WorkflowInstance;
use App\Models\WorkflowInstanceStep;
use App\Models\WorkflowStage;
use App\Models\WorkflowStepAssignment;

class DevolverEtapaAction
{
    public function __construct(private IniciarEtapaAction $iniciarEtapa) {}

    public function ejecutar(WorkflowInstanceStep $step, WorkflowStepAssignment $decisionRechazo): void
    {
        $etapa    = $step->stage;
        $instance = $step->instance;

        // Cerrar el step actual con el estado que corresponda
        $estadoStep = $decisionRechazo->estado === WorkflowStepAssignment::ESTADO_RECHAZADO
            ? WorkflowInstanceStep::ESTADO_RECHAZADO
            : WorkflowInstanceStep::ESTADO_DEVUELTO;

        $step->update(['estado' => $estadoStep, 'cierre_en' => now()]);

        // Marcar como devueltas las asignaciones pendientes hermanas
        // (en modos paralelos puede haber otros actores que aún no habían decidido)
        $step->assignments()
            ->where('estado', WorkflowStepAssignment::ESTADO_PENDIENTE)
            ->update([
                'estado'      => WorkflowStepAssignment::ESTADO_DEVUELTO,
                'decidido_en' => now(),
                'comentario'  => '(Cerrada por decisión de rechazo/devolución de otro actor)',
            ]);

        // Aplicar política declarada por la etapa
        match ($etapa->politica_rechazo) {
            WorkflowStage::RECHAZO_DEVOLVER_INICIADOR        => $this->devolverAIniciador($instance, $step),
            WorkflowStage::RECHAZO_DEVOLVER_ETAPA_ANTERIOR   => $this->devolverAEtapaAnterior($instance, $etapa),
            WorkflowStage::RECHAZO_DEVOLVER_ETAPA_ESPECIFICA => $this->devolverAEtapaEspecifica($instance, $etapa),
            WorkflowStage::RECHAZO_FINALIZAR                 => $this->finalizarComoCancelado($instance, $decisionRechazo),
            default                                          => $this->devolverAIniciador($instance, $step),
        };
    }

    private function devolverAIniciador(WorkflowInstance $instance, WorkflowInstanceStep $stepRechazo): void
    {
        $instance->update([
            'estado' => WorkflowInstance::ESTADO_REQUIERE_CORRECCION,
        ]);

        event(new \App\Events\Flujos\FlujoRequiereCorreccion($instance->fresh(), $stepRechazo));
    }

    private function devolverAEtapaAnterior(WorkflowInstance $instance, WorkflowStage $etapaActual): void
    {
        $anterior = WorkflowStage::where('workflow_template_id', $etapaActual->workflow_template_id)
            ->where('orden', '<', $etapaActual->orden)
            ->orderByDesc('orden')
            ->first();

        if (! $anterior) {
            // Fallback seguro: si no hay etapa anterior, va al iniciador
            $this->devolverAIniciador($instance, $etapaActual->steps()->latest('id')->first());
            return;
        }

        $instance->update(['estado' => WorkflowInstance::ESTADO_EN_PROGRESO]);
        $this->iniciarEtapa->ejecutar($instance, $anterior);
    }

    private function devolverAEtapaEspecifica(WorkflowInstance $instance, WorkflowStage $etapaActual): void
    {
        $destino = $etapaActual->etapaDestinoRechazo;

        if (! $destino) {
            $this->devolverAIniciador($instance, $etapaActual->steps()->latest('id')->first());
            return;
        }

        $instance->update(['estado' => WorkflowInstance::ESTADO_EN_PROGRESO]);
        $this->iniciarEtapa->ejecutar($instance, $destino);
    }

    private function finalizarComoCancelado(WorkflowInstance $instance, WorkflowStepAssignment $decisionRechazo): void
    {
        $instance->update([
            'estado'          => WorkflowInstance::ESTADO_CANCELADO,
            'etapa_actual_id' => null,
            'cerrado_en'      => now(),
        ]);

        // El "actor" del cierre es quien rechazó definitivamente
        $decisionRechazo->loadMissing('user');

        event(new FlujoCerrado(
            $instance->fresh(),
            $decisionRechazo->user,
            $decisionRechazo->comentario,
        ));
    }
}