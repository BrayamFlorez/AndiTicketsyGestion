<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Lectura del contador de páginas de una impresora. La fecha de la lectura es created_at
 * (la del mantenimiento si vino de uno). mantenimiento_id null = registro manual.
 */
class ImpresoraContador extends Model
{
    protected $table = 'impresora_contadores';

    protected $fillable = ['impresora_id', 'mantenimiento_id', 'contador', 'paginas_desde_anterior', 'reinicio', 'registrado_por'];

    protected $casts = [
        'impresora_id'           => 'integer',
        'mantenimiento_id'       => 'integer',
        'contador'               => 'integer',
        'paginas_desde_anterior' => 'integer',
        'reinicio'               => 'boolean',
        'registrado_por'         => 'integer',
    ];

    public function impresora(): BelongsTo
    {
        return $this->belongsTo(Impresora::class);
    }

    public function mantenimiento(): BelongsTo
    {
        return $this->belongsTo(ImpresoraMantenimiento::class, 'mantenimiento_id');
    }

    public function registrador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'registrado_por');
    }
}
