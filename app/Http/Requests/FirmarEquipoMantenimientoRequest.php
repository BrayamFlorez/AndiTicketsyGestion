<?php

namespace App\Http\Requests;

/** Mismas validaciones de firma que impresoras; cambia quién puede firmar. */
class FirmarEquipoMantenimientoRequest extends FirmarMantenimientoRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('equipos.mantenimientos.gestionar')
            && $this->route('equipo')->puedeSerGestionadoPor($this->user());
    }
}
