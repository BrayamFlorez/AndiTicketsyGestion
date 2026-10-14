<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreImpresoraContadorRequest;
use App\Models\Impresora;
use App\Models\ImpresoraContador;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

/** Lecturas del contador de páginas. La fecha es la del momento del registro. */
class ImpresoraContadorController extends Controller
{
    public function store(StoreImpresoraContadorRequest $request, Impresora $impresora): RedirectResponse
    {
        $contador = (int) $request->validated('contador');
        $reinicio = $request->boolean('reinicio');

        DB::transaction(function () use ($impresora, $contador, $reinicio, $request) {
            $anterior = $impresora->contador_paginas_actual;

            ImpresoraContador::create([
                'impresora_id'           => $impresora->id,
                'contador'               => $contador,
                // Tras un reinicio no se puede saber cuántas páginas se imprimieron
                'paginas_desde_anterior' => $anterior !== null && ! $reinicio ? $contador - $anterior : null,
                'reinicio'               => $reinicio,
                'registrado_por'         => $request->user()->id,
            ]);

            $impresora->update([
                'contador_paginas_actual' => $contador,
                'contador_actualizado_en' => now(),
            ]);
        });

        return back()->with('exito', 'Contador registrado: ' . number_format($contador, 0, ',', '.') . ' páginas.');
    }
}
