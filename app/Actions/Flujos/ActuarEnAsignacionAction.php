<?php

namespace App\Actions\Flujos;

use App\Events\Flujos\AsignacionActuada;
use App\Models\User;
use App\Models\WorkflowInstanceStep;
use App\Models\WorkflowStageActor;
use App\Models\WorkflowStepAssignment;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Role;

class ActuarEnAsignacionAction
{
    public const ACCION_APROBAR  = 'aprobar';
    public const ACCION_RECHAZAR = 'rechazar';
    public const ACCION_DEVOLVER = 'devolver';

    private const MAPA_ESTADOS = [
        self::ACCION_APROBAR  => WorkflowStepAssignment::ESTADO_APROBADO,
        self::ACCION_RECHAZAR => WorkflowStepAssignment::ESTADO_RECHAZADO,
        self::ACCION_DEVOLVER => WorkflowStepAssignment::ESTADO_DEVUELTO,
    ];

    public function __construct(private EvaluarAvanceEtapaAction $evaluador) {}

    public function ejecutar(
        WorkflowStepAssignment $assignment,
        User $usuario,
        string $accion,
        ?string $comentario = null,
    ): WorkflowStepAssignment {

        if (! isset(self::MAPA_ESTADOS[$accion])) {
            throw ValidationException::withMessages([
                'accion' => "Acción no soportada: {$accion}",
            ]);
        }

        if (! $assignment->estaPendiente()) {
            throw ValidationException::withMessages([
                'assignment' => 'Esta acción ya fue resuelta.',
            ]);
        }

        $step = $assignment->step()->with('instance', 'stage')->first();

        if (! $step->estaEnCurso()) {
            throw ValidationException::withMessages([
                'step' => 'La etapa ya no está en curso.',
            ]);
        }

        if (! $step->instance->estaAbierta()) {
            throw ValidationException::withMessages([
                'instance' => 'El flujo ya no admite acciones.',
            ]);
        }

        $this->verificarAutorizacion($assignment, $usuario);

        // Requisito de adjunto (solo para aprobar)
        if ($accion === self::ACCION_APROBAR && $step->stage->requiere_adjunto) {
            $tieneAdjunto = $step->attachments()->where('subido_por', $usuario->id)->exists();

            if (! $tieneAdjunto) {
                throw ValidationException::withMessages([
                    'adjunto' => 'Esta etapa requiere adjuntar al menos un archivo antes de aprobar.',
                ]);
            }
        }

        // Requerimos comentario para rechazar/devolver
        if (in_array($accion, [self::ACCION_RECHAZAR, self::ACCION_DEVOLVER], true) && blank($comentario)) {
            throw ValidationException::withMessages([
                'comentario' => 'Debe indicar un motivo al rechazar o devolver.',
            ]);
        }

        return DB::transaction(function () use ($assignment, $usuario, $accion, $comentario, $step) {

            $assignment->update([
                'user_id'     => $usuario->id,
                'estado'      => self::MAPA_ESTADOS[$accion],
                'comentario'  => $comentario,
                'decidido_en' => now(),
            ]);

            event(new AsignacionActuada($assignment->fresh()));

            // Delegamos al evaluador: puede avanzar, devolver o quedarse esperando
            $this->evaluador->ejecutar($step->fresh());

            return $assignment->fresh();
        });
    }

    /**
     * Un usuario puede actuar si:
     *  1) La asignación ya está apuntada a él (usuario específico), o
     *  2) La asignación es "por rol" (user_id null) y el usuario tiene ese rol.
     */
    private function verificarAutorizacion(WorkflowStepAssignment $assignment, User $usuario): void
    {
        if ($assignment->user_id === $usuario->id) {
            return;
        }

        $stageActor = $assignment->stageActor;

        if ($assignment->user_id === null
            && $stageActor?->actor_type === WorkflowStageActor::TIPO_ROL
        ) {
            $rol = Role::find($stageActor->actor_id);

            if ($rol && $usuario->hasRole($rol->name)) {
                return;
            }
        }

        throw ValidationException::withMessages([
            'autorizacion' => 'No estás autorizado a actuar en esta asignación.',
        ]);
    }
}