<?php

namespace App\Events\Tickets;

use App\Models\Ticket;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TicketCreado
{
    use Dispatchable, SerializesModels;

    /**
     * Se dispara cuando un ticket se crea SIN asignación.
     * El destinatario típico son los líderes técnicos para que asignen.
     */
    public function __construct(public Ticket $ticket) {}
}