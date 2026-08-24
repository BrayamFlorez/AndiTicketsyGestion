<?php

namespace App\Http\Controllers;

use App\Enums\DocumentAuditAction;
use App\Models\DocumentAuditLog;
use App\Models\WorkflowInstance;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Controlador 100% de lectura: no existen métodos store/update/destroy
 * a propósito, porque la tabla que consulta es de solo inserción. Es el
 * único punto de entrada HTTP de todo el módulo de auditoría.
 */
class DocumentAuditLogController extends Controller
{
    use AuthorizesRequests;

    /**
     * Bandeja general de auditoría, con filtros básicos. Pensada para
     * super-admin/admin o un rol de auditoría/cumplimiento.
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', DocumentAuditLog::class);

        $filtros = $request->only(['q', 'accion', 'desde', 'hasta', 'ip']);

        $registros = DocumentAuditLog::query()
            ->when($filtros['q'] ?? null, function ($query, $texto) {
                $query->where(function ($sub) use ($texto) {
                    $sub->where('documento_codigo', 'like', "%{$texto}%")
                        ->orWhere('documento_titulo', 'like', "%{$texto}%")
                        ->orWhere('user_name', 'like', "%{$texto}%")
                        ->orWhere('user_email', 'like', "%{$texto}%");
                });
            })
            ->when($filtros['accion'] ?? null, fn ($query, $v) => $query->where('accion', $v))
            ->when($filtros['ip'] ?? null, fn ($query, $v) => $query->where('ip_address', $v))
            ->when($filtros['desde'] ?? null, fn ($query, $v) => $query->whereDate('created_at', '>=', $v))
            ->when($filtros['hasta'] ?? null, fn ($query, $v) => $query->whereDate('created_at', '<=', $v))
            ->orderByDesc('created_at')
            ->paginate(25)
            ->withQueryString();

        $acciones = DocumentAuditAction::cases();

        return view('auditoria.index', compact('registros', 'filtros', 'acciones'));
    }

    /**
     * Historial cronológico completo de UN documento puntual. No depende
     * de que la instancia siga existiendo con los mismos datos: los
     * campos "documento_codigo"/"documento_titulo" quedan congelados en
     * cada fila al momento del evento.
     */
    public function show(WorkflowInstance $instancia): View
    {
        $this->authorize('viewAny', DocumentAuditLog::class);

        $registros = DocumentAuditLog::query()
            ->where('workflow_instance_id', $instancia->id)
            ->orderBy('created_at')
            ->get();

        return view('auditoria.show', compact('instancia', 'registros'));
    }
}
