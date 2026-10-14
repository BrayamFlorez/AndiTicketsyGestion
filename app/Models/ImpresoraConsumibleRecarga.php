<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Recarga parcial de un consumible (se agregó solo una porción, por ejemplo 20 % o 50 %),
 * con la medición del técnico y la recalibración que produjo.
 */
class ImpresoraConsumibleRecarga extends Model
{
    protected $table = 'impresora_consumible_recargas';

    protected $fillable = [
        'impresora_id', 'consumible_id',
        'nivel_estimado_sistema', 'nivel_antes', 'nivel_despues', 'porcion_recargada',
        'desviacion_porcentaje', 'desviacion_dias', 'dias_desde_referencia',
        'dias_estimados_anteriores', 'dias_estimados_nuevos', 'recalibrado', 'dias_restantes_estimados',
        'observaciones', 'registrado_por',
    ];

    protected $casts = [
        'impresora_id'              => 'integer',
        'consumible_id'             => 'integer',
        'nivel_estimado_sistema'    => 'float',
        'nivel_antes'               => 'float',
        'nivel_despues'             => 'float',
        'porcion_recargada'         => 'float',
        'desviacion_porcentaje'     => 'float',
        'desviacion_dias'           => 'integer',
        'dias_desde_referencia'     => 'float',
        'dias_estimados_anteriores' => 'integer',
        'dias_estimados_nuevos'     => 'integer',
        'recalibrado'               => 'boolean',
        'dias_restantes_estimados'  => 'integer',
        'registrado_por'            => 'integer',
    ];

    public function consumible(): BelongsTo
    {
        return $this->belongsTo(ImpresoraConsumible::class, 'consumible_id');
    }

    public function impresora(): BelongsTo
    {
        return $this->belongsTo(Impresora::class);
    }

    public function registrador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'registrado_por');
    }
}
