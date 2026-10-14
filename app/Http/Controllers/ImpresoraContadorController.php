<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreImpresoraContadorRequest;
use App\Models\Impresora;
use App\Services\Impresoras\RegistroContador;
use Illuminate\Http\RedirectResponse;

/** Lecturas del contador de páginas. La fecha es la del momento del registro. */
class ImpresoraContadorController extends Controller
{
    public function store(StoreImpresoraContadorRequest $request, Impresora $impresora, RegistroContador $contadores): RedirectResponse
    {
        $contador = (int) $request->validated('contador');

        $contadores->registrar($impresora, $contador, $request->user()->id, now(), $request->boolean('reinicio'));

        return back()->with('exito', 'Contador registrado: ' . number_format($contador, 0, ',', '.') . ' páginas.');
    }
}
