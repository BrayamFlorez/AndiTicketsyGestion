<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WorkflowInstance extends Model
{
    use HasFactory;

    public const ESTADO_EN_PROGRESO         = 'en_progreso';
    public const ESTADO_REQUIERE_CORRECCION = 'requiere_correccion';
    public const ESTADO_APROBADO            = 'aprobado';
    public const ESTADO_CANCELADO           = 'cancelado';
    public const ESTADO_ARCHIVADO           = 'archivado';

    protected $fillable = [
        'codigo',
        'workflow_template_id',
        'document_type_id',
        'titulo',
        'descripcion',
        'iniciado_por',
        'estado',
        'etapa_actual_id',
        'revision',
        'iniciado_en',
        'cerrado_en',
    ];

    protected $casts = [
        'revision'    => 'integer',
        'iniciado_en' => 'datetime',
        'cerrado_en'  => 'datetime',
    ];

    public function template(): BelongsTo
    {
        return $this->belongsTo(WorkflowTemplate::class, 'workflow_template_id');
    }

    public function documentType(): BelongsTo
    {
        return $this->belongsTo(DocumentType::class, 'document_type_id');
    }

    public function iniciador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'iniciado_por');
    }

    public function etapaActual(): BelongsTo
    {
        return $this->belongsTo(WorkflowStage::class, 'etapa_actual_id');
    }

    public function steps(): HasMany
    {
        return $this->hasMany(WorkflowInstanceStep::class);
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(WorkflowAttachment::class);
    }

    /**
     * Verdadero si aún acepta acciones (progresando o esperando corrección).
     */
    public function estaAbierta(): bool
    {
        return in_array($this->estado, [
            self::ESTADO_EN_PROGRESO,
            self::ESTADO_REQUIERE_CORRECCION,
        ], true);
    }

    public function scopeAbiertas($query)
    {
        return $query->whereIn('estado', [
            self::ESTADO_EN_PROGRESO,
            self::ESTADO_REQUIERE_CORRECCION,
        ]);
    }
}