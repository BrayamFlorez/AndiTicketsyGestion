<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkflowStepAssignment extends Model
{
    use HasFactory;

    public const ESTADO_PENDIENTE = 'pendiente';
    public const ESTADO_APROBADO  = 'aprobado';
    public const ESTADO_RECHAZADO = 'rechazado';
    public const ESTADO_DEVUELTO  = 'devuelto';

    protected $fillable = [
        'workflow_instance_step_id',
        'workflow_stage_actor_id',
        'user_id',
        'estado',
        'comentario',
        'decidido_en',
    ];

    protected $casts = ['decidido_en' => 'datetime'];

    public function step(): BelongsTo
    {
        return $this->belongsTo(WorkflowInstanceStep::class, 'workflow_instance_step_id');
    }

    public function stageActor(): BelongsTo
    {
        return $this->belongsTo(WorkflowStageActor::class, 'workflow_stage_actor_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function estaPendiente(): bool
    {
        return $this->estado === self::ESTADO_PENDIENTE;
    }

    public function fueResuelta(): bool
    {
        return $this->estado !== self::ESTADO_PENDIENTE;
    }
}