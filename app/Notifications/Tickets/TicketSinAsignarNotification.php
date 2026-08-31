<?php

namespace App\Notifications\Tickets;

use App\Models\Ticket;
use App\Support\Flujos;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class TicketSinAsignarNotification extends Notification
{
    use Queueable;

    public function __construct(public Ticket $ticket) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $solicitante = $this->ticket->solicitante;
        $empresa     = $this->ticket->empresa;
        $url         = route('tickets.show', $this->ticket);

        $mail = (new MailMessage())
            ->subject("Nuevo ticket sin asignar: {$this->ticket->titulo}")
            ->greeting("Hola, {$notifiable->name}");

        $mail->line($solicitante
            ? "**{$solicitante->name}** creó un nuevo ticket que necesita asignación."
            : "Se creó un ticket que necesita asignación.");

        $mail->line("**Título:** {$this->ticket->titulo}");
        $mail->line("**Categoría:** {$this->ticket->label_categoria}");
        $mail->line("**Prioridad:** {$this->ticket->label_prioridad}");
        if ($empresa) $mail->line("**Empresa:** {$empresa->nombre}");

        if ($this->ticket->descripcion) {
            $mail->line("**Descripción:**")
                 ->line("> " . Str::limit($this->ticket->descripcion, 300));
        }

        return $mail
            ->action('Ver y asignar', $url)
            ->line('Podés asignarlo a un técnico con cobertura disponible desde el detalle del ticket.');
    }

    public function toDatabase(object $notifiable): array
    {
        $solicitante = $this->ticket->solicitante;

        $mensaje = $solicitante
            ? sprintf(
                "%s creó un ticket sin asignar: \"%s\"",
                $solicitante->name,
                Str::limit($this->ticket->titulo, 60),
            )
            : sprintf("Nuevo ticket sin asignar: \"%s\"", Str::limit($this->ticket->titulo, 60));

        return [
            'tipo'           => 'ticket_sin_asignar',
            'mensaje'        => $mensaje,
            'ticket_id'      => $this->ticket->id,
            'codigo'         => "TKT-{$this->ticket->id}",
            'titulo'         => $this->ticket->titulo,
            'url'            => route('tickets.show', $this->ticket),
            'icono'          => 'fa-inbox',
            'color'          => 'warning',
            'actor'          => $solicitante ? [
                'name'      => $solicitante->name,
                'iniciales' => Flujos::iniciales($solicitante->name),
                'rol'       => 'Solicitante',
            ] : null,
            'tipo_documento' => $this->ticket->label_categoria,
            'tipo_icono'     => $this->ticket->icono_categoria,
            'etapa'          => $this->ticket->label_prioridad,
        ];
    }
}