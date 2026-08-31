<?php

namespace App\Notifications\Flujos;

use App\Models\User;
use App\Models\WorkflowInstance;
use App\Models\WorkflowInstanceHistory;
use App\Support\Flujos;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class FlujoDatosEditadosNotification extends Notification
{
    use Queueable;

    /**
     * @param  array<int, string>  $camposEditados  Ej: ['titulo', 'descripcion']
     */
    public function __construct(
        public WorkflowInstance $instance,
        public User $editor,
        public array $camposEditados,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $labels = collect($this->camposEditados)
            ->map(fn ($c) => WorkflowInstanceHistory::CAMPOS_EDITABLES[$c] ?? $c)
            ->implode(', ');

        return (new MailMessage())
            ->subject("Datos actualizados: {$this->instance->titulo}")
            ->greeting("Hola, {$notifiable->name}")
            ->line("**{$this->editor->name}** actualizó los datos de un trámite donde tenés una decisión pendiente.")
            ->line("**Trámite:** {$this->instance->titulo}")
            ->line("**Código:** {$this->instance->codigo}")
            ->line("**Campos modificados:** {$labels}")
            ->action('Ver detalle actualizado', route('flujos.instancias.show', $this->instance))
            ->line('Es solo informativo — no requiere acción de tu parte.');
    }

    public function toDatabase(object $notifiable): array
    {
        $labels = collect($this->camposEditados)
            ->map(fn ($c) => WorkflowInstanceHistory::CAMPOS_EDITABLES[$c] ?? $c)
            ->implode(', ');

        return [
            'tipo'           => 'flujo_datos_editados',
            'mensaje'        => sprintf(
                "%s actualizó datos de \"%s\"",
                $this->editor->name,
                Str::limit($this->instance->titulo, 55),
            ),
            'instance_id'    => $this->instance->id,
            'codigo'         => $this->instance->codigo,
            'titulo'         => $this->instance->titulo,
            'url'            => route('flujos.instancias.show', $this->instance),
            'icono'          => 'fa-edit',
            'color'          => 'accent',
            'actor'          => [
                'name'      => $this->editor->name,
                'iniciales' => Flujos::iniciales($this->editor->name),
                'rol'       => 'Editó datos',
            ],
            'tipo_documento' => $this->instance->documentType?->nombre,
            'tipo_icono'     => $this->instance->documentType?->icono ?: 'fa-file-alt',
            'comentario'     => "Campos actualizados: {$labels}",
        ];
    }
}