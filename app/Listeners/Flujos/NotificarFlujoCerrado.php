<?php

namespace App\Listeners\Flujos;

use App\Events\Flujos\FlujoCerrado;
use App\Models\WorkflowInstance;
use App\Models\WorkflowInstanceStep;
use App\Models\WorkflowStepAssignment;
use App\Notifications\Flujos\FlujoAprobadoNotification;
use App\Notifications\Flujos\FlujoCanceladoNotification;
use Illuminate\Contracts\Queue\ShouldQueue;

class NotificarFlujoCerrado implements ShouldQueue
{
    public function handle(FlujoCerrado $event): void
    {
        $iniciador = $event->instance->iniciador;

        if (! $iniciador) {
            return;
        }

        $event->instance->loadMissing('documentType');

        if ($event->instance->estado === WorkflowInstance::ESTADO_APROBADO) {
            $firmantes = $this->recolectarFirmantes($event->instance);

            $iniciador->notify(new FlujoAprobadoNotification(
                $event->instance,
                $event->actor,
                $firmantes,
            ));

            return;
        }

        if ($event->instance->estado === WorkflowInstance::ESTADO_CANCELADO) {
            $iniciador->notify(new FlujoCanceladoNotification(
                $event->instance,
                $event->actor,
                $event->motivo,
            ));
        }
    }

    /**
     * Devuelve los nombres únicos de quienes firmaron aprobado en la revisión final.
     */
    private function recolectarFirmantes(WorkflowInstance $instance): array
    {
        return $instance->steps()
            ->where('revision', $instance->revision)
            ->where('estado', WorkflowInstanceStep::ESTADO_APROBADO)
            ->with(['assignments' => fn ($q) =>
                $q->where('estado', WorkflowStepAssignment::ESTADO_APROBADO)->with('user:id,name')
            ])
            ->get()
            ->flatMap(fn ($step) => $step->assignments)
            ->pluck('user.name')
            ->filter()
            ->unique()
            ->values()
            ->all();
    }
}