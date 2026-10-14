<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Servicio técnico a un PC (preventivo o correctivo) con los campos del formato
 * "Contrato de prestación de servicio". Se registra pendiente de firma; con la firma
 * del técnico y la del cliente a la salida queda finalizado y se habilita el PDF.
 */
class EquipoMantenimiento extends Model
{
    protected $table = 'equipo_mantenimientos';

    public const ESTADO_PENDIENTE  = 'pendiente_firma';
    public const ESTADO_FINALIZADO = 'finalizado';

    public const ESTADOS = [
        self::ESTADO_PENDIENTE  => 'Pendiente de firma',
        self::ESTADO_FINALIZADO => 'Finalizado',
    ];

    public const TIPOS = [
        'preventivo' => 'Mantenimiento preventivo',
        'correctivo' => 'Mantenimiento correctivo',
    ];

    public const MODALIDADES = [
        'contrato_arrendamiento'  => 'Contrato de arrendamiento',
        'contrato_mantenimiento'  => 'Contrato de mantenimiento',
        'garantia'                => 'Garantía',
        'servicio_puntual'        => 'Servicio puntual',
    ];

    /** columna => [encabezado, convención] (en el orden del formato) */
    public const ACCIONES = [
        'limpieza'           => ['Limpieza', 'L'],
        'ajuste'             => ['Ajuste', 'A'],
        'lubricacion'        => ['Lubric.', 'Lu'],
        'cambio_instalacion' => ['C/I', 'C/I'],
        'reparacion'         => ['Repar.', 'Repar.'],
        'revision'           => ['Rev.', 'Rev.'],
    ];

    protected $fillable = [
        'equipo_id', 'ticket_id', 'tipo', 'modalidad', 'deposito',
        'fecha_ingreso', 'fecha_servicio', 'hora_inicio', 'hora_fin',
        'contador_inicial', 'contador_final', 'tecnico_id', 'falla', 'observaciones', 'registrado_por',
    ];

    protected $casts = [
        'fecha_ingreso'    => 'date',
        'fecha_servicio'   => 'date',
        'firma_tecnico_at' => 'datetime',
        'firma_cliente_at' => 'datetime',
        'finalizado_at'    => 'datetime',
    ];

    // ── Relaciones ──────────────────────────────────────────────────────

    public function equipo(): BelongsTo      { return $this->belongsTo(Equipo::class); }
    public function ticket(): BelongsTo      { return $this->belongsTo(Ticket::class); }
    public function tecnico(): BelongsTo     { return $this->belongsTo(User::class, 'tecnico_id')->withTrashed(); }
    public function registrador(): BelongsTo { return $this->belongsTo(User::class, 'registrado_por')->withTrashed(); }
    public function finalizador(): BelongsTo { return $this->belongsTo(User::class, 'finalizado_por')->withTrashed(); }

    public function unidades(): HasMany
    {
        return $this->hasMany(EquipoMantenimientoUnidad::class, 'mantenimiento_id')->orderBy('orden')->orderBy('id');
    }

    public function partes(): HasMany
    {
        return $this->hasMany(EquipoMantenimientoParte::class, 'mantenimiento_id')->orderBy('id');
    }

    public function evidencias(): HasMany
    {
        return $this->hasMany(EquipoMantenimientoEvidencia::class, 'mantenimiento_id')->oldest('id');
    }

    // ── Scopes ──────────────────────────────────────────────────────────

    public function scopePendientes(Builder $query): Builder
    {
        return $query->where('estado', self::ESTADO_PENDIENTE);
    }

    // ── Helpers ─────────────────────────────────────────────────────────

    public function getLabelTipoAttribute(): string      { return self::TIPOS[$this->tipo] ?? ucfirst($this->tipo); }
    public function getLabelModalidadAttribute(): ?string { return $this->modalidad ? (self::MODALIDADES[$this->modalidad] ?? $this->modalidad) : null; }
    public function getLabelEstadoAttribute(): string    { return self::ESTADOS[$this->estado] ?? ucfirst((string) $this->estado); }

    /** Número de orden del reporte: prefijo configurable + id */
    public function getNumeroOrdenAttribute(): string
    {
        return config('reportes.equipos.prefijo_orden', 'MT-') . str_pad((string) $this->id, 5, '0', STR_PAD_LEFT);
    }

    public function estaFinalizado(): bool
    {
        return $this->estado === self::ESTADO_FINALIZADO;
    }

    /** Carpeta privada con la evidencia y las firmas */
    public function carpetaArchivos(): string
    {
        return "equipos/mantenimientos/{$this->id}";
    }
}
