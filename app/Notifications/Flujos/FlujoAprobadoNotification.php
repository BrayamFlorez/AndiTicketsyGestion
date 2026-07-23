<?php

namespace App\Notifications\Flujos;

use App\Models\WorkflowInstance;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class FlujoAprobadoNotification extends Notification
{
    use Queueable;

    public function __construct(public WorkflowInstance $instance) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage())
            ->subject("Aprobado: {$this->instance->codigo}")
            ->greeting("Hola, {$notifiable->name}")
            ->line("Tu trámite **{$this->instance->codigo}** fue aprobado en todas sus etapas.")
            ->line("**Trámite:** {$this->instance->titulo}")
            ->action('Ver detalle', route('flujos.instancias.show', $this->instance))
            ->line('Toda la línea de tiempo y adjuntos quedan disponibles en el detalle.');
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'tipo'        => 'flujo_aprobado',
            'instance_id' => $this->instance->id,
            'codigo'      => $this->instance->codigo,
            'titulo'      => $this->instance->titulo,
            'url'         => route('flujos.instancias.show', $this->instance),
            'mensaje'     => "Tu flujo {$this->instance->codigo} fue aprobado",
            'icono'       => 'fa-check-circle',
            'color'       => 'success',
        ];
    }
}