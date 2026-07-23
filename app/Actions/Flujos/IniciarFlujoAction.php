<?php

namespace App\Actions\Flujos;

use App\Events\Flujos\FlujoIniciado;
use App\Models\User;
use App\Models\WorkflowInstance;
use App\Models\WorkflowTemplate;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class IniciarFlujoAction
{
    public function __construct(private IniciarEtapaAction $iniciarEtapa) {}

    /**
     * @param  array{titulo:string, descripcion?:?string, document_type_id?:?int}  $datos
     */
    public function ejecutar(WorkflowTemplate $plantilla, User $iniciador, array $datos): WorkflowInstance
    {
        if (! $plantilla->activo) {
            throw ValidationException::withMessages([
                'plantilla' => 'La plantilla no está activa.',
            ]);
        }

        $primeraEtapa = $plantilla->stages()->orderBy('orden')->first();

        if (! $primeraEtapa) {
            throw ValidationException::withMessages([
                'plantilla' => 'La plantilla no tiene etapas configuradas.',
            ]);
        }

        return DB::transaction(function () use ($plantilla, $primeraEtapa, $iniciador, $datos) {

            $instance = WorkflowInstance::create([
                'codigo'               => 'FLJ-TMP', // se reemplaza justo abajo (necesitamos el id)
                'workflow_template_id' => $plantilla->id,
                'document_type_id'     => $datos['document_type_id'] ?? null,
                'titulo'               => $datos['titulo'],
                'descripcion'          => $datos['descripcion'] ?? null,
                'iniciado_por'         => $iniciador->id,
                'estado'               => WorkflowInstance::ESTADO_EN_PROGRESO,
                'revision'             => 1,
                'iniciado_en'          => now(),
            ]);

            $instance->update([
                'codigo' => sprintf('FLJ-%d-%06d', now()->year, $instance->id),
            ]);

            $this->iniciarEtapa->ejecutar($instance, $primeraEtapa);

            event(new FlujoIniciado($instance->fresh()));

            return $instance->fresh(['template', 'documentType', 'etapaActual', 'steps.assignments']);
        });
    }
}