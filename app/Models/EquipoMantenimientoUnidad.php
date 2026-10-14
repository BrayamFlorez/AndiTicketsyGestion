<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Fila de "Unidades revisadas": qué se le hizo a cada componente o periférico. */
class EquipoMantenimientoUnidad extends Model
{
    protected $table = 'equipo_mantenimiento_unidades';

    public $timestamps = false;

    protected $fillable = [
        'mantenimiento_id', 'periferico_id', 'orden', 'unidad',
        'limpieza', 'ajuste', 'lubricacion', 'cambio_instalacion', 'reparacion', 'revision',
    ];

    protected $casts = [
        'limpieza'           => 'boolean',
        'ajuste'             => 'boolean',
        'lubricacion'        => 'boolean',
        'cambio_instalacion' => 'boolean',
        'reparacion'         => 'boolean',
        'revision'           => 'boolean',
    ];

    public function mantenimiento(): BelongsTo
    {
        return $this->belongsTo(EquipoMantenimiento::class, 'mantenimiento_id');
    }

    public function periferico(): BelongsTo
    {
        return $this->belongsTo(EquipoPeriferico::class, 'periferico_id');
    }

    public function tieneAcciones(): bool
    {
        return collect(array_keys(EquipoMantenimiento::ACCIONES))->contains(fn ($a) => $this->{$a});
    }
}
