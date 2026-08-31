<?php

namespace App\Notifications\Tickets;

use App\Models\Ticket;
use App\Support\Flujos;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class TicketIniciadoNotification extends Notification
{
    use Queueable;

    public function __construct(public Ticket $ticket) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $tecnico = $this->ticket->asignado;

        $mail = (new MailMessage())
            ->subject("En progreso: {$this->ticket->titulo}")
            ->greeting("Hola, {$notifiable->name}");

        $mail->line($tecnico
            ? "**{$tecnico->name}** comenzó a trabajar en tu ticket."
            : "Se comenzó a trabajar en tu ticket.");

        $mail->line("**Título:** {$this->ticket->titulo}")
             ->action('Ver estado', route('tickets.show', $this->ticket))
             ->line('Te avisaremos automáticamente cuando esté resuelto.');

        return $mail;
    }

    public function toDatabase(object $notifiable): array
    {
        $tecnico = $this->ticket->asignado;

        return [
            'tipo'           => 'ticket_iniciado',
            'mensaje'        => $tecnico
                ? sprintf("%s comenzó a trabajar en \"%s\"", $tecnico->name, Str::limit($this->ticket->titulo, 60))
                : sprintf("Se inició trabajo en \"%s\"", Str::limit($this->ticket->titulo, 60)),
            'ticket_id'      => $this->ticket->id,
            'codigo'         => "TKT-{$this->ticket->id}",
            'titulo'         => $this->ticket->titulo,
            'url'            => route('tickets.show', $this->ticket),
            'icono'          => 'fa-sync-alt',
            'color'          => 'accent',
            'actor'          => $tecnico ? [
                'name'      => $tecnico->name,
                'iniciales' => Flujos::iniciales($tecnico->name),
                'rol'       => 'Técnico',
            ] : null,
            'tipo_documento' => $this->ticket->label_categoria,
            'tipo_icono'     => $this->ticket->icono_categoria,
            'etapa'          => 'En progreso',
        ];
    }
}