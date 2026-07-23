<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WorkflowStage extends Model
{
    use HasFactory;

    // Modos de evaluación
    public const MODO_SECUENCIAL          = 'secuencial';
    public const MODO_PARALELO_TODOS      = 'paralelo_todos';
    public const MODO_PARALELO_CUALQUIERA = 'paralelo_cualquiera';

    // Políticas de rechazo
    public const RECHAZO_DEVOLVER_INICIADOR       = 'devolver_iniciador';
    public const RECHAZO_DEVOLVER_ETAPA_ANTERIOR  = 'devolver_etapa_anterior';
    public const RECHAZO_DEVOLVER_ETAPA_ESPECIFICA = 'devolver_etapa_especifica';
    public const RECHAZO_FINALIZAR                = 'finalizar';

    protected $fillable = [
        'workflow_template_id',
        'nombre',
        'descripcion',
        'orden',
        'modo',
        'politica_rechazo',
        'etapa_destino_rechazo_id',
        'requiere_adjunto',
        'sla_horas',
    ];

    protected $casts = [
        'orden'            => 'integer',
        'sla_horas'        => 'integer',
        'requiere_adjunto' => 'boolean',
    ];

    public function template(): BelongsTo
    {
        return $this->belongsTo(WorkflowTemplate::class, 'workflow_template_id');
    }

    public function actors(): HasMany
    {
        return $this->hasMany(WorkflowStageActor::class);
    }

    public function etapaDestinoRechazo(): BelongsTo
    {
        return $this->belongsTo(self::class, 'etapa_destino_rechazo_id');
    }

    public function esParalela(): bool
    {
        return in_array($this->modo, [
            self::MODO_PARALELO_TODOS,
            self::MODO_PARALELO_CUALQUIERA,
        ], true);
    }
}