<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MantenimientoPreventivo extends Model
{
    protected $table = 'mantenimientos_preventivos';

    protected $fillable = [
        'equipo_id',
        'tecnico_id',
        'fecha_programada',
        'fecha_inicio',
        'fecha_finalizacion',
        'estado',
        'hw_limpieza_interna',
        'hw_limpieza_externa',
        'hw_cambio_pasta_termica',
        'hw_revision_ventiladores',
        'hw_verificacion_cables',
        'sw_limpieza_temporales',
        'sw_actualizacion_so',
        'sw_revision_antivirus',
        'sw_revision_disco_duro',
        'sw_desinstalacion_basura',
        'observaciones_tecnicas',
        'recomendaciones_cliente',
        'firma_cliente_path',
        'evidencia_fotografica_path',
    ];

    protected $casts = [
        'fecha_programada'         => 'date',
        'fecha_inicio'             => 'datetime',
        'fecha_finalizacion'       => 'datetime',
        
        // Convertimos los tinyint de MySQL a booleanos puros en PHP
        'hw_limpieza_interna'      => 'boolean',
        'hw_limpieza_externa'      => 'boolean',
        'hw_cambio_pasta_termica'  => 'boolean',
        'hw_revision_ventiladores' => 'boolean',
        'hw_verificacion_cables'   => 'boolean',
        'sw_limpieza_temporales'   => 'boolean',
        'sw_actualizacion_so'      => 'boolean',
        'sw_revision_antivirus'    => 'boolean',
        'sw_revision_disco_duro'   => 'boolean',
        'sw_desinstalacion_basura' => 'boolean',
    ];

    // ── Relaciones ──

    public function equipo(): BelongsTo
    {
        return $this->belongsTo(Equipo::class);
    }

    public function tecnico(): BelongsTo
    {
        return $this->belongsTo(User::class, 'tecnico_id');
    }
}