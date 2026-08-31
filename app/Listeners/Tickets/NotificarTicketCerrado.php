<?php

namespace App\Listeners\Tickets;

use App\Events\Tickets\TicketCerrado;
use App\Notifications\Tickets\TicketCerradoNotification;
use Illuminate\Contracts\Queue\ShouldQueue;

class NotificarTicketCerrado implements ShouldQueue
{
    public function handle(TicketCerrado $event): void
    {
        $event->ticket->loadMissing(['asignado']);

        $tecnico = $event->ticket->asignado;

        // No notificar al técnico si es él mismo quien cierra
        if (! $tecnico || $tecnico->id === $event->cerradoPor->id) {
            return;
        }

        $tecnico->notify(new TicketCerradoNotification(
            $event->ticket,
            $event->cerradoPor,
        ));
    }
}