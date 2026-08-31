<?php

namespace App\Events\Tickets;

use App\Models\Ticket;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TicketResuelto
{
    use Dispatchable, SerializesModels;

    public function __construct(public Ticket $ticket) {}
}