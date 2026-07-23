<?php

namespace App\Notifications\Flujos;

use App\Models\User;
use App\Models\WorkflowInstance;
use App\Support\Flujos;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class FlujoCanceladoNotification extends Notification
{
    use Queueable;

    public function __construct(
        public WorkflowInstance $instance,
        public ?User $canceladoPor = null,
        public ?string $motivo = null,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $tipoDoc = $this->instance->documentType;

        $mail = (new MailMessage())
            ->subject("Cancelado: {$this->instance->titulo}")
            ->greeting("Hola, {$notifiable->name}");

        $mail->line($this->canceladoPor
            ? "**{$this->canceladoPor->name}** canceló tu trámite."
            : "Tu trámite fue cancelado por política del flujo (rechazo con cierre definitivo).");

        $mail->line("**Trámite:** {$this->instance->titulo}");
        if ($tipoDoc) $mail->line("**Tipo:** {$tipoDoc->nombre}");
        $mail->line("**Código:** {$this->instance->codigo}");

        if ($this->motivo) {
            $mail->line("**Motivo:**")
                 ->line("> " . Str::limit($this->motivo, 500));
        }

        return $mail
            ->action('Ver detalle', route('flujos.instancias.show', $this->instance))
            ->line('Si necesitás retomarlo, un administrador puede reabrirlo desde el detalle.');
    }

    public function toDatabase(object $notifiable): array
    {
        $tipoDoc = $this->instance->documentType;

        $mensaje = $this->canceladoPor
            ? sprintf(
                "%s canceló \"%s\"",
                $this->canceladoPor->name,
                Str::limit($this->instance->titulo, 60),
            )
            : sprintf(
                "\"%s\" fue cancelado por política del flujo",
                Str::limit($this->instance->titulo, 60),
            );

        return [
            'tipo'           => 'flujo_cancelado',
            'mensaje'        => $mensaje,
            'instance_id'    => $this->instance->id,
            'codigo'         => $this->instance->codigo,
            'titulo'         => $this->instance->titulo,
            'url'            => route('flujos.instancias.show', $this->instance),
            'icono'          => 'fa-ban',
            'color'          => 'danger',
            'actor'          => $this->canceladoPor ? [
                'name'      => $this->canceladoPor->name,
                'iniciales' => Flujos::iniciales($this->canceladoPor->name),
                'rol'       => 'Canceló',
            ] : null,
            'tipo_documento' => $tipoDoc?->nombre,
            'tipo_icono'     => $tipoDoc?->icono ?: 'fa-file-alt',
            'motivo'         => $this->motivo,
        ];
    }
}