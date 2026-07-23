<?php

namespace App\Http\Requests\Flujos;

use App\Actions\Flujos\ActuarEnAsignacionAction;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ActuarEnAsignacionRequest extends FormRequest
{
    public function authorize(): bool
    {
        // La autorización fina la valida el Action (por asignación + rol/usuario).
        // Aquí solo verificamos que el usuario esté autenticado.
        return $this->user() !== null;
    }

    public function rules(): array
    {
        $acciones = [
            ActuarEnAsignacionAction::ACCION_APROBAR,
            ActuarEnAsignacionAction::ACCION_RECHAZAR,
            ActuarEnAsignacionAction::ACCION_DEVOLVER,
        ];

        return [
            'accion'     => ['required', Rule::in($acciones)],
            'comentario' => [
                'nullable',
                'string',
                'max:2000',
                Rule::requiredIf(fn () => in_array(
                    $this->input('accion'),
                    [ActuarEnAsignacionAction::ACCION_RECHAZAR, ActuarEnAsignacionAction::ACCION_DEVOLVER],
                    true
                )),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'comentario.required' => 'Debe indicar un motivo al rechazar o devolver.',
        ];
    }
}