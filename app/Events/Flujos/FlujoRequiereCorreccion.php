<?php

namespace App\Events\Flujos;

use App\Models\WorkflowInstance;
use App\Models\WorkflowInstanceStep;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class FlujoRequiereCorreccion
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public WorkflowInstance $instance,
        public WorkflowInstanceStep $stepRechazo,
    ) {}
}