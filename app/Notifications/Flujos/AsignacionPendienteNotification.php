<?php

namespace App\Notifications\Flujos;

use App\Models\WorkflowInstance;
use App\Models\WorkflowStepAssignment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AsignacionPendienteNotification extends Notification
{
    use Queueable;

    public function __construct(
        public WorkflowInstance $instance,
        public WorkflowStepAssignment $assignment,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $etapa = $this->assignment->step?->stage?->nombre ?? 'una etapa';
        $url   = route('flujos.instancias.show', $this->instance);

        return (new MailMessage())
            ->subject("Nueva tarea de aprobación: {$this->instance->codigo}")
            ->greeting("Hola, {$notifiable->name}")
            ->line("Se te asignó una decisión pendiente en el flujo **{$this->instance->codigo}**.")
            ->line("**Trámite:** {$this->instance->titulo}")
            ->line("**Etapa:** {$etapa}")
            ->action('Revisar y decidir', $url)
            ->line('Podrás aprobar, rechazar o devolver el trámite desde el detalle.');
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'tipo'          => 'asignacion_pendiente',
            'instance_id'   => $this->instance->id,
            'codigo'        => $this->instance->codigo,
            'titulo'        => $this->instance->titulo,
            'assignment_id' => $this->assignment->id,
            'etapa'         => $this->assignment->step?->stage?->nombre,
            'url'           => route('flujos.instancias.show', $this->instance),
            'mensaje'       => "Tienes una decisión pendiente en {$this->instance->codigo}",
            'icono'         => 'fa-hand-paper',
            'color'         => 'accent',
        ];
    }
}