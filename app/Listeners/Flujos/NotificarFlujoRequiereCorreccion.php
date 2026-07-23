<?php

namespace App\Listeners\Flujos;

use App\Events\Flujos\FlujoRequiereCorreccion;
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

        // Cargamos las asignaciones del step de rechazo para incluir los comentarios en el mail
        $event->stepRechazo->loadMissing('stage', 'assignments');

        $iniciador->notify(new FlujoRequiereCorreccionNotification(
            $event->instance,
            $event->stepRechazo,
        ));
    }
}