<?php

namespace App\Http\Requests;

/** Mismas reglas que la evidencia de impresoras; cambia quién puede subirla. */
class SubirEvidenciaEquipoMantenimientoRequest extends SubirEvidenciaMantenimientoRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('equipos.mantenimientos.gestionar')
            && $this->route('equipo')->puedeSerGestionadoPor($this->user());
    }
}
