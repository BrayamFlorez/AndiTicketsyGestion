<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * El mapeo de eventos y listeners para la aplicación.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        \App\Events\Flujos\AsignacionCreada::class => [
            \App\Listeners\Flujos\NotificarAsignacionPendiente::class,
        ],
        \App\Events\Flujos\FlujoRequiereCorreccion::class => [
            \App\Listeners\Flujos\NotificarFlujoRequiereCorreccion::class,
        ],
        \App\Events\Flujos\FlujoCerrado::class => [
            \App\Listeners\Flujos\NotificarFlujoCerrado::class,
        ],
        \App\Events\Tickets\TicketCreado::class => [
            \App\Listeners\Tickets\NotificarTicketCreado::class,
        ],
        \App\Events\Tickets\TicketAsignado::class => [
            \App\Listeners\Tickets\NotificarTicketAsignado::class,
        ],
        \App\Events\Tickets\TicketIniciado::class => [
            \App\Listeners\Tickets\NotificarTicketIniciado::class,
        ],
        \App\Events\Tickets\TicketResuelto::class => [
            \App\Listeners\Tickets\NotificarTicketResuelto::class,
        ],
        \App\Events\Tickets\TicketCerrado::class => [
            \App\Listeners\Tickets\NotificarTicketCerrado::class,
        ],
        \App\Events\Tickets\TicketCancelado::class => [
            \App\Listeners\Tickets\NotificarTicketCancelado::class,
        ],
    ];

    /**
     * Registrar cualquier evento para tu aplicación.
     */
    public function boot(): void
    {
        parent::boot();
    }

    /**
     * Determina si los eventos y listeners deben ser descubiertos automáticamente.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}