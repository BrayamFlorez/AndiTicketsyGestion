<?php

namespace App\Events\Tickets;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TicketAsignado
{
    use Dispatchable, SerializesModels;

    /**
     * Cubre asignación inicial y reasignación.
     * @param  Ticket  $ticket         Ticket ya con el nuevo técnico asignado
     * @param  User    $asignador      Quien ejecutó la asignación (admin, líder técnico...)
     * @param  ?User   $tecnicoAnterior Solo en reasignaciones: quien perdió el ticket
     */
    public function __construct(
        public Ticket $ticket,
        public User $asignador,
        public ?User $tecnicoAnterior = null,
    ) {}
}