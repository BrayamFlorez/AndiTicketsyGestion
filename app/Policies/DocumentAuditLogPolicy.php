<?php

namespace App\Policies;

use App\Models\DocumentAuditLog;
use App\Models\User;

/**
 * Autorización deliberadamente independiente de WorkflowInstancePolicy:
 * ver el RASTRO de auditoría de un documento es un permiso distinto a ver
 * el documento en sí (un auditor puede necesitar ver quién hizo qué sin
 * necesariamente tener acceso al contenido del expediente).
 *
 * Usa el permiso 'auditoria.ver', que YA existe en
 * database/seeders/RolesAndPermissionsSeeder.php (grupo "sistema") y ya
 * está referenciado en resources/views/layouts/partials/sidebar.blade.php
 * — no requiere ningún seeder ni permiso nuevo.
 */
class DocumentAuditLogPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('auditoria.ver');
    }

    public function view(User $user, DocumentAuditLog $registro): bool
    {
        return $user->can('auditoria.ver');
    }
}
