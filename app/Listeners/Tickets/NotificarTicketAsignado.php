<?php

namespace App\Listeners\Tickets;

use App\Events\Tickets\TicketAsignado;
use App\Models\User;
use App\Notifications\Tickets\TicketAsignadoTecnicoNotification;
use App\Notifications\Tickets\TicketReasignadoNotification;
use Illuminate\Contracts\Queue\ShouldQueue;

class NotificarTicketAsignado implements ShouldQueue
{
    public function handle(TicketAsignado $event): void
    {
        $event->ticket->loadMissing(['solicitante', 'asignado', 'empresa', 'sede']);

        // 1. Al técnico NUEVO que recibe el ticket
        $tecnicoNuevo = $event->ticket->asignado;
        if ($tecnicoNuevo) {
            $tecnicoNuevo->notify(new TicketAsignadoTecnicoNotification(
                $event->ticket,
                $event->asignador,
            ));
        }

        // 2. Al técnico ANTERIOR (solo si es reasignación y es distinto del nuevo)
        if ($event->tecnicoAnterior
            && $tecnicoNuevo
            && $event->tecnicoAnterior->id !== $tecnicoNuevo->id
        ) {
            $event->tecnicoAnterior->notify(new TicketReasignadoNotification(
                $event->ticket,
                $event->asignador,
                $tecnicoNuevo,
            ));
        }
    }
}