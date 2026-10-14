<?php

namespace App\Http\Requests;

use App\Models\EquipoPeriferico;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GuardarPerifericoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('equipos.editar')
            && $this->route('equipo')->puedeSerGestionadoPor($this->user());
    }

    public function rules(): array
    {
        return [
            'tipo'         => ['required', Rule::in(array_keys(EquipoPeriferico::TIPOS))],
            'marca'        => ['nullable', 'string', 'max:100'],
            'modelo'       => ['nullable', 'string', 'max:100'],
            'numero_serie' => ['nullable', 'string', 'max:100'],
            'notas'        => ['nullable', 'string', 'max:255', Rule::requiredIf($this->input('tipo') === 'otro')],
        ];
    }

    public function messages(): array
    {
        return ['notas.required' => 'Para "Otro", indique qué periférico es.'];
    }
}
