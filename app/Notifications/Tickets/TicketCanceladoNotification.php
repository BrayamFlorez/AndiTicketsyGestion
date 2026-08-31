<?php

namespace App\Notifications\Tickets;

use App\Models\Ticket;
use App\Models\User;
use App\Support\Flujos;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class TicketCanceladoNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Ticket $ticket,
        public User $canceladoPor,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage())
            ->subject("Cancelado: {$this->ticket->titulo}")
            ->greeting("Hola, {$notifiable->name}")
            ->line("**{$this->canceladoPor->name}** canceló un ticket que involucra a tu cuenta.")
            ->line("**Título:** {$this->ticket->titulo}")
            ->line("**Categoría:** {$this->ticket->label_categoria}")
            ->action('Ver detalle', route('tickets.show', $this->ticket))
            ->line('No requiere acción de tu parte. Si tenés dudas, contactá al administrador.');
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'tipo'           => 'ticket_cancelado',
            'mensaje'        => sprintf(
                "%s canceló \"%s\"",
                $this->canceladoPor->name,
                Str::limit($this->ticket->titulo, 60),
            ),
            'ticket_id'      => $this->ticket->id,
            'codigo'         => "TKT-{$this->ticket->id}",
            'titulo'         => $this->ticket->titulo,
            'url'            => route('tickets.show', $this->ticket),
            'icono'          => 'fa-ban',
            'color'          => 'danger',
            'actor'          => [
                'name'      => $this->canceladoPor->name,
                'iniciales' => Flujos::iniciales($this->canceladoPor->name),
                'rol'       => 'Canceló',
            ],
            'tipo_documento' => $this->ticket->label_categoria,
            'tipo_icono'     => $this->ticket->icono_categoria,
            'etapa'          => 'Cancelado',
        ];
    }
}