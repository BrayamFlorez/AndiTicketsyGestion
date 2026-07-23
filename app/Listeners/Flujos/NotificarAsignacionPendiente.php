<?php

namespace App\Listeners\Flujos;

use App\Events\Flujos\AsignacionCreada;
use App\Models\User;
use App\Models\WorkflowStageActor;
use App\Notifications\Flujos\AsignacionPendienteNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;

class NotificarAsignacionPendiente implements ShouldQueue
{
    public function handle(AsignacionCreada $event): void
    {
        $assignment = $event->assignment->loadMissing('stageActor', 'step.stage', 'step.instance');
        $instance   = $assignment->step->instance;

        $destinatarios = $this->resolverDestinatarios($assignment);

        if ($destinatarios->isEmpty()) {
            return;
        }

        Notification::send(
            $destinatarios,
            new AsignacionPendienteNotification($instance, $assignment),
        );
    }

    /**
     * Si la asignación apunta a un usuario específico, ese es el destinatario.
     * Si apunta a un rol, todos los usuarios con ese rol son destinatarios.
     */
    private function resolverDestinatarios($assignment)
    {
        if ($assignment->user_id) {
            return User::where('id', $assignment->user_id)->get();
        }

        $stageActor = $assignment->stageActor;

        if (! $stageActor || $stageActor->actor_type !== WorkflowStageActor::TIPO_ROL) {
            return collect();
        }

        $rol = \Spatie\Permission\Models\Role::find($stageActor->actor_id);

        return $rol
            ? User::role($rol->name)->get()
            : collect();
    }
}