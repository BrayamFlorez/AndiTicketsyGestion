<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreImpresoraMantenimientoRequest;
use App\Models\Impresora;
use App\Models\ImpresoraAlertaEnviada;
use App\Models\ImpresoraConsumible;
use App\Models\ImpresoraMantenimiento;
use App\Services\Impresoras\RegistroContador;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

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
        RegistroContador $contadores,
    ): RedirectResponse {

        $datos = $request->validated();
        $user  = $request->user();

        $mantenimiento = DB::transaction(function () use ($datos, $impresora, $user, $contadores) {

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

            // 2. Lectura del contador en el histórico, con la fecha del mantenimiento
            if (! empty($datos['contador_paginas'])) {
                $fecha = Carbon::parse($datos['fecha_realizado']);

                $contadores->registrar(
                    $impresora,
                    (int) $datos['contador_paginas'],
                    $user->id,
                    $fecha->isToday() ? now() : $fecha->setTime(12, 0),
                    reinicio: null, // un valor menor al anterior se toma como reinicio
                    mantenimientoId: $mantenimiento->id,
                );
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

                // Crear el nuevo (forceCreate: mantenimiento_id lo fija el sistema)
                ImpresoraConsumible::forceCreate([
                    'impresora_id'           => $impresora->id,
                    'tipo'                   => $consumible->tipo,
                    'color'                  => $consumible->color,
                    'etiqueta'               => $consumible->etiqueta,
                    'dias_estimados'         => $diasNuevo,
                    'fecha_instalacion'      => $fechaReemplazo,
                    'activo'                 => true,
                    'consumible_anterior_id' => $consumible->id,
                    'mantenimiento_id'       => $mantenimiento->id,
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

            return $mantenimiento;
        });

        // Siguiente paso: evidencia y firmas para finalizarlo
        return redirect()
            ->route('impresoras.mantenimientos.show', [$impresora, $mantenimiento])
            ->with('exito', 'Mantenimiento registrado. Agregue la evidencia y las firmas para finalizarlo.');
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

        $carpeta = $mantenimiento->carpetaArchivos();
        $mantenimiento->delete();

        // Evidencia y firmas
        Storage::disk('local')->deleteDirectory($carpeta);

        // Recalcular después de eliminar
        $impresora->recalcularProximoMantenimiento();

        return back()->with('exito', 'Mantenimiento eliminado.');
    }
}