<?php

namespace App\Notifications\Flujos;

use App\Models\WorkflowInstance;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class FlujoCanceladoNotification extends Notification
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
            ->subject("Cancelado: {$this->instance->codigo}")
            ->greeting("Hola, {$notifiable->name}")
            ->line("El trámite **{$this->instance->codigo}** fue cancelado.")
            ->line("**Trámite:** {$this->instance->titulo}")
            ->action('Ver detalle', route('flujos.instancias.show', $this->instance))
            ->line('Si necesitás retomarlo, un administrador puede reabrirlo.');
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'tipo'        => 'flujo_cancelado',
            'instance_id' => $this->instance->id,
            'codigo'      => $this->instance->codigo,
            'titulo'      => $this->instance->titulo,
            'url'         => route('flujos.instancias.show', $this->instance),
            'mensaje'     => "El flujo {$this->instance->codigo} fue cancelado",
            'icono'       => 'fa-ban',
            'color'       => 'danger',
        ];
    }
}