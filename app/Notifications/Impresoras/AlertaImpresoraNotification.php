<?php

namespace App\Notifications\Impresoras;

use App\Models\Impresora;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AlertaImpresoraNotification extends Notification
{
    use Queueable;

    /**
     * @param  string  $tipoAlerta  'consumible_bajo' | 'mantenimiento_proximo'
     * @param  array   $detalle    Info específica de la alerta
     */
    public function __construct(
        public Impresora $impresora,
        public string $tipoAlerta,
        public array $detalle,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $esConsumible = $this->tipoAlerta === 'consumible_bajo';

        return [
            'tipo'      => "impresora_{$this->tipoAlerta}",
            'mensaje'   => $esConsumible
                ? sprintf(
                    '%s: %s al %d%%',
                    $this->impresora->nombre_visible,
                    $this->detalle['consumible'] ?? 'Consumible',
                    $this->detalle['nivel'] ?? 0,
                )
                : sprintf(
                    '%s: mantenimiento programado para %s',
                    $this->impresora->nombre_visible,
                    $this->detalle['fecha'] ?? 'próximamente',
                ),
            'impresora_id' => $this->impresora->id,
            'codigo'       => $this->impresora->codigo,
            'url'          => route('impresoras.show', $this->impresora),
            'icono'        => $esConsumible ? 'fa-fill-drip' : 'fa-tools',
            'color'        => $esConsumible ? 'meta-1' : 'meta-6',
            'detalle'      => $this->detalle,
        ];
    }
}