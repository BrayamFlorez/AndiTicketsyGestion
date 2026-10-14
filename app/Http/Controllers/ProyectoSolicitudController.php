<?php

namespace App\Http\Controllers;

use App\Http\Requests\Proyectos\SolicitudRequest;
use App\Models\Proyecto;
use App\Models\ProyectoSolicitud;
use App\Services\Proyectos\SolicitudService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class ProyectoSolicitudController extends Controller
{
    public function __construct(private readonly SolicitudService $solicitudes)
    {
    }

    /** La autorización (registrar) la hace SolicitudRequest */
    public function store(SolicitudRequest $request, Proyecto $proyecto): RedirectResponse
    {
        $this->solicitudes->registrar(
            $proyecto,
            $request->validated(),
            $request->file('archivos', []),
            $request->user(),
        );

        return redirect()
            ->route('proyectos.show', $proyecto)
            ->with('exito', 'Solicitud registrada como pendiente.');
    }

    /** La autorización (modificarRegistro) la hace SolicitudRequest */
    public function update(SolicitudRequest $request, Proyecto $proyecto, ProyectoSolicitud $solicitud): RedirectResponse
    {
        abort_unless($solicitud->proyecto_id === $proyecto->id, 404);

        $this->solicitudes->actualizar($solicitud, $request->validated(), $request->file('archivos', []), $request->user());

        return redirect()
            ->route('proyectos.show', $proyecto)
            ->with('exito', 'Solicitud actualizada.');
    }

    /** Cambio rápido de estado desde la tarjeta de la línea de tiempo */
    public function estado(Request $request, Proyecto $proyecto, ProyectoSolicitud $solicitud): RedirectResponse
    {
        abort_unless($solicitud->proyecto_id === $proyecto->id, 404);
        Gate::authorize('modificarRegistro', [$proyecto, $solicitud]);

        $datos = $request->validate([
            'estado'    => ['required', Rule::in(array_keys(ProyectoSolicitud::ESTADOS))],
            'respuesta' => ['nullable', 'string', 'max:2000'],
        ]);

        $this->solicitudes->cambiarEstado($solicitud, $datos['estado'], $datos['respuesta'] ?? null);

        return redirect()
            ->route('proyectos.show', $proyecto)
            ->with('exito', "Solicitud \"{$solicitud->titulo}\" marcada como {$solicitud->label_estado}.");
    }

    public function destroy(Request $request, Proyecto $proyecto, ProyectoSolicitud $solicitud): RedirectResponse
    {
        abort_unless($solicitud->proyecto_id === $proyecto->id, 404);
        Gate::authorize('modificarRegistro', [$proyecto, $solicitud]);

        $this->solicitudes->eliminar($solicitud, $request->user());

        return redirect()
            ->route('proyectos.show', $proyecto)
            ->with('exito', 'Solicitud eliminada.');
    }
}
