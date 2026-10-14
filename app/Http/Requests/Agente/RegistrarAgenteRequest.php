<?php

namespace App\Http\Requests\Agente;

use App\Services\Agente\AgenteInventarioService;
use Illuminate\Validation\Rule;

class RegistrarAgenteRequest extends AgenteRequest
{
    public function rules(): array
    {
        return array_merge($this->reglasInventario(), [
            // nuevo: crea un equipo (con serial combinado si ya existe) · reemplazar: toma el equipo con ese serial
            'accion' => ['required', Rule::in([AgenteInventarioService::ACCION_NUEVO, AgenteInventarioService::ACCION_REEMPLAZAR])],
            // Sede elegida en el instalador (opcional)
            'sede_id' => ['nullable', 'integer'],
            // Tipo de equipo elegido en el instalador (si no viene, se usa el sugerido)
            'categoria' => ['nullable', 'string', Rule::in(array_keys(
                app(AgenteInventarioService::class)->categoriasDisponibles()
            ))],
        ]);
    }

    public function messages(): array
    {
        return [
            'categoria.in' => 'El tipo de equipo elegido no existe en el sistema.',
        ];
    }
}
