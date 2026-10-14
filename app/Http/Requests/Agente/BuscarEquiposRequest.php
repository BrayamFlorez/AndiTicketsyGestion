<?php

namespace App\Http\Requests\Agente;

class BuscarEquiposRequest extends AgenteRequest
{
    public function rules(): array
    {
        return array_merge($this->reglasInventario(), [
            'codigo_empresa' => ['required', 'string', 'max:20'],
            // Texto libre para buscar por nombre cuando el serial no coincide
            'q'              => ['nullable', 'string', 'max:100'],
        ]);
    }
}
