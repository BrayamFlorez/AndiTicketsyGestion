<?php

namespace App\Http\Requests\Flujos;

use App\Models\DocumentType;
use Illuminate\Foundation\Http\FormRequest;

class StoreWorkflowInstanceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('flujos.iniciar') ?? false;
    }

    public function rules(): array
    {
        return [
            'document_type_id' => [
                'required',
                'integer',
                function ($attr, $value, $fail) {
                    $tipo = DocumentType::with('template')->find($value);

                    if (! $tipo || ! $tipo->activo) {
                        $fail('El tipo de documento seleccionado no está disponible.');
                        return;
                    }

                    if (! $tipo->template || ! $tipo->template->activo) {
                        $fail('El tipo de documento no tiene una plantilla de flujo activa asociada.');
                    }
                },
            ],
            'titulo'      => ['required', 'string', 'max:200'],
            'descripcion' => ['nullable', 'string', 'max:2000'],
        ];
    }
}