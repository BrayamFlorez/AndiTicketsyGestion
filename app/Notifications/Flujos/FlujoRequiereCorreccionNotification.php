<?php

namespace App\Notifications\Flujos;

use App\Models\WorkflowInstance;
use App\Models\WorkflowInstanceStep;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class FlujoRequiereCorreccionNotification extends Notification
{
    use Queueable;

    public function __construct(
        public WorkflowInstance $instance,
        public WorkflowInstanceStep $stepRechazo,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $comentarios = $this->stepRechazo->assignments
            ->whereIn('estado', ['rechazado', 'devuelto'])
            ->pluck('comentario')
            ->filter()
            ->implode(' · ');

        $mail = (new MailMessage())
            ->subject("Requiere corrección: {$this->instance->codigo}")
            ->greeting("Hola, {$notifiable->name}")
            ->line("Tu trámite **{$this->instance->codigo}** fue devuelto para corrección.")
            ->line("**Trámite:** {$this->instance->titulo}")
            ->line("**Etapa:** {$this->stepRechazo->stage?->nombre}");

        if ($comentarios) {
            $mail->line("**Comentarios:** {$comentarios}");
        }

        return $mail
            ->action('Revisar y corregir', route('flujos.instancias.show', $this->instance))
            ->line('Cuando corrijas lo indicado, podés reenviar el flujo desde el detalle.');
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'tipo'        => 'requiere_correccion',
            'instance_id' => $this->instance->id,
            'codigo'      => $this->instance->codigo,
            'titulo'      => $this->instance->titulo,
            'etapa'       => $this->stepRechazo->stage?->nombre,
            'url'         => route('flujos.instancias.show', $this->instance),
            'mensaje'     => "Tu flujo {$this->instance->codigo} fue devuelto para corrección",
            'icono'       => 'fa-exclamation-triangle',
            'color'       => 'warning',
        ];
    }
}