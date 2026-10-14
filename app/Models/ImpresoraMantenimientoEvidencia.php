<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Foto de evidencia de un mantenimiento: antes, después u otra. */
class ImpresoraMantenimientoEvidencia extends Model
{
    protected $table = 'impresora_mantenimiento_evidencias';

    public const MOMENTOS = [
        'antes'   => 'Antes',
        'despues' => 'Después',
        'otra'    => 'Otras',
    ];

    protected $fillable = ['mantenimiento_id', 'momento', 'archivo', 'descripcion', 'subido_por'];

    public function mantenimiento(): BelongsTo
    {
        return $this->belongsTo(ImpresoraMantenimiento::class, 'mantenimiento_id');
    }

    public function subidor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'subido_por')->withTrashed();
    }

    public function getLabelMomentoAttribute(): string
    {
        return self::MOMENTOS[$this->momento] ?? ucfirst($this->momento);
    }
}
