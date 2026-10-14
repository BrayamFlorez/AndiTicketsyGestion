<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreImpresoraConsumibleRequest;
use App\Models\Impresora;
use App\Models\ImpresoraConsumible;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ImpresoraConsumibleController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | STORE — Alta de un nuevo consumible en la impresora
    |--------------------------------------------------------------------------
    */
    public function store(
        StoreImpresoraConsumibleRequest $request,
        Impresora $impresora,
    ): RedirectResponse {

        $datos = $request->validated();

        // Evitar duplicados: si ya hay uno activo con mismo tipo+color, rechazar
        $duplicado = $impresora->consumiblesActivos()
            ->where('tipo', $datos['tipo'])
            ->where('color', $datos['color'])
            ->exists();

        if ($duplicado) {
            return back()
                ->withInput()
                ->withErrors([
                    'tipo' => 'Ya existe un consumible activo con ese tipo y color. Reemplazalo en vez de crear uno nuevo.',
                ]);
        }

        ImpresoraConsumible::create(array_merge($datos, [
            'impresora_id'   => $impresora->id,
            'activo'         => true,
            'registrado_por' => $request->user()->id,
        ]));

        return back()->with('exito', 'Consumible agregado correctamente.');
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE — Editar datos del consumible activo (días estimados, etiqueta, obs)
    |--------------------------------------------------------------------------
    */
    public function update(
        Request $request,
        Impresora $impresora,
        ImpresoraConsumible $consumible,
    ): RedirectResponse {

        abort_if($consumible->impresora_id !== $impresora->id, 404);
        $this->authorize('gestionarConsumibles', $impresora);

        if (! $consumible->activo) {
            return back()->with('error', 'No se pueden modificar consumibles archivados.');
        }

        $datos = $request->validate([
            'etiqueta'          => ['nullable', 'string', 'max:80'],
            'dias_estimados'    => ['required', 'integer', 'min:1', 'max:3650'],
            'fecha_instalacion' => ['nullable', 'date', 'before_or_equal:today'],
            'observaciones'     => ['nullable', 'string', 'max:1000'],
        ]);

        $consumible->update($datos);

        return back()->with('exito', 'Consumible actualizado.');
    }

    /*
    |--------------------------------------------------------------------------
    | REEMPLAZAR — Archiva el actual, crea uno nuevo, opcional recalibración
    |--------------------------------------------------------------------------
    */
    public function reemplazar(
        Request $request,
        Impresora $impresora,
        ImpresoraConsumible $consumible,
    ): RedirectResponse {

        abort_if($consumible->impresora_id !== $impresora->id, 404);
        $this->authorize('gestionarConsumibles', $impresora);

        if (! $consumible->activo) {
            return back()->with('error', 'Este consumible ya fue reemplazado.');
        }

        $datos = $request->validate([
            'fecha_reemplazo'         => ['required', 'date', 'before_or_equal:today'],
            'dias_estimados_nuevo'    => ['required', 'integer', 'min:1', 'max:3650'],
            'recalibrar'              => ['nullable', 'boolean'],
            'observaciones'           => ['nullable', 'string', 'max:1000'],
        ]);

        DB::transaction(function () use ($consumible, $datos, $request) {

            $fechaReemplazo = \Carbon\Carbon::parse($datos['fecha_reemplazo']);

            // Calcular duración real del consumible que se retira
            $duracionReal = $consumible->fecha_instalacion
                ? (int) $consumible->fecha_instalacion->diffInDays($fechaReemplazo)
                : null;

            // Cerrar el consumible viejo
            $consumible->update([
                'activo'             => false,
                'fecha_reemplazo'    => $fechaReemplazo,
                'duracion_real_dias' => $duracionReal,
            ]);

            // Días estimados para el nuevo: si el usuario pidió recalibrar Y hay duración real, usarla
            $diasEstimadosNuevo = ! empty($datos['recalibrar']) && $duracionReal
                ? $duracionReal
                : (int) $datos['dias_estimados_nuevo'];

            // Crear el consumible nuevo, referenciando al anterior
            ImpresoraConsumible::create([
                'impresora_id'           => $consumible->impresora_id,
                'tipo'                   => $consumible->tipo,
                'color'                  => $consumible->color,
                'etiqueta'               => $consumible->etiqueta,
                'dias_estimados'         => $diasEstimadosNuevo,
                'fecha_instalacion'      => $fechaReemplazo,
                'activo'                 => true,
                'consumible_anterior_id' => $consumible->id,
                'observaciones'          => $datos['observaciones'] ?? null,
                'registrado_por'         => $request->user()->id,
            ]);
        });

        return back()->with('exito', "Consumible reemplazado. El anterior duró {$consumible->fresh()->duracion_real_dias} días.");
    }

    /*
    |--------------------------------------------------------------------------
    | DESTROY — Solo permite eliminar consumibles ACTIVOS (no historial)
    |--------------------------------------------------------------------------
    */
    public function destroy(Impresora $impresora, ImpresoraConsumible $consumible): RedirectResponse
    {
        abort_if($consumible->impresora_id !== $impresora->id, 404);
        $this->authorize('gestionarConsumibles', $impresora);

        if (! $consumible->activo) {
            return back()->with('error', 'No se puede eliminar el historial. Solo consumibles activos.');
        }

        $consumible->delete();

        return back()->with('exito', 'Consumible eliminado.');
    }
}