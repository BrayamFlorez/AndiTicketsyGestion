<?php

namespace App\Notifications\Tickets;

use App\Models\Ticket;
use App\Models\User;
use App\Support\Flujos;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class TicketAsignadoTecnicoNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Ticket $ticket,
        public User $asignador,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $solicitante = $this->ticket->solicitante;
        $empresa     = $this->ticket->empresa;
        $sede        = $this->ticket->sede;

        $mail = (new MailMessage())
            ->subject("Ticket asignado: {$this->ticket->titulo}")
            ->greeting("Hola, {$notifiable->name}")
            ->line("**{$this->asignador->name}** te asignó un ticket.")
            ->line("**Título:** {$this->ticket->titulo}")
            ->line("**Categoría:** {$this->ticket->label_categoria}")
            ->line("**Prioridad:** {$this->ticket->label_prioridad}");

        if ($empresa)     $mail->line("**Empresa:** {$empresa->nombre}");
        if ($sede)        $mail->line("**Sede:** {$sede->nombre}");
        if ($solicitante) $mail->line("**Solicitante:** {$solicitante->name}");

        if ($this->ticket->descripcion) {
            $mail->line("**Descripción:**")
                 ->line("> " . Str::limit($this->ticket->descripcion, 300));
        }

        return $mail
            ->action('Abrir ticket', route('tickets.show', $this->ticket))
            ->line('Podés iniciarlo cuando estés listo. El solicitante recibirá aviso automáticamente.');
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'tipo'           => 'ticket_asignado',
            'mensaje'        => sprintf(
                "%s te asignó \"%s\"",
                $this->asignador->name,
                Str::limit($this->ticket->titulo, 60),
            ),
            'ticket_id'      => $this->ticket->id,
            'codigo'         => "TKT-{$this->ticket->id}",
            'titulo'         => $this->ticket->titulo,
            'url'            => route('tickets.show', $this->ticket),
            'icono'          => 'fa-user-check',
            'color'          => 'accent',
            'actor'          => [
                'name'      => $this->asignador->name,
                'iniciales' => Flujos::iniciales($this->asignador->name),
                'rol'       => 'Asignador',
            ],
            'tipo_documento' => $this->ticket->label_categoria,
            'tipo_icono'     => $this->ticket->icono_categoria,
            'etapa'          => $this->ticket->label_prioridad,
        ];
    }
}