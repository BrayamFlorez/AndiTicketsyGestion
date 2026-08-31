<?php

namespace App\Http\Requests\Flujos;

use App\Models\WorkflowStage;
use App\Models\WorkflowStageActor;
use Illuminate\Foundation\Http\FormRequest;

class StoreWorkflowTemplateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('flujos.plantillas.gestionar') ?? false;
    }

    /**
     * El frontend envía las etapas serializadas en un JSON dentro del campo
     * `etapas_json`. Aquí lo decodificamos y lo remergemos como array `etapas`
     * para que Laravel valide con dot-notation.
     */
    protected function prepareForValidation(): void
    {
        $etapas = json_decode((string) $this->input('etapas_json', '[]'), true) ?: [];

        $this->merge([
            'activo' => $this->boolean('activo'),
            'etapas' => $etapas,
        ]);
    }

    public function rules(): array
    {
        $modosStage = [
            WorkflowStage::MODO_SECUENCIAL,
            WorkflowStage::MODO_PARALELO_TODOS,
            WorkflowStage::MODO_PARALELO_CUALQUIERA,
        ];

        $politicasRechazo = [
            WorkflowStage::RECHAZO_DEVOLVER_INICIADOR,
            WorkflowStage::RECHAZO_DEVOLVER_ETAPA_ANTERIOR,
            WorkflowStage::RECHAZO_DEVOLVER_ETAPA_ESPECIFICA,
            WorkflowStage::RECHAZO_FINALIZAR,
        ];

        return [
            'nombre'      => ['required', 'string', 'max:150'],
            'descripcion' => ['nullable', 'string', 'max:2000'],
            'activo'      => ['boolean'],

            'etapas'                            => ['required', 'array', 'min:1'],
            'etapas.*.id'                       => ['nullable', 'integer'],
            'etapas.*.nombre'                   => ['required', 'string', 'max:150'],
            'etapas.*.descripcion'              => ['nullable', 'string', 'max:1000'],
            'etapas.*.codigo_externo'           => ['nullable', 'string', 'max:50', 'regex:/^[A-Za-z0-9\-_]+$/'],
            'etapas.*.orden'                    => ['required', 'integer', 'min:1'],
            'etapas.*.modo'                     => ['required', 'in:' . implode(',', $modosStage)],
            'etapas.*.politica_rechazo'         => ['required', 'in:' . implode(',', $politicasRechazo)],
            'etapas.*.etapa_destino_rechazo_idx' => ['nullable', 'integer', 'min:0'],
            'etapas.*.requiere_adjunto'         => ['boolean'],
            'etapas.*.sla_horas'                => ['nullable', 'integer', 'min:1', 'max:8760'],

            'etapas.*.actores'                  => ['required', 'array', 'min:1'],
            'etapas.*.actores.*.actor_type'     => ['required', 'in:' . WorkflowStageActor::TIPO_ROL . ',' . WorkflowStageActor::TIPO_USUARIO],
            'etapas.*.actores.*.actor_id'       => ['required', 'integer'],
            'etapas.*.actores.*.etiqueta'       => ['nullable', 'string', 'max:120'],
        ];
    }

    public function messages(): array
    {
        return [
            'etapas.required'          => 'La plantilla debe tener al menos una etapa.',
            'etapas.*.codigo_externo.regex' => 'El código de etapa solo admite letras, números, guiones y guion bajo.',
            'etapas.*.actores.required' => 'Cada etapa debe tener al menos un actor asignado.',
            'etapas.*.actores.min'      => 'Cada etapa debe tener al menos un actor asignado.',
        ];
    }
}