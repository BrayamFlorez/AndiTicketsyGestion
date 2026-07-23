<?php

namespace App\Actions\Flujos;

use App\Events\Flujos\FlujoCerrado;
use App\Models\User;
use App\Models\WorkflowInstance;
use App\Models\WorkflowInstanceStep;
use App\Models\WorkflowStepAssignment;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CancelarFlujoAction
{
    public function ejecutar(WorkflowInstance $instance, User $usuario, ?string $motivo = null): WorkflowInstance
    {
        if (! $instance->estaAbierta()) {
            throw ValidationException::withMessages([
                'instance' => 'El flujo ya está cerrado.',
            ]);
        }

        return DB::transaction(function () use ($instance, $motivo, $usuario) {

            $stepActual = $instance->steps()
                ->where('estado', WorkflowInstanceStep::ESTADO_EN_CURSO)
                ->first();

            if ($stepActual) {
                $stepActual->update([
                    'estado'    => WorkflowInstanceStep::ESTADO_OMITIDO,
                    'cierre_en' => now(),
                ]);

                $stepActual->assignments()
                    ->where('estado', WorkflowStepAssignment::ESTADO_PENDIENTE)
                    ->update([
                        'estado'      => WorkflowStepAssignment::ESTADO_DEVUELTO,
                        'decidido_en' => now(),
                        'comentario'  => $motivo ? "(Cancelado: {$motivo})" : '(Cancelado)',
                    ]);
            }

            $instance->update([
                'estado'     => WorkflowInstance::ESTADO_CANCELADO,
                'cerrado_en' => now(),
            ]);

            event(new FlujoCerrado($instance->fresh(), $usuario, $motivo));

            return $instance->fresh();
        });
    }
}