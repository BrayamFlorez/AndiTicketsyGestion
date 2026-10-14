<?php

namespace App\Http\Requests\Agente;

class ReporteAgenteRequest extends AgenteRequest
{
    public function rules(): array
    {
        return $this->reglasInventario();
    }
}
