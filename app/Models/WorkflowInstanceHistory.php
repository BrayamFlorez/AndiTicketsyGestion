<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkflowInstanceHistory extends Model
{
    protected $table = 'workflow_instance_history';

    public $timestamps = false;

    protected $fillable = [
        'workflow_instance_id',
        'editado_por',
        'campo',
        'valor_anterior',
        'valor_nuevo',
        'editado_en',
    ];

    protected $casts = [
        'editado_en' => 'datetime',
    ];

    public const CAMPOS_EDITABLES = [
        'titulo'             => 'Título',
        'descripcion'        => 'Descripción',
        'referencia_externa' => 'Código de referencia',
    ];

    public function instance(): BelongsTo
    {
        return $this->belongsTo(WorkflowInstance::class, 'workflow_instance_id');
    }

    public function editor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'editado_por')->withTrashed();
    }

    public function getLabelCampoAttribute(): string
    {
        return self::CAMPOS_EDITABLES[$this->campo] ?? ucfirst($this->campo);
    }
}