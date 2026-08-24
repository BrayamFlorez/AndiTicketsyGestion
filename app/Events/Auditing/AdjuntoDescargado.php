<?php

namespace App\Events\Auditing;

use App\Models\User;
use App\Models\WorkflowAttachment;
use App\Models\WorkflowInstance;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AdjuntoDescargado
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public WorkflowInstance $instancia,
        public WorkflowAttachment $adjunto,
        public ?User $usuario = null,
    ) {}
}
