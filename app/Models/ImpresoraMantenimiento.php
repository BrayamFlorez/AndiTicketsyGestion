<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ImpresoraMantenimiento extends Model
{
    protected $table = 'impresora_mantenimientos';

    // Se registra pendiente de firma; con la firma del técnico y del cliente queda finalizado
    public const ESTADO_PENDIENTE  = 'pendiente_firma';
    public const ESTADO_FINALIZADO = 'finalizado';

    protected $fillable = [
        'impresora_id', 'tipo', 'fecha_realizado', 'realizado_por',
        'proximo_programado', 'contador_paginas', 'descripcion',
        'observaciones', 'registrado_por',
    ];

    protected $casts = [
        'fecha_realizado'    => 'date',
        'proximo_programado' => 'date',
        'contador_paginas'   => 'integer',
        'firma_tecnico_at'   => 'datetime',
        'firma_cliente_at'   => 'datetime',
        'finalizado_at'      => 'datetime',
    ];

    public const TIPOS = [
        'preventivo'         => 'Preventivo',
        'correctivo'         => 'Correctivo',
        'limpieza'           => 'Limpieza',
        'cambio_consumible'  => 'Cambio de consumible',
    ];

    public const TIPOS_ICONO = [
        'preventivo'        => 'fa-shield-alt',
        'correctivo'        => 'fa-wrench',
        'limpieza'          => 'fa-broom',
        'cambio_consumible' => 'fa-fill-drip',
    ];

    public const TIPOS_COLOR = [
        'preventivo'        => 'meta-3',
        'correctivo'        => 'meta-1',
        'limpieza'          => 'primary',
        'cambio_consumible' => 'meta-6',
    ];

    // ── Relaciones ──────────────────────────────────────────────────────

    public function impresora(): BelongsTo
    {
        return $this->belongsTo(Impresora::class);
    }

    public function realizador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'realizado_por')->withTrashed();
    }

    public function registrador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'registrado_por')->withTrashed();
    }

    public function finalizador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'finalizado_por')->withTrashed();
    }

    public function evidencias(): HasMany
    {
        return $this->hasMany(ImpresoraMantenimientoEvidencia::class, 'mantenimiento_id')->oldest('id');
    }

    /** Consumibles nuevos que se instalaron en este mantenimiento */
    public function consumiblesInstalados(): HasMany
    {
        return $this->hasMany(ImpresoraConsumible::class, 'mantenimiento_id');
    }

    /** Lectura del contador que se registró con este mantenimiento */
    public function lecturaContador(): HasOne
    {
        return $this->hasOne(ImpresoraContador::class, 'mantenimiento_id');
    }

    // ── Helpers ─────────────────────────────────────────────────────────

    public function getLabelTipoAttribute(): string
    {
        return self::TIPOS[$this->tipo] ?? ucfirst($this->tipo);
    }

    public function getIconoTipoAttribute(): string
    {
        return self::TIPOS_ICONO[$this->tipo] ?? 'fa-tools';
    }

    public function getColorTipoAttribute(): string
    {
        return self::TIPOS_COLOR[$this->tipo] ?? 'bodydark2';
    }

    public function estaFinalizado(): bool
    {
        return $this->estado === self::ESTADO_FINALIZADO;
    }

    /** Finalizado antes de que existieran las firmas */
    public function esRegistroSinFirmas(): bool
    {
        return $this->estaFinalizado() && ! $this->firma_cliente && ! $this->firma_tecnico;
    }

    /** Carpeta privada con la evidencia y las firmas */
    public function carpetaArchivos(): string
    {
        return "impresoras/mantenimientos/{$this->id}";
    }
}
