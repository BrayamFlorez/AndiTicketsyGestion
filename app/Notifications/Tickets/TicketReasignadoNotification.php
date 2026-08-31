<?php

namespace App\Notifications\Tickets;

use App\Models\Ticket;
use App\Models\User;
use App\Support\Flujos;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class TicketReasignadoNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Ticket $ticket,
        public User $asignador,
        public ?User $tecnicoNuevo = null,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage())
            ->subject("Ticket reasignado: {$this->ticket->titulo}")
            ->greeting("Hola, {$notifiable->name}")
            ->line("El ticket **{$this->ticket->titulo}** fue reasignado por **{$this->asignador->name}**.");

        if ($this->tecnicoNuevo) {
            $mail->line("**Nuevo responsable:** {$this->tecnicoNuevo->name}");
        }

        return $mail
            ->action('Ver ticket', route('tickets.show', $this->ticket))
            ->line('Ya no es tu responsabilidad — no verás nuevas actualizaciones sobre este ticket.');
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'tipo'           => 'ticket_reasignado',
            'mensaje'        => sprintf(
                "%s reasignó \"%s\"%s",
                $this->asignador->name,
                Str::limit($this->ticket->titulo, 55),
                $this->tecnicoNuevo ? " a {$this->tecnicoNuevo->name}" : '',
            ),
            'ticket_id'      => $this->ticket->id,
            'codigo'         => "TKT-{$this->ticket->id}",
            'titulo'         => $this->ticket->titulo,
            'url'            => route('tickets.show', $this->ticket),
            'icono'          => 'fa-exchange-alt',
            'color'          => 'warning',
            'actor'          => [
                'name'      => $this->asignador->name,
                'iniciales' => Flujos::iniciales($this->asignador->name),
                'rol'       => 'Asignador',
            ],
            'tipo_documento' => $this->ticket->label_categoria,
            'tipo_icono'     => $this->ticket->icono_categoria,
            'etapa'          => 'Reasignado',
        ];
    }
}