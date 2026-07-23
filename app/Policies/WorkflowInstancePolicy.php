<?php

namespace App\Policies;

use App\Models\User;
use App\Models\WorkflowInstance;
use App\Models\WorkflowInstanceStep;
use App\Models\WorkflowStageActor;
use App\Models\WorkflowAttachment;

class WorkflowInstancePolicy
{
    /**
     * Ver la instancia: iniciador, participante actual o histórico, o quien tenga ver-todos.
     */
    public function view(User $user, WorkflowInstance $instance): bool
    {
        if ($user->can('flujos.ver-todos')) {
            return true;
        }

        if ($instance->iniciado_por === $user->id) {
            return true;
        }

        // Alguna asignación pasada o presente apuntada al usuario
        $participaPorAsignacion = $instance->steps()
            ->whereHas('assignments', fn ($q) => $q->where('user_id', $user->id))
            ->exists();

        if ($participaPorAsignacion) {
            return true;
        }

        // Puede actuar por rol en el step actual
        $idsRolesUsuario = $user->roles()->pluck('id')->all();

        return $instance->steps()
            ->where('estado', WorkflowInstanceStep::ESTADO_EN_CURSO)
            ->whereHas('assignments', function ($q) use ($idsRolesUsuario) {
                $q->whereNull('user_id')
                  ->whereHas('stageActor', function ($sq) use ($idsRolesUsuario) {
                      $sq->where('actor_type', WorkflowStageActor::TIPO_ROL)
                         ->whereIn('actor_id', $idsRolesUsuario);
                  });
            })
            ->exists();
    }

    public function corregir(User $user, WorkflowInstance $instance): bool
    {
        return $user->can('flujos.corregir-propio')
            && $instance->iniciado_por === $user->id
            && $instance->estado === WorkflowInstance::ESTADO_REQUIERE_CORRECCION;
    }

    public function desistir(User $user, WorkflowInstance $instance): bool
    {
        return $user->can('flujos.desistir-propio')
            && $instance->iniciado_por === $user->id
            && $instance->estaAbierta();
    }

    public function cancelar(User $user, WorkflowInstance $instance): bool
    {
        return $user->can('flujos.cancelar-cualquiera')
            && $instance->estaAbierta();
    }

    public function reabrir(User $user, WorkflowInstance $instance): bool
    {
        return $user->can('flujos.reabrir')
            && $instance->estado === WorkflowInstance::ESTADO_CANCELADO;
    }

    /**
     * Solo el iniciador, mientras la instancia esté abierta.
     */
    public function subirAdjuntoInstancia(User $user, WorkflowInstance $instance): bool
    {
        return $instance->iniciado_por === $user->id
            && $instance->estaAbierta();
    }

    /**
     * Actores con asignación pendiente en el step indicado, mientras el step siga en curso.
     */
    public function subirAdjuntoStep(User $user, WorkflowInstance $instance, WorkflowInstanceStep $step): bool
    {
        if ($step->workflow_instance_id !== $instance->id) {
            return false;
        }

        if (! $step->estaEnCurso() || ! $instance->estaAbierta()) {
            return false;
        }

        $idsRolesUsuario = $user->roles()->pluck('id')->all();

        return $step->assignments()
            ->where('estado', \App\Models\WorkflowStepAssignment::ESTADO_PENDIENTE)
            ->where(function ($q) use ($user, $idsRolesUsuario) {
                $q->where('user_id', $user->id)
                  ->orWhere(function ($rq) use ($idsRolesUsuario) {
                      $rq->whereNull('user_id')
                         ->whereHas('stageActor', function ($sq) use ($idsRolesUsuario) {
                             $sq->where('actor_type', WorkflowStageActor::TIPO_ROL)
                                ->whereIn('actor_id', $idsRolesUsuario);
                         });
                  });
            })
            ->exists();
    }

    /**
     * El que subió el adjunto puede eliminarlo mientras la instancia siga abierta
     * y — si el adjunto pertenece a un step — mientras ese step siga en curso.
     */
    public function eliminarAdjunto(User $user, \App\Models\WorkflowAttachment $attachment): bool
    {
        if ($attachment->subido_por !== $user->id) {
            return false;
        }

        if (! $attachment->instance->estaAbierta()) {
            return false;
        }

        if ($attachment->workflow_instance_step_id) {
            return $attachment->step?->estaEnCurso() ?? false;
        }

        return true;
    }
}