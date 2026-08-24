<?php

namespace App\Support\Auditing;

use App\Exceptions\Auditing\ImmutableAuditLogException;
use Illuminate\Database\Eloquent\Builder;

/**
 * Un update()/delete() a nivel de instancia (p. ej. $log->delete()) dispara
 * los eventos "updating"/"deleting" del modelo (ver App\Models\DocumentAuditLog),
 * pero esos eventos NO se disparan en updates/deletes masivos hechos por query
 * builder (DocumentAuditLog::where(...)->update(...) o ->delete()). Este builder
 * cierra exactamente ese hueco.
 *
 * @template TModel of \Illuminate\Database\Eloquent\Model
 * @extends Builder<TModel>
 */
class ImmutableAuditQueryBuilder extends Builder
{
    public function update(array $values = []): int
    {
        throw new ImmutableAuditLogException(
            'document_audit_logs es de solo inserción: no se permite UPDATE masivo por query builder.'
        );
    }

    public function delete(): int|false
    {
        throw new ImmutableAuditLogException(
            'document_audit_logs es de solo inserción: no se permite DELETE masivo por query builder.'
        );
    }

    public function forceDelete(): bool|int|null
    {
        throw new ImmutableAuditLogException(
            'document_audit_logs es de solo inserción: no se permite DELETE masivo por query builder.'
        );
    }
}
