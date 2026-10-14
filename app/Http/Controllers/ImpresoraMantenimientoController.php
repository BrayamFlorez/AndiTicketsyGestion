<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreImpresoraMantenimientoRequest;
use App\Models\Impresora;
use App\Models\ImpresoraAlertaEnviada;
use App\Models\ImpresoraConsumible;
use App\Models\ImpresoraContador;
use App\Models\ImpresoraMantenimiento;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class ImpresoraMantenimientoController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | STORE — Registrar mantenimiento + reemplazar consumibles en una acción
    |--------------------------------------------------------------------------
    */
    public function store(
        StoreImpresoraMantenimientoRequest $request,
        Impresora $impresora,
    ): RedirectResponse {

        $datos = $request->validated();
        $user  = $request->user();

        DB::transaction(function () use ($datos, $impresora, $user) {

            // 1. Crear el mantenimiento
            $mantenimiento = ImpresoraMantenimiento::create([
                'impresora_id'       => $impresora->id,
                'tipo'               => $datos['tipo'],
                'fecha_realizado'    => $datos['fecha_realizado'],
                'realizado_por'      => $datos['realizado_por'] ?? $user->id,
                'proximo_programado' => $datos['proximo_programado'] ?? null,
                'contador_paginas'   => $datos['contador_paginas'] ?? null,
                'descripcion'        => $datos['descripcion'],
                'observaciones'      => $datos['observaciones'] ?? null,
                'registrado_por'     => $user->id,
            ]);

            // 2. Actualizar contador de páginas de la impresora si vino (y dejarlo en el histórico)
            if (! empty($datos['contador_paginas'])) {
                $anterior = $impresora->contador_paginas_actual;
                $nuevo    = (int) $datos['contador_paginas'];
                $reinicio = $anterior !== null && $nuevo < $anterior;

                ImpresoraContador::create([
                    'impresora_id'           => $impresora->id,
                    'contador'               => $nuevo,
                    'paginas_desde_anterior' => $anterior !== null && ! $reinicio ? $nuevo - $anterior : null,
                    'reinicio'               => $reinicio,
                    'registrado_por'         => $user->id,
                ]);

                $impresora->update([
                    'contador_paginas_actual' => $nuevo,
                    'contador_actualizado_en' => now(),
                ]);
            }

            // 3. Reemplazar consumibles seleccionados (integrado)
            $fechaReemplazo = Carbon::parse($datos['fecha_realizado']);

            foreach ($datos['consumibles_reemplazados'] ?? [] as $ent) {
                $consumible = ImpresoraConsumible::where('id', $ent['consumible_id'])
                    ->where('impresora_id', $impresora->id)
                    ->where('activo', true)
                    ->first();

                if (! $consumible) continue;

                $duracionReal = $consumible->fecha_instalacion
                    ? (int) $consumible->fecha_instalacion->diffInDays($fechaReemplazo)
                    : null;

                // Cerrar el viejo
                $consumible->update([
                    'activo'             => false,
                    'fecha_reemplazo'    => $fechaReemplazo,
                    'duracion_real_dias' => $duracionReal,
                ]);

                // Días para el nuevo: recalibrar si se pidió y hay duración real
                $diasNuevo = ! empty($ent['recalibrar']) && $duracionReal
                    ? $duracionReal
                    : (int) $ent['dias_estimados'];

                // Crear el nuevo
                ImpresoraConsumible::create([
                    'impresora_id'           => $impresora->id,
                    'tipo'                   => $consumible->tipo,
                    'color'                  => $consumible->color,
                    'etiqueta'               => $consumible->etiqueta,
                    'dias_estimados'         => $diasNuevo,
                    'fecha_instalacion'      => $fechaReemplazo,
                    'activo'                 => true,
                    'consumible_anterior_id' => $consumible->id,
                    'observaciones'          => "Reemplazado en mantenimiento #{$mantenimiento->id}",
                    'registrado_por'         => $user->id,
                ]);

                // Marcar alertas de este consumible como resueltas
                ImpresoraAlertaEnviada::resolverAlertas(
                    $impresora->id,
                    ImpresoraAlertaEnviada::TIPO_CONSUMIBLE_BAJO,
                    $consumible->id,
                );
            }

            // 4. Recalcular próximo mantenimiento sugerido
            $impresora->refresh()->recalcularProximoMantenimiento();

            // 5. Resolver alertas de mantenimiento_proximo si existen
            ImpresoraAlertaEnviada::resolverAlertas(
                $impresora->id,
                ImpresoraAlertaEnviada::TIPO_MANTENIMIENTO_PROXIMO,
            );
        });

        return back()->with('exito', 'Mantenimiento registrado correctamente.');
    }

    /*
    |--------------------------------------------------------------------------
    | DESTROY — Eliminar un mantenimiento del historial
    |--------------------------------------------------------------------------
    */
    public function destroy(
        Impresora $impresora,
        ImpresoraMantenimiento $mantenimiento,
    ): RedirectResponse {

        abort_if($mantenimiento->impresora_id !== $impresora->id, 404);
        $this->authorize('gestionarMantenimientos', $impresora);

        $mantenimiento->delete();

        // Recalcular después de eliminar
        $impresora->recalcularProximoMantenimiento();

        return back()->with('exito', 'Mantenimiento eliminado.');
    }
}