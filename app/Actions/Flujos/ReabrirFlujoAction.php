<?php

namespace App\Actions\Flujos;

use App\Models\WorkflowInstance;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ReabrirFlujoAction
{
    public function __construct(private IniciarEtapaAction $iniciarEtapa) {}

    public function ejecutar(WorkflowInstance $instance): WorkflowInstance
    {
        if ($instance->estado !== WorkflowInstance::ESTADO_CANCELADO) {
            throw ValidationException::withMessages([
                'instance' => 'Solo se pueden reabrir flujos cancelados.',
            ]);
        }

        return DB::transaction(function () use ($instance) {

            $ultimoStep = $instance->steps()->latest('id')->with('stage')->first();

            $etapaObjetivo = $ultimoStep?->stage
                ?? $instance->template->stages()->orderBy('orden')->first();

            if (! $etapaObjetivo) {
                throw ValidationException::withMessages([
                    'instance' => 'La plantilla no tiene etapas disponibles para reabrir.',
                ]);
            }

            $instance->update([
                'estado'     => WorkflowInstance::ESTADO_EN_PROGRESO,
                'cerrado_en' => null,
            ]);

            $this->iniciarEtapa->ejecutar($instance->fresh(), $etapaObjetivo);

            return $instance->fresh();
        });
    }
}