<?php

namespace App\Events\Flujos;

use App\Models\User;
use App\Models\WorkflowInstance;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class FlujoCerrado
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public WorkflowInstance $instance,
        public ?User $actor = null,      // Quien disparó el cierre (último aprobador, cancelador, o rechazo terminal)
        public ?string $motivo = null,   // Comentario del cancelador o del rechazo terminal
    ) {}
}