<?php

namespace App\Exceptions\Auditing;

use RuntimeException;

/**
 * Se lanza cuando cualquier capa del sistema (modelo, query builder o
 * trigger de base de datos) detecta un intento de UPDATE o DELETE sobre
 * document_audit_logs. La tabla es de solo inserción por diseño.
 */
class ImmutableAuditLogException extends RuntimeException
{
}
