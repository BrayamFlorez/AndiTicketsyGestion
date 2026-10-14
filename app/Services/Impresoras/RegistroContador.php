<?php

namespace App\Services\Impresoras;

use App\Models\Impresora;
use App\Models\ImpresoraContador;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\DB;

/**
 * Registra una lectura del contador de páginas con su fecha.
 * Lo usan el modal "Agregar contador" (fecha actual) y los mantenimientos (fecha del mantenimiento).
 */
class RegistroContador
{
    /**
     * @param  bool|null  $reinicio  null = se deduce (un valor menor al anterior solo puede ser un reinicio)
     */
    public function registrar(
        Impresora $impresora,
        int $contador,
        ?int $usuarioId,
        ?CarbonInterface $fecha = null,
        ?bool $reinicio = false,
        ?int $mantenimientoId = null,
    ): ImpresoraContador {
        $fecha ??= now();

        return DB::transaction(function () use ($impresora, $contador, $usuarioId, $fecha, $reinicio, $mantenimientoId) {
            // La lectura anterior es la última hasta esa fecha (un mantenimiento puede registrarse días después)
            $anterior = $impresora->contadores()
                ->where('created_at', '<=', $fecha)
                ->latest()->latest('id')
                ->value('contador');

            if ($anterior === null && ! $impresora->contadores()->exists()) {
                $anterior = $impresora->contador_paginas_actual;
            }

            $reinicio ??= $anterior !== null && $contador < $anterior;

            $lectura = new ImpresoraContador([
                'impresora_id'           => $impresora->id,
                'mantenimiento_id'       => $mantenimientoId,
                'contador'               => $contador,
                // Tras un reinicio no se puede saber cuántas páginas se imprimieron
                'paginas_desde_anterior' => $anterior !== null && ! $reinicio && $contador >= $anterior ? $contador - $anterior : null,
                'reinicio'               => $reinicio,
                'registrado_por'         => $usuarioId,
            ]);
            $lectura->created_at = $fecha;
            $lectura->updated_at = now();
            $lectura->save();

            // Si se registró con fecha anterior (mantenimiento cargado tarde), la lectura siguiente
            // ahora cuenta desde esta: si no, esas páginas se contarían dos veces
            $siguiente = $impresora->contadores()
                ->where('created_at', '>', $fecha)
                ->oldest()->oldest('id')
                ->first();

            if ($siguiente && ! $siguiente->reinicio) {
                $siguiente->update([
                    'paginas_desde_anterior' => $siguiente->contador >= $contador ? $siguiente->contador - $contador : null,
                ]);
            }

            // El contador actual de la impresora solo avanza si esta es la lectura más reciente
            if (! $siguiente) {
                $impresora->update([
                    'contador_paginas_actual' => $contador,
                    'contador_actualizado_en' => $fecha,
                ]);
            }

            return $lectura;
        });
    }
}
