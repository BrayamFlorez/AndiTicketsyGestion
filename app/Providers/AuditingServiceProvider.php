<?php

namespace App\Providers;

use App\Listeners\Auditing\DocumentAuditSubscriber;
use App\Models\DocumentAuditLog;
use App\Models\WorkflowAttachment;
use App\Models\WorkflowInstance;
use App\Observers\Auditing\WorkflowAttachmentAuditObserver;
use App\Observers\Auditing\WorkflowInstanceAuditObserver;
use App\Policies\DocumentAuditLogPolicy;
use App\Services\Auditing\DocumentAuditLogger;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

/**
 * Punto de entrada ÚNICO del módulo de auditoría documental.
 *
 * Todo lo que el módulo necesita enganchar al resto de la aplicación
 * (escuchar eventos de Flujos, observar cambios en WorkflowInstance/
 * WorkflowAttachment, registrar su policy) se conecta aquí — no en
 * App\Providers\EventServiceProvider ni en App\Providers\AppServiceProvider.
 *
 * Esto es lo que hace que el módulo sea "totalmente independiente": para
 * desactivarlo por completo bastaría con quitar UNA línea en
 * bootstrap/providers.php; ningún otro archivo de la aplicación depende
 * de que este provider exista.
 */
class AuditingServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Una sola instancia por request: la reutilizan el subscriber y
        // ambos observers sin recrear el servicio en cada evento.
        $this->app->singleton(DocumentAuditLogger::class);
    }

    public function boot(): void
    {
        Event::subscribe(DocumentAuditSubscriber::class);

        WorkflowInstance::observe(WorkflowInstanceAuditObserver::class);
        WorkflowAttachment::observe(WorkflowAttachmentAuditObserver::class);

        Gate::policy(DocumentAuditLog::class, DocumentAuditLogPolicy::class);
    }
}
