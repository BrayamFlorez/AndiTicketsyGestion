<?php

namespace App\Listeners\Flujos;

use App\Events\Flujos\FlujoRequiereCorreccion;
use App\Models\WorkflowStepAssignment;
use App\Notifications\Flujos\FlujoRequiereCorreccionNotification;
use Illuminate\Contracts\Queue\ShouldQueue;

class NotificarFlujoRequiereCorreccion implements ShouldQueue
{
    public function handle(FlujoRequiereCorreccion $event): void
    {
        $iniciador = $event->instance->iniciador;

        if (! $iniciador) {
            return;
        }

        $event->stepRechazo->loadMissing(['stage', 'assignments.user']);
        $event->instance->loadMissing('documentType');

        // Encontrar quién rechazó/devolvió — el primero que actuó negativamente
        $decisionRechazo = $event->stepRechazo->assignments
            ->whereIn('estado', [
                WorkflowStepAssignment::ESTADO_RECHAZADO,
                WorkflowStepAssignment::ESTADO_DEVUELTO,
            ])
            ->sortBy('decidido_en')
            ->first();

        $iniciador->notify(new FlujoRequiereCorreccionNotification(
            $event->instance,
            $event->stepRechazo,
            $decisionRechazo?->user,
            $decisionRechazo?->comentario,
        ));
    }
}