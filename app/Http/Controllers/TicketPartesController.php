<?php

namespace App\Http\Controllers;

use App\Http\Requests\SolicitarPartesRequest;
use App\Models\Ticket;
use App\Services\Tickets\CicloTicket;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Etapa "Esperando partes", entre "En progreso" y "Resuelto".
 *  - solicitar: en progreso → esperando partes (qué se pidió, fecha y costo estimados, evidencia)
 *  - recibir:   esperando partes → en progreso (las partes llegaron y se sigue trabajando)
 * Desde "esperando partes" también se puede resolver directamente (TicketController@resolver).
 */
class TicketPartesController extends Controller
{
    public function __construct(private readonly CicloTicket $ciclo) {}

    public function solicitar(SolicitarPartesRequest $request, Ticket $ticket): RedirectResponse
    {
        $this->autorizarTecnico($request, $ticket);
        abort_unless($ticket->puedeSolicitarPartes(), 422,
            'Solo se pueden solicitar partes con el ticket en progreso.');

        $datos = $request->validated();

        DB::transaction(function () use ($request, $ticket, $datos) {
            $solicitud = $ticket->solicitudesPartes()->create([
                'descripcion'    => $datos['partes'],
                'fecha_estimada' => $datos['fecha_estimada'] ?? null,
                'costo_estimado' => $datos['costo_estimado'] ?? null,
                'evidencia'      => $request->hasFile('evidencia')
                    ? $request->file('evidencia')->store('tickets/partes', 'local')
                    : null,
                'solicitado_por' => $request->user()->id,
            ]);

            $this->ciclo->transicion($ticket, 'partes_solicitadas', ['estado' => 'esperando_partes'], $request->user(), [
                'comentario' => $solicitud->descripcion,
                'datos'      => array_filter([
                    'solicitud_id'   => $solicitud->id,
                    'fecha_estimada' => $solicitud->fecha_estimada?->toDateString(),
                    'costo_estimado' => $solicitud->costo_estimado,
                ], fn ($v) => $v !== null),
            ]);
        });

        return back()->with('success', "Ticket #{$ticket->id} en espera de partes.");
    }

    public function recibir(Request $request, Ticket $ticket): RedirectResponse
    {
        abort_unless($request->user()->can('tickets.resolver'), 403);
        $this->autorizarTecnico($request, $ticket);
        abort_unless($ticket->puedeRecibirPartes(), 422,
            'El ticket no está esperando partes.');

        $datos = $request->validate([
            'notas_recepcion' => ['nullable', 'string', 'max:1000'],
        ]);

        DB::transaction(function () use ($request, $ticket, $datos) {
            $solicitud = $ticket->solicitudParteAbierta;

            $solicitud?->update([
                'recibido_at'     => now(),
                'recibido_por'    => $request->user()->id,
                'notas_recepcion' => $datos['notas_recepcion'] ?? null,
            ]);

            $this->ciclo->transicion($ticket, 'partes_recibidas', ['estado' => 'en_progreso'], $request->user(), [
                'comentario' => $datos['notas_recepcion'] ?? null,
                'datos'      => $solicitud ? ['solicitud_id' => $solicitud->id, 'dias_espera' => $solicitud->dias_espera] : null,
            ]);
        });

        return back()->with('success', "Partes recibidas: el ticket #{$ticket->id} vuelve a estar en progreso.");
    }

    /** Igual que iniciar/resolver: solo el técnico asignado mueve el ticket. */
    private function autorizarTecnico(Request $request, Ticket $ticket): void
    {
        abort_unless($ticket->asignado_a === $request->user()->id, 403,
            'Solo el técnico asignado puede gestionar las partes de este ticket.');
    }
}
