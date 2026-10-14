<?php

namespace App\Http\Middleware;

use App\Models\EquipoAgente;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Autentica al agente de Windows con el token que recibió al vincularse.
 * Deja el agente disponible en $request->attributes->get('agente').
 */
class AutenticarAgente
{
    public function handle(Request $request, Closure $next): Response
    {
        $agente = EquipoAgente::buscarPorToken($request->bearerToken());

        if (! $agente) {
            // El agente, al recibir este código, deja de reportar y pide volver a vincularse
            return response()->json([
                'codigo'  => 'agente_no_autorizado',
                'mensaje' => 'Token inválido o revocado. Vuelva a vincular el agente.',
            ], 401);
        }

        $request->attributes->set('agente', $agente);

        return $next($request);
    }
}
