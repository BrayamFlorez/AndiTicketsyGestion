<?php

namespace App\Events\Flujos;

use App\Models\WorkflowInstance;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class FlujoCerrado
{
    use Dispatchable, SerializesModels;

    public function __construct(public WorkflowInstance $instance) {}
}