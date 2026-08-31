<?php

namespace App\Notifications\Tickets;

use App\Models\Ticket;
use App\Support\Flujos;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class TicketResueltoNotification extends Notification
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
            ->subject("Resuelto: {$this->ticket->titulo}")
            ->greeting("Hola, {$notifiable->name}");

        $mail->line($tecnico
            ? "**{$tecnico->name}** marcó tu ticket como resuelto."
            : "Tu ticket fue marcado como resuelto.");

        $mail->line("**Título:** {$this->ticket->titulo}");

        if ($this->ticket->notas_resolucion) {
            $mail->line("**Notas de resolución:**")
                 ->line("> " . Str::limit($this->ticket->notas_resolucion, 500));
        }

        return $mail
            ->action('Revisar y cerrar', route('tickets.show', $this->ticket))
            ->line('Si la solución es satisfactoria, podés cerrar el ticket desde el detalle. Si no, contactá al técnico o a un administrador.');
    }

    public function toDatabase(object $notifiable): array
    {
        $tecnico = $this->ticket->asignado;

        return [
            'tipo'           => 'ticket_resuelto',
            'mensaje'        => $tecnico
                ? sprintf("%s resolvió \"%s\"", $tecnico->name, Str::limit($this->ticket->titulo, 60))
                : sprintf("Se resolvió \"%s\"", Str::limit($this->ticket->titulo, 60)),
            'ticket_id'      => $this->ticket->id,
            'codigo'         => "TKT-{$this->ticket->id}",
            'titulo'         => $this->ticket->titulo,
            'url'            => route('tickets.show', $this->ticket),
            'icono'          => 'fa-check-circle',
            'color'          => 'success',
            'actor'          => $tecnico ? [
                'name'      => $tecnico->name,
                'iniciales' => Flujos::iniciales($tecnico->name),
                'rol'       => 'Técnico',
            ] : null,
            'tipo_documento' => $this->ticket->label_categoria,
            'tipo_icono'     => $this->ticket->icono_categoria,
            'etapa'          => 'Resuelto',
            'comentario'     => $this->ticket->notas_resolucion,
        ];
    }
}