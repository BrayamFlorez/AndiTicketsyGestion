<?php

namespace App\Events\Tickets;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TicketCancelado
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public Ticket $ticket,
        public User $canceladoPor,
    ) {}
}