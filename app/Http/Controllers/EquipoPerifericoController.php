<?php

namespace App\Http\Controllers;

use App\Http\Requests\GuardarPerifericoRequest;
use App\Models\Equipo;
use App\Models\EquipoPeriferico;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/** Periféricos de un PC (pantalla, teclado, mouse, UPS...). Se gestionan desde la ficha del equipo. */
class EquipoPerifericoController extends Controller
{
    public function store(GuardarPerifericoRequest $request, Equipo $equipo): RedirectResponse
    {
        $periferico = $equipo->perifericos()->create($request->validated());

        return back()->with('success', "Periférico agregado: {$periferico->descripcion}.");
    }

    public function update(GuardarPerifericoRequest $request, Equipo $equipo, EquipoPeriferico $periferico): RedirectResponse
    {
        abort_if((int) $periferico->equipo_id !== (int) $equipo->id, 404);

        $periferico->update($request->validated());

        return back()->with('success', 'Periférico actualizado.');
    }

    public function destroy(Request $request, Equipo $equipo, EquipoPeriferico $periferico): RedirectResponse
    {
        abort_if((int) $periferico->equipo_id !== (int) $equipo->id, 404);
        abort_unless($request->user()->can('equipos.editar') && $equipo->puedeSerGestionadoPor($request->user()), 403);

        // Las revisiones ya registradas conservan el nombre (la fila guarda el texto)
        $periferico->delete();

        return back()->with('success', 'Periférico retirado del equipo.');
    }
}
