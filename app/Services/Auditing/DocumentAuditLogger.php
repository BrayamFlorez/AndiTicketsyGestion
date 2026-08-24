<?php

namespace App\Services\Auditing;

use App\Enums\DocumentAuditAction;
use App\Models\DocumentAuditLog;
use App\Models\User;
use App\Models\WorkflowAttachment;
use App\Models\WorkflowInstance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Punto ÚNICO de escritura hacia document_audit_logs.
 *
 * Todo lo demás (subscriber de eventos de Flujos, observers de modelos,
 * eventos de visualización/descarga) termina llamando a record(). Esto
 * mantiene el módulo de auditoría totalmente autocontenido: el resto de
 * la aplicación nunca sabe cómo se guarda un registro de auditoría, solo
 * dispara eventos o los deja fluir a través de los observers.
 *
 * Diseño deliberado sobre fallas: un fallo al escribir auditoría NUNCA debe
 * tumbar la operación de negocio que la originó (aprobar un documento no
 * puede fallar porque la tabla de auditoría esté momentáneamente caída).
 * Por eso record() atrapa cualquier excepción y la deja registrada en el
 * canal de logs por defecto como 'critical'. Si tu política de cumplimiento
 * exige lo contrario (fallar en seco si no se puede auditar), cambia
 * $failSilently a false o inyecta la config 'auditing.fail_silently'.
 */
class DocumentAuditLogger
{
    public function __construct(
        private readonly Request $request,
        private readonly bool $failSilently = true,
    ) {
    }

    public function record(
        DocumentAuditAction $accion,
        WorkflowInstance $instancia,
        ?User $usuario = null,
        ?WorkflowAttachment $adjunto = null,
        array $contexto = [],
    ): ?DocumentAuditLog {
        try {
            return DocumentAuditLog::create([
                'workflow_instance_id'   => $instancia->id,
                'workflow_attachment_id' => $adjunto?->id,
                'accion'                 => $accion,
                'user_id'                => $usuario?->id,
                'user_name'              => $usuario?->name,
                'user_email'             => $usuario?->email,
                'documento_codigo'       => $instancia->codigo,
                'documento_titulo'       => $instancia->titulo,
                'ip_address'             => $this->resolverIp(),
                'user_agent'             => $this->resolverUserAgent(),
                'metodo_http'            => $this->resolverMetodoHttp(),
                'url'                    => $this->resolverUrl(),
                'contexto'               => $contexto === [] ? null : $contexto,
            ]);
        } catch (Throwable $e) {
            report($e);

            Log::critical('No se pudo escribir el registro de auditoría documental.', [
                'accion'               => $accion->value,
                'workflow_instance_id' => $instancia->id,
                'user_id'              => $usuario?->id,
                'error'                => $e->getMessage(),
            ]);

            if (! $this->failSilently) {
                throw $e;
            }

            return null;
        }
    }

    private function resolverIp(): ?string
    {
        if (app()->runningInConsole()) {
            return 'cli';
        }

        return $this->request->ip();
    }

    private function resolverUserAgent(): ?string
    {
        if (app()->runningInConsole()) {
            return null;
        }

        return substr((string) $this->request->userAgent(), 0, 1000) ?: null;
    }

    private function resolverMetodoHttp(): ?string
    {
        if (app()->runningInConsole()) {
            return null;
        }

        return $this->request->method();
    }

    private function resolverUrl(): ?string
    {
        if (app()->runningInConsole()) {
            return null;
        }

        return substr($this->request->fullUrl(), 0, 2048) ?: null;
    }
}
