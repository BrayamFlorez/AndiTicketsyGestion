<?php

namespace App\Notifications\Tickets;

use App\Models\Ticket;
use App\Models\User;
use App\Support\Flujos;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class TicketCerradoNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Ticket $ticket,
        public User $cerradoPor,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage())
            ->subject("Ticket cerrado: {$this->ticket->titulo}")
            ->greeting("Hola, {$notifiable->name}")
            ->line("**{$this->cerradoPor->name}** cerró un ticket que resolviste.")
            ->line("**Título:** {$this->ticket->titulo}")
            ->action('Ver ticket', route('tickets.show', $this->ticket))
            ->line('Buen trabajo. Este ticket ya no requiere más acción de tu parte.');
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'tipo'           => 'ticket_cerrado',
            'mensaje'        => sprintf(
                "%s cerró \"%s\"",
                $this->cerradoPor->name,
                Str::limit($this->ticket->titulo, 60),
            ),
            'ticket_id'      => $this->ticket->id,
            'codigo'         => "TKT-{$this->ticket->id}",
            'titulo'         => $this->ticket->titulo,
            'url'            => route('tickets.show', $this->ticket),
            'icono'          => 'fa-check-double',
            'color'          => 'success',
            'actor'          => [
                'name'      => $this->cerradoPor->name,
                'iniciales' => Flujos::iniciales($this->cerradoPor->name),
                'rol'       => 'Cerró',
            ],
            'tipo_documento' => $this->ticket->label_categoria,
            'tipo_icono'     => $this->ticket->icono_categoria,
            'etapa'          => 'Cerrado',
        ];
    }
}