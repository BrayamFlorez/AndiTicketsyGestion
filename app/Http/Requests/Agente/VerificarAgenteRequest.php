<?php

namespace App\Http\Requests\Agente;

class VerificarAgenteRequest extends AgenteRequest
{
    public function rules(): array
    {
        return $this->reglasInventario();
    }
}
