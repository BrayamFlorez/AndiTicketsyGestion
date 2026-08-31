<?php

namespace App\Events\Auditing;

use App\Models\User;
use App\Models\WorkflowInstance;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DocumentoVisualizado
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public WorkflowInstance $instancia,
        public ?User $usuario = null,
    ) {}
}
