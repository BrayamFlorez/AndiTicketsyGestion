<?php

namespace App\Actions\Flujos;

use App\Models\User;
use App\Models\WorkflowInstance;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class EnviarCorreccionAction
{
    public function __construct(private IniciarEtapaAction $iniciarEtapa) {}

    public function ejecutar(WorkflowInstance $instance, User $iniciador): WorkflowInstance
    {
        if ($instance->estado !== WorkflowInstance::ESTADO_REQUIERE_CORRECCION) {
            throw ValidationException::withMessages([
                'instance' => 'El flujo no está esperando corrección.',
            ]);
        }

        if ($instance->iniciado_por !== $iniciador->id) {
            throw ValidationException::withMessages([
                'autorizacion' => 'Solo el iniciador puede reenviar tras corrección.',
            ]);
        }

        $primeraEtapa = $instance->template->stages()->orderBy('orden')->first();

        if (! $primeraEtapa) {
            throw ValidationException::withMessages([
                'plantilla' => 'La plantilla ya no tiene etapas configuradas.',
            ]);
        }

        return DB::transaction(function () use ($instance, $primeraEtapa) {
            // Incrementar revisión ANTES de crear el nuevo step,
            // así IniciarEtapaAction lo etiqueta con el número correcto.
            $instance->update([
                'revision' => $instance->revision + 1,
                'estado'   => WorkflowInstance::ESTADO_EN_PROGRESO,
            ]);

            $this->iniciarEtapa->ejecutar($instance->fresh(), $primeraEtapa);

            return $instance->fresh();
        });
    }
}