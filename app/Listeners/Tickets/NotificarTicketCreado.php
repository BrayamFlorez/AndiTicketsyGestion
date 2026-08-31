<?php

namespace App\Listeners\Tickets;

use App\Events\Tickets\TicketCreado;
use App\Models\User;
use App\Notifications\Tickets\TicketSinAsignarNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;

class NotificarTicketCreado implements ShouldQueue
{
    public function handle(TicketCreado $event): void
    {
        $event->ticket->loadMissing(['solicitante', 'empresa']);

        // Todos los usuarios con rol líder-técnico reciben la alerta de "sin asignar"
        $destinatarios = User::role('lider-tecnico')->get();

        if ($destinatarios->isEmpty()) {
            return;
        }

        Notification::send(
            $destinatarios,
            new TicketSinAsignarNotification($event->ticket),
        );
    }
}