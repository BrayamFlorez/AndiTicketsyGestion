<?php

namespace App\Notifications\Flujos;

use App\Models\WorkflowInstance;
use App\Models\WorkflowStepAssignment;
use App\Support\Flujos;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

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
        $iniciador = $this->instance->iniciador;
        $tipoDoc   = $this->instance->documentType;
        $etapa     = $this->assignment->step?->stage?->nombre;
        $url       = route('flujos.instancias.show', $this->instance);

        $mail = (new MailMessage())
            ->subject("Aprobación pendiente: {$this->instance->titulo}")
            ->greeting("Hola, {$notifiable->name}");

        $mail->line($iniciador
            ? "**{$iniciador->name}** inició un trámite que requiere tu decisión."
            : "Tenés una decisión pendiente en un trámite.");

        $mail->line("**Trámite:** {$this->instance->titulo}");
        if ($tipoDoc) $mail->line("**Tipo:** {$tipoDoc->nombre}");
        if ($etapa)   $mail->line("**Etapa donde debés actuar:** {$etapa}");
        $mail->line("**Código:** {$this->instance->codigo}");

        if ($this->instance->descripcion) {
            $mail->line("**Descripción del solicitante:**")
                 ->line("> " . Str::limit($this->instance->descripcion, 300));
        }

        return $mail
            ->action('Revisar y decidir', $url)
            ->line('Podés aprobar, rechazar o devolver el trámite desde el detalle. Tu decisión queda registrada con fecha y comentario.');
    }

    public function toDatabase(object $notifiable): array
    {
        $iniciador = $this->instance->iniciador;
        $tipoDoc   = $this->instance->documentType;
        $etapa     = $this->assignment->step?->stage?->nombre;

        $mensaje = $iniciador
            ? sprintf(
                "%s requiere tu aprobación: \"%s\"",
                $iniciador->name,
                Str::limit($this->instance->titulo, 60),
            )
            : sprintf("Requiere tu aprobación: \"%s\"", Str::limit($this->instance->titulo, 60));

        return [
            'tipo'           => 'asignacion_pendiente',
            'mensaje'        => $mensaje,
            'instance_id'    => $this->instance->id,
            'codigo'         => $this->instance->codigo,
            'titulo'         => $this->instance->titulo,
            'url'            => route('flujos.instancias.show', $this->instance),
            'icono'          => 'fa-hand-paper',
            'color'          => 'accent',
            'actor'          => $iniciador ? [
                'name'      => $iniciador->name,
                'iniciales' => Flujos::iniciales($iniciador->name),
                'rol'       => 'Iniciador',
            ] : null,
            'tipo_documento' => $tipoDoc?->nombre,
            'tipo_icono'     => $tipoDoc?->icono ?: 'fa-file-alt',
            'etapa'          => $etapa,
        ];
    }
}