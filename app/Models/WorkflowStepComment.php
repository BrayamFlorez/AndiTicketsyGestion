<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkflowStepComment extends Model
{
    protected $fillable = [
        'workflow_instance_step_id',
        'user_id',
        'comentario',
    ];

    public function step(): BelongsTo
    {
        return $this->belongsTo(WorkflowInstanceStep::class, 'workflow_instance_step_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->withTrashed();
    }
}