<?php

namespace App\Http\Requests\Flujos;

use App\Models\WorkflowTemplate;

class UpdateWorkflowTemplateRequest extends StoreWorkflowTemplateRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('flujos.plantillas.gestionar') ?? false;
    }

    /**
     * Si la plantilla tiene instancias registradas, se bloquea la edición
     * estructural: solo se permite tocar nombre/descripción/activo.
     */
    public function rules(): array
    {
        $plantilla = $this->route('plantilla');

        if ($plantilla instanceof WorkflowTemplate && $plantilla->instances()->exists()) {
            return [
                'nombre'      => ['required', 'string', 'max:150'],
                'descripcion' => ['nullable', 'string', 'max:2000'],
                'activo'      => ['boolean'],
            ];
        }

        return parent::rules();
    }

    protected function prepareForValidation(): void
    {
        $plantilla = $this->route('plantilla');
        $tieneInstancias = $plantilla instanceof WorkflowTemplate && $plantilla->instances()->exists();

        // Solo procesamos etapas si la plantilla admite cambio estructural
        $etapas = $tieneInstancias
            ? []
            : (json_decode((string) $this->input('etapas_json', '[]'), true) ?: []);

        $this->merge([
            'activo' => $this->boolean('activo'),
            'etapas' => $etapas,
        ]);
    }
}