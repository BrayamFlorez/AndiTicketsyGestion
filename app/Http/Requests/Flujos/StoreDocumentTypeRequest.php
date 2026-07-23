<?php

namespace App\Http\Requests\Flujos;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class StoreDocumentTypeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('flujos.tipos-documento.gestionar') ?? false;
    }

    public function rules(): array
    {
        return [
            'nombre'               => ['required', 'string', 'max:120'],
            'slug'                 => ['nullable', 'string', 'max:140', 'regex:/^[a-z0-9-]+$/', Rule::unique('document_types', 'slug')],
            'descripcion'          => ['nullable', 'string', 'max:1000'],
            'icono'                => ['nullable', 'string', 'max:60'],
            'workflow_template_id' => ['nullable', 'exists:workflow_templates,id'],
            'activo'               => ['boolean'],
        ];
    }

    /**
     * Normaliza el payload antes de validar:
     * - genera slug automático si viene vacío
     * - fuerza activo a booleano (los checkbox HTML no envían valor cuando no están marcados)
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'slug'   => $this->filled('slug') ? Str::slug($this->slug) : Str::slug($this->nombre ?? ''),
            'activo' => $this->boolean('activo'),
        ]);
    }

    public function messages(): array
    {
        return [
            'slug.regex'                 => 'El identificador solo admite letras minúsculas, números y guiones.',
            'workflow_template_id.exists' => 'La plantilla seleccionada no existe o fue eliminada.',
        ];
    }
}