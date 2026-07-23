<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WorkflowInstanceStep extends Model
{
    use HasFactory;

    public const ESTADO_PENDIENTE = 'pendiente';
    public const ESTADO_EN_CURSO  = 'en_curso';
    public const ESTADO_APROBADO  = 'aprobado';
    public const ESTADO_RECHAZADO = 'rechazado';
    public const ESTADO_DEVUELTO  = 'devuelto';
    public const ESTADO_OMITIDO   = 'omitido';

    protected $fillable = [
        'workflow_instance_id',
        'workflow_stage_id',
        'revision',
        'estado',
        'inicio_en',
        'cierre_en',
    ];

    protected $casts = [
        'revision'  => 'integer',
        'inicio_en' => 'datetime',
        'cierre_en' => 'datetime',
    ];

    public function instance(): BelongsTo
    {
        return $this->belongsTo(WorkflowInstance::class, 'workflow_instance_id');
    }

    public function stage(): BelongsTo
    {
        return $this->belongsTo(WorkflowStage::class, 'workflow_stage_id');
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(WorkflowStepAssignment::class);
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(WorkflowAttachment::class);
    }

    public function estaEnCurso(): bool
    {
        return $this->estado === self::ESTADO_EN_CURSO;
    }
}