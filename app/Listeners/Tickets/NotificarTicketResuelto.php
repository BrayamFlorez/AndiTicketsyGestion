<?php

namespace App\Listeners\Tickets;

use App\Events\Tickets\TicketResuelto;
use App\Notifications\Tickets\TicketResueltoNotification;
use Illuminate\Contracts\Queue\ShouldQueue;

class NotificarTicketResuelto implements ShouldQueue
{
    public function handle(TicketResuelto $event): void
    {
        $event->ticket->loadMissing(['solicitante', 'asignado']);

        $solicitante = $event->ticket->solicitante;

        if (! $solicitante || $solicitante->id === $event->ticket->asignado_a) {
            return;
        }

        $solicitante->notify(new TicketResueltoNotification($event->ticket));
    }
}