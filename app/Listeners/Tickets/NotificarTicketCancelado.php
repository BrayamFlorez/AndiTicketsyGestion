<?php

namespace App\Listeners\Tickets;

use App\Events\Tickets\TicketCancelado;
use App\Notifications\Tickets\TicketCanceladoNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;

class NotificarTicketCancelado implements ShouldQueue
{
    public function handle(TicketCancelado $event): void
    {
        $event->ticket->loadMissing(['solicitante', 'asignado']);

        $destinatarios = collect();

        // Notificar al solicitante SI no fue él quien canceló
        if ($event->ticket->solicitante
            && $event->ticket->solicitante_id !== $event->canceladoPor->id
        ) {
            $destinatarios->push($event->ticket->solicitante);
        }

        // Notificar al técnico asignado SI no fue él quien canceló
        // (aunque el controller no permite que el técnico cancele, prevengo el edge case)
        if ($event->ticket->asignado
            && $event->ticket->asignado_a !== $event->canceladoPor->id
            && $event->ticket->asignado_a !== $event->ticket->solicitante_id
        ) {
            $destinatarios->push($event->ticket->asignado);
        }

        if ($destinatarios->isEmpty()) {
            return;
        }

        Notification::send(
            $destinatarios,
            new TicketCanceladoNotification($event->ticket, $event->canceladoPor),
        );
    }
}