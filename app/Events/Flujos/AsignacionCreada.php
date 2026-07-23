<?php

namespace App\Events\Flujos;

use App\Models\WorkflowStepAssignment;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AsignacionCreada
{
    use Dispatchable, SerializesModels;

    public function __construct(public WorkflowStepAssignment $assignment) {}
}