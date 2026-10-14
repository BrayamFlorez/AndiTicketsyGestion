<?php

namespace App\Services\Tickets;

use App\Models\Ticket;
use App\Models\TicketEvento;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Aplica un cambio al ticket y lo deja en su línea de tiempo, en la misma transacción.
 * Todo cambio de estado o de técnico debe pasar por aquí para que el historial quede completo.
 */
class CicloTicket
{
    /**
     * @param  array  $cambios  columnas del ticket a actualizar (estado, asignado_a, fechas...)
     * @param  array  $evento   comentario y/o datos extra del evento
     */
    public function transicion(Ticket $ticket, string $tipo, array $cambios, ?User $actor, array $evento = []): TicketEvento
    {
        return DB::transaction(function () use ($ticket, $tipo, $cambios, $actor, $evento) {
            $estadoAnterior  = $ticket->estado;
            $tecnicoAnterior = $ticket->asignado_a;

            if ($cambios) {
                $ticket->update($cambios);
            }

            // Comparación como texto: la BD puede devolver el id como string o como entero
            $cambioTecnico = (string) $ticket->asignado_a !== (string) $tecnicoAnterior;

            return $this->registrar($ticket, $tipo, $actor, [
                'estado_anterior'     => $estadoAnterior,
                'estado_nuevo'        => $ticket->estado,
                'tecnico_anterior_id' => $cambioTecnico ? $tecnicoAnterior : null,
                'tecnico_nuevo_id'    => $cambioTecnico ? $ticket->asignado_a : null,
                'comentario'          => $evento['comentario'] ?? null,
                'datos'               => $evento['datos'] ?? null,
            ]);
        });
    }

    /** Evento sin cambios en el ticket (p. ej. la creación, que ya se guardó). */
    public function registrar(Ticket $ticket, string $tipo, ?User $actor, array $atributos = []): TicketEvento
    {
        return $ticket->eventos()->create(array_merge([
            'tipo'         => $tipo,
            'usuario_id'   => $actor?->id,
            'estado_nuevo' => $ticket->estado,
        ], $atributos));
    }
}
