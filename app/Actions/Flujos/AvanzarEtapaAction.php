<?php

namespace App\Actions\Flujos;

use App\Events\Flujos\EtapaAvanzada;
use App\Events\Flujos\FlujoCerrado;
use App\Models\WorkflowInstance;
use App\Models\WorkflowInstanceStep;
use App\Models\WorkflowStage;
use App\Models\WorkflowStepAssignment;

class AvanzarEtapaAction
{
    public function __construct(private IniciarEtapaAction $iniciarEtapa) {}

    public function ejecutar(WorkflowInstanceStep $step): void
    {

        $step->assignments()
        ->where('estado', WorkflowStepAssignment::ESTADO_PENDIENTE)
        ->update([
            'estado'      => WorkflowStepAssignment::ESTADO_DEVUELTO,
            'decidido_en' => now(),
            'comentario'  => '(Etapa avanzada por decisión de otro actor)',
        ]);
        
        // Cerrar step actual como aprobado
        $step->update([
            'estado'    => WorkflowInstanceStep::ESTADO_APROBADO,
            'cierre_en' => now(),
        ]);

        $instance = $step->instance;

        // Buscar la siguiente etapa por orden
        $siguienteEtapa = WorkflowStage::where('workflow_template_id', $step->stage->workflow_template_id)
            ->where('orden', '>', $step->stage->orden)
            ->orderBy('orden')
            ->first();

        if (! $siguienteEtapa) {
            $instance->update([
                'estado'          => WorkflowInstance::ESTADO_APROBADO,
                'etapa_actual_id' => null,
                'cerrado_en'      => now(),
            ]);

            // Último aprobador = quien acaba de firmar la etapa final
            $ultimoAprobador = $step->assignments()
                ->where('estado', WorkflowStepAssignment::ESTADO_APROBADO)
                ->with('user')
                ->latest('decidido_en')
                ->first()
                ?->user;

            event(new FlujoCerrado($instance->fresh(), $ultimoAprobador));
            return;
        }

        $nuevoStep = $this->iniciarEtapa->ejecutar($instance, $siguienteEtapa);

        event(new EtapaAvanzada($instance->fresh(), $step->fresh(), $nuevoStep));
    }
}