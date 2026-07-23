<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkflowAttachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'workflow_instance_id',
        'workflow_instance_step_id',
        'subido_por',
        'revision',
        'nombre_original',
        'ruta',
        'mime_type',
        'tamano_bytes',
    ];

    protected $casts = [
        'revision'     => 'integer',
        'tamano_bytes' => 'integer',
    ];

    public function instance(): BelongsTo
    {
        return $this->belongsTo(WorkflowInstance::class, 'workflow_instance_id');
    }

    public function step(): BelongsTo
    {
        return $this->belongsTo(WorkflowInstanceStep::class, 'workflow_instance_step_id');
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'subido_por');
    }
}