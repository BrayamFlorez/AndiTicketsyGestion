<?php

namespace App\Http\Controllers;

use App\Http\Requests\RecargarConsumibleRequest;
use App\Models\Impresora;
use App\Models\ImpresoraAlertaEnviada;
use App\Models\ImpresoraConsumible;
use App\Services\Impresoras\NivelConsumible;
use Illuminate\Http\RedirectResponse;

/**
 * Recarga parcial de un consumible: se agregó solo una porción (20 %, 50 %…).
 * Solo válida si se registra en el momento de la recarga: el nivel "antes" se compara
 * con lo que el sistema estima hoy para recalibrar la duración del consumible.
 */
class ImpresoraConsumibleRecargaController extends Controller
{
    public function store(
        RecargarConsumibleRequest $request,
        Impresora $impresora,
        ImpresoraConsumible $consumible,
        NivelConsumible $niveles,
    ): RedirectResponse {
        abort_if($consumible->impresora_id !== $impresora->id, 404);

        if (! $consumible->activo) {
            return back()->with('error', 'Solo se pueden recargar consumibles activos.');
        }

        $datos = $request->validated();

        $recarga = $niveles->aplicarRecarga(
            $consumible,
            (float) $datos['nivel_antes'],
            (float) $datos['nivel_despues'],
            $request->user()->id,
            $datos['observaciones'] ?? null,
        );

        // Con el nivel repuesto, la alerta de consumible bajo ya no aplica
        ImpresoraAlertaEnviada::resolverAlertas(
            $impresora->id,
            ImpresoraAlertaEnviada::TIPO_CONSUMIBLE_BAJO,
            $consumible->id,
        );

        $mensaje = "Recarga registrada: +{$this->numero($recarga->porcion_recargada)} % "
            . "(quedó en {$this->numero($recarga->nivel_despues)} %).";

        $mensaje .= $recarga->recalibrado
            ? " Duración recalibrada de {$recarga->dias_estimados_anteriores} a {$recarga->dias_estimados_nuevos} días; "
              . "quedan unos {$recarga->dias_restantes_estimados} días."
            : " Sin recalibrar la duración: muy poco uso desde la instalación o la última recarga.";

        return back()->with('exito', $mensaje);
    }

    private function numero(float $valor): string
    {
        return rtrim(rtrim(number_format($valor, 1, ',', '.'), '0'), ',');
    }
}
