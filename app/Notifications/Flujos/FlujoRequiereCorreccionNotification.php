<?php

namespace App\Notifications\Flujos;

use App\Models\User;
use App\Models\WorkflowInstance;
use App\Models\WorkflowInstanceStep;
use App\Support\Flujos;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class FlujoRequiereCorreccionNotification extends Notification
{
    use Queueable;

    public function __construct(
        public WorkflowInstance $instance,
        public WorkflowInstanceStep $stepRechazo,
        public ?User $rechazadoPor = null,
        public ?string $comentario = null,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $tipoDoc = $this->instance->documentType;
        $etapa   = $this->stepRechazo->stage?->nombre;

        $mail = (new MailMessage())
            ->subject("Requiere corrección: {$this->instance->titulo}")
            ->greeting("Hola, {$notifiable->name}");

        $mail->line($this->rechazadoPor
            ? "**{$this->rechazadoPor->name}** devolvió tu trámite para que lo corrijas."
            : "Tu trámite fue devuelto para corrección.");

        $mail->line("**Trámite:** {$this->instance->titulo}");
        if ($tipoDoc) $mail->line("**Tipo:** {$tipoDoc->nombre}");
        if ($etapa)   $mail->line("**Etapa donde se devolvió:** {$etapa}");
        $mail->line("**Código:** {$this->instance->codigo}");
        $mail->line("**Revisión actual:** {$this->instance->revision}");

        if ($this->comentario) {
            $mail->line("**Motivo indicado:**")
                 ->line("> " . Str::limit($this->comentario, 500));
        }

        return $mail
            ->action('Revisar y corregir', route('flujos.instancias.show', $this->instance))
            ->line('Cuando ajustes lo indicado, podés reenviar el flujo desde el detalle. Se creará como una nueva revisión conservando el historial de la anterior.');
    }

    public function toDatabase(object $notifiable): array
    {
        $tipoDoc = $this->instance->documentType;
        $etapa   = $this->stepRechazo->stage?->nombre;

        $mensaje = $this->rechazadoPor
            ? sprintf(
                "%s rechazó \"%s\"",
                $this->rechazadoPor->name,
                Str::limit($this->instance->titulo, 60),
            )
            : sprintf(
                "Tu trámite \"%s\" fue devuelto para corrección",
                Str::limit($this->instance->titulo, 60),
            );

        return [
            'tipo'           => 'requiere_correccion',
            'mensaje'        => $mensaje,
            'instance_id'    => $this->instance->id,
            'codigo'         => $this->instance->codigo,
            'titulo'         => $this->instance->titulo,
            'url'            => route('flujos.instancias.show', $this->instance),
            'icono'          => 'fa-exclamation-triangle',
            'color'          => 'warning',
            'actor'          => $this->rechazadoPor ? [
                'name'      => $this->rechazadoPor->name,
                'iniciales' => Flujos::iniciales($this->rechazadoPor->name),
                'rol'       => 'Rechazó',
            ] : null,
            'tipo_documento' => $tipoDoc?->nombre,
            'tipo_icono'     => $tipoDoc?->icono ?: 'fa-file-alt',
            'etapa'          => $etapa,
            'comentario'     => $this->comentario,
        ];
    }
}