<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Bitácora de eventos del proyecto que no son etapas ni solicitudes
 * (creación, cambios de estado, equipo, eliminaciones). Solo inserción.
 */
class ProyectoActividad extends Model
{
    protected $table = 'proyecto_actividades';

    public const UPDATED_AT = null;

    protected $fillable = [
        'proyecto_id',
        'user_id',
        'accion',
        'descripcion',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public const ICONOS = [
        'creado'            => 'fas fa-flag',
        'estado'            => 'fas fa-arrows-rotate',
        'equipo'            => 'fas fa-users',
        'editado'           => 'fas fa-pen',
        'etapa_eliminada'   => 'fas fa-trash-can',
        'solicitud_eliminada' => 'fas fa-trash-can',
    ];

    public function proyecto(): BelongsTo
    {
        return $this->belongsTo(Proyecto::class);
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id')->withTrashed();
    }

    public function getIconoAttribute(): string
    {
        return self::ICONOS[$this->accion] ?? 'fas fa-circle-info';
    }

    public static function registrar(Proyecto $proyecto, string $accion, string $descripcion, ?User $user = null): self
    {
        return self::create([
            'proyecto_id' => $proyecto->id,
            'user_id'     => $user?->id,
            'accion'      => $accion,
            'descripcion' => mb_substr($descripcion, 0, 500),
        ]);
    }
}
