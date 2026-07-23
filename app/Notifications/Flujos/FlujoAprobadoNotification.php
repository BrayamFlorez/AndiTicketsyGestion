<?php

namespace App\Notifications\Flujos;

use App\Models\User;
use App\Models\WorkflowInstance;
use App\Support\Flujos;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class FlujoAprobadoNotification extends Notification
{
    use Queueable;

    /**
     * @param  array<int, string>  $firmantes  Nombres de todos los aprobadores de la revisión final
     */
    public function __construct(
        public WorkflowInstance $instance,
        public ?User $cerradoPor = null,
        public array $firmantes = [],
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $tipoDoc = $this->instance->documentType;

        $mail = (new MailMessage())
            ->subject("Aprobado: {$this->instance->titulo}")
            ->greeting("Hola, {$notifiable->name}")
            ->line("Tu trámite **{$this->instance->titulo}** fue aprobado en todas sus etapas.");

        if ($tipoDoc) $mail->line("**Tipo:** {$tipoDoc->nombre}");
        $mail->line("**Código:** {$this->instance->codigo}");

        if ($this->instance->revision > 1) {
            $mail->line("Aprobado en la revisión {$this->instance->revision}.");
        }

        if (! empty($this->firmantes)) {
            $mail->line("**Firmado por:** " . implode(' · ', $this->firmantes));
        }

        return $mail
            ->action('Ver expediente completo', route('flujos.instancias.show', $this->instance))
            ->line('La línea de tiempo y los adjuntos de todas las revisiones quedan disponibles en el detalle.');
    }

    public function toDatabase(object $notifiable): array
    {
        $tipoDoc = $this->instance->documentType;

        $mensaje = $this->cerradoPor
            ? sprintf(
                "%s firmó \"%s\" — trámite aprobado",
                $this->cerradoPor->name,
                Str::limit($this->instance->titulo, 60),
            )
            : sprintf(
                "\"%s\" fue aprobado en todas sus etapas",
                Str::limit($this->instance->titulo, 60),
            );

        return [
            'tipo'           => 'flujo_aprobado',
            'mensaje'        => $mensaje,
            'instance_id'    => $this->instance->id,
            'codigo'         => $this->instance->codigo,
            'titulo'         => $this->instance->titulo,
            'url'            => route('flujos.instancias.show', $this->instance),
            'icono'          => 'fa-check-circle',
            'color'          => 'success',
            'actor'          => $this->cerradoPor ? [
                'name'      => $this->cerradoPor->name,
                'iniciales' => Flujos::iniciales($this->cerradoPor->name),
                'rol'       => 'Firma final',
            ] : null,
            'tipo_documento' => $tipoDoc?->nombre,
            'tipo_icono'     => $tipoDoc?->icono ?: 'fa-file-alt',
            'firmantes'      => $this->firmantes,
        ];
    }
}