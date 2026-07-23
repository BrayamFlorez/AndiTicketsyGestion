<?php

namespace App\Http\Requests\Flujos;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UpdateDocumentTypeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('flujos.tipos-documento.gestionar') ?? false;
    }

    public function rules(): array
    {
        $tipoId = $this->route('tipoDocumento')?->id;

        return [
            'nombre'               => ['required', 'string', 'max:120'],
            'slug'                 => ['nullable', 'string', 'max:140', 'regex:/^[a-z0-9-]+$/', Rule::unique('document_types', 'slug')->ignore($tipoId)],
            'descripcion'          => ['nullable', 'string', 'max:1000'],
            'icono'                => ['nullable', 'string', 'max:60'],
            'workflow_template_id' => ['nullable', 'exists:workflow_templates,id'],
            'activo'               => ['boolean'],
        ];
    }

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
            'slug.regex' => 'El identificador solo admite letras minúsculas, números y guiones.',
        ];
    }
}