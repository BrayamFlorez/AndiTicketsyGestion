<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Fila de "Solicitud y cambio de partes". */
class EquipoMantenimientoParte extends Model
{
    protected $table = 'equipo_mantenimiento_partes';

    public $timestamps = false;

    protected $fillable = ['mantenimiento_id', 'numero_parte', 'nombre', 'serial_original', 'serial_reemplazo'];

    public function mantenimiento(): BelongsTo
    {
        return $this->belongsTo(EquipoMantenimiento::class, 'mantenimiento_id');
    }
}
