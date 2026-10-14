<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProyectoEtapa extends Model
{
    use SoftDeletes;

    protected $table = 'proyecto_etapas';

    protected $fillable = [
        'proyecto_id',
        'tipo',
        'titulo',
        'descripcion',
        'fecha',
        'estado',
        'monto',
        'fecha_validez',
        'medio_pago',
        'version',
        'ronda',
        'avance',
        'enlace',
        'registrado_por',
    ];

    protected $casts = [
        'fecha'         => 'date',
        'fecha_validez' => 'date',
        'monto'         => 'decimal:2',
        'ronda'         => 'integer',
        'avance'        => 'integer',
        'proyecto_id'   => 'integer',
        'registrado_por' => 'integer',
    ];

    public const TIPO_COTIZACION = 'cotizacion';
    public const TIPO_PAGO       = 'pago';
    public const TIPO_ENTREGA    = 'entrega';
    public const TIPO_CORRECCION = 'correccion';

    /** Catálogo de tipos: etiqueta, icono Font Awesome y color (hex) */
    public const TIPOS = [
        'levantamiento' => ['label' => 'Requisitos',   'icono' => 'fas fa-clipboard-list',      'color' => '#3ea6f0'],
        'cotizacion'    => ['label' => 'Cotización',   'icono' => 'fas fa-file-invoice-dollar', 'color' => '#a855f7'],
        'aprobacion'    => ['label' => 'Aprobación',   'icono' => 'fas fa-file-signature',      'color' => '#10b981'],
        'pago'          => ['label' => 'Pago',         'icono' => 'fas fa-money-bill-wave',     'color' => '#16a34a'],
        'diseno'        => ['label' => 'Diseño',       'icono' => 'fas fa-pen-ruler',           'color' => '#ec4899'],
        'desarrollo'    => ['label' => 'Desarrollo',   'icono' => 'fas fa-code',                'color' => '#007acc'],
        'entrega'       => ['label' => 'Entrega',      'icono' => 'fas fa-box-open',            'color' => '#06b6d4'],
        'correccion'    => ['label' => 'Correcciones', 'icono' => 'fas fa-screwdriver-wrench',  'color' => '#f59e0b'],
        'pruebas'       => ['label' => 'Pruebas',      'icono' => 'fas fa-vial-circle-check',   'color' => '#8b5cf6'],
        'capacitacion'  => ['label' => 'Capacitación', 'icono' => 'fas fa-chalkboard-user',     'color' => '#14b8a6'],
        'finalizacion'  => ['label' => 'Finalización', 'icono' => 'fas fa-flag-checkered',      'color' => '#22c55e'],
        'garantia'      => ['label' => 'Garantía',     'icono' => 'fas fa-shield-halved',       'color' => '#64748b'],
    ];

    /** Campos opcionales que aplica cada tipo. Los demás se guardan en null. */
    public const CAMPOS_POR_TIPO = [
        'cotizacion' => ['monto', 'fecha_validez'],
        'pago'       => ['monto', 'medio_pago'],
        'entrega'    => ['version', 'enlace'],
        'correccion' => ['ronda'],
        'desarrollo' => ['avance', 'enlace'],
        'diseno'     => ['enlace'],
        'pruebas'    => ['enlace'],
    ];

    public const CAMPOS_OPCIONALES = ['monto', 'fecha_validez', 'medio_pago', 'version', 'ronda', 'avance', 'enlace'];

    /** Tipos que pueden marcar solicitudes del cliente como atendidas */
    public const TIPOS_QUE_ATIENDEN = ['correccion', 'entrega', 'desarrollo', 'pruebas'];

    public const ESTADO_PLANEADA   = 'planeada';
    public const ESTADO_EN_CURSO   = 'en_curso';
    public const ESTADO_COMPLETADA = 'completada';

    public const ESTADOS = [
        self::ESTADO_PLANEADA   => 'Planeada',
        self::ESTADO_EN_CURSO   => 'En curso',
        self::ESTADO_COMPLETADA => 'Completada',
    ];

    public const MEDIOS_PAGO = [
        'transferencia' => 'Transferencia',
        'efectivo'      => 'Efectivo',
        'consignacion'  => 'Consignación',
        'tarjeta'       => 'Tarjeta',
        'otro'          => 'Otro',
    ];

    // ── Relaciones ──────────────────────────────────────────────────────

    public function proyecto(): BelongsTo
    {
        return $this->belongsTo(Proyecto::class);
    }

    public function autor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'registrado_por')->withTrashed();
    }

    public function adjuntos(): MorphMany
    {
        return $this->morphMany(ProyectoAdjunto::class, 'adjuntable')->orderBy('id');
    }

    public function solicitudesAtendidas(): HasMany
    {
        return $this->hasMany(ProyectoSolicitud::class, 'etapa_resolucion_id');
    }

    // ── Helpers ─────────────────────────────────────────────────────────

    public function getConfigTipoAttribute(): array
    {
        return self::TIPOS[$this->tipo] ?? ['label' => ucfirst($this->tipo), 'icono' => 'fas fa-circle', 'color' => '#007acc'];
    }

    public function getLabelEstadoAttribute(): string
    {
        return self::ESTADOS[$this->estado] ?? ucfirst($this->estado);
    }

    public function getLabelMedioPagoAttribute(): ?string
    {
        return $this->medio_pago ? (self::MEDIOS_PAGO[$this->medio_pago] ?? $this->medio_pago) : null;
    }

    public function esEconomica(): bool
    {
        return in_array($this->tipo, [self::TIPO_COTIZACION, self::TIPO_PAGO], true);
    }

    /** Título automático cuando el usuario no escribe uno */
    public static function tituloPorDefecto(string $tipo, array $datos = []): string
    {
        return match ($tipo) {
            self::TIPO_ENTREGA    => trim('Entrega ' . ($datos['version'] ?? '')),
            self::TIPO_CORRECCION => trim('Correcciones · Ronda ' . ($datos['ronda'] ?? '')),
            default               => self::TIPOS[$tipo]['label'] ?? 'Etapa',
        };
    }
}
