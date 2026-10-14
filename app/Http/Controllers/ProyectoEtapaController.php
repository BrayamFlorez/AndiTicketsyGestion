<?php

namespace App\Http\Controllers;

use App\Http\Requests\Proyectos\EtapaRequest;
use App\Models\Proyecto;
use App\Models\ProyectoEtapa;
use App\Services\Proyectos\EtapaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ProyectoEtapaController extends Controller
{
    public function __construct(private readonly EtapaService $etapas)
    {
    }

    /** La autorización (registrar) la hace EtapaRequest */
    public function store(EtapaRequest $request, Proyecto $proyecto): RedirectResponse
    {
        $etapa = $this->etapas->registrar(
            $proyecto,
            $request->validated(),
            $request->file('archivos', []),
            $request->user(),
        );

        return redirect()
            ->route('proyectos.show', $proyecto)
            ->with('exito', "\"{$etapa->titulo}\" registrada en la línea de tiempo.");
    }

    /** La autorización (modificarRegistro) la hace EtapaRequest */
    public function update(EtapaRequest $request, Proyecto $proyecto, ProyectoEtapa $etapa): RedirectResponse
    {
        abort_unless($etapa->proyecto_id === $proyecto->id, 404);

        $this->etapas->actualizar($etapa, $request->validated(), $request->file('archivos', []), $request->user());

        return redirect()
            ->route('proyectos.show', $proyecto)
            ->with('exito', "\"{$etapa->titulo}\" actualizada.");
    }

    public function destroy(Request $request, Proyecto $proyecto, ProyectoEtapa $etapa): RedirectResponse
    {
        abort_unless($etapa->proyecto_id === $proyecto->id, 404);
        Gate::authorize('modificarRegistro', [$proyecto, $etapa]);

        $this->etapas->eliminar($etapa, $request->user());

        return redirect()
            ->route('proyectos.show', $proyecto)
            ->with('exito', 'Registro eliminado. Las solicitudes que atendía volvieron a "En proceso".');
    }
}
