<?php

namespace App\Listeners\Tickets;

use App\Events\Tickets\TicketIniciado;
use App\Notifications\Tickets\TicketIniciadoNotification;
use Illuminate\Contracts\Queue\ShouldQueue;

class NotificarTicketIniciado implements ShouldQueue
{
    public function handle(TicketIniciado $event): void
    {
        $event->ticket->loadMissing(['solicitante', 'asignado']);

        $solicitante = $event->ticket->solicitante;

        // No notificarnos a nosotros mismos si el solicitante y el técnico son el mismo
        if (! $solicitante || $solicitante->id === $event->ticket->asignado_a) {
            return;
        }

        $solicitante->notify(new TicketIniciadoNotification($event->ticket));
    }
}