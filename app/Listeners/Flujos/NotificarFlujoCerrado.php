<?php

namespace App\Listeners\Flujos;

use App\Events\Flujos\FlujoCerrado;
use App\Models\WorkflowInstance;
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

        $notificacion = match ($event->instance->estado) {
            WorkflowInstance::ESTADO_APROBADO  => new FlujoAprobadoNotification($event->instance),
            WorkflowInstance::ESTADO_CANCELADO => new FlujoCanceladoNotification($event->instance),
            default                            => null,
        };

        if ($notificacion) {
            $iniciador->notify($notificacion);
        }
    }
}