<?php

namespace App\Http\Requests\Agente;

class VincularAgenteRequest extends AgenteRequest
{
    public function rules(): array
    {
        return array_merge($this->reglasInventario(), [
            'codigo_empresa' => ['required', 'string', 'max:20'],
            // Equipo existente elegido en el agente, o crear uno nuevo
            'equipo_id'      => ['nullable', 'integer', 'required_without:crear'],
            'crear'          => ['nullable', 'boolean', 'required_without:equipo_id'],
            // Confirmación explícita para reemplazar un agente activo en ese equipo
            'reemplazar'     => ['nullable', 'boolean'],
        ]);
    }

    public function messages(): array
    {
        return [
            'equipo_id.required_without' => 'Indique el equipo a vincular o pida crear uno nuevo.',
            'crear.required_without'     => 'Indique el equipo a vincular o pida crear uno nuevo.',
        ];
    }
}
