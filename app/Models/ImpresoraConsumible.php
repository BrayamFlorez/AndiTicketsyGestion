<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Services\Impresoras\NivelConsumible;

class ImpresoraConsumible extends Model
{
    protected $table = 'impresora_consumibles';

    protected $fillable = [
        'impresora_id', 'tipo', 'color', 'etiqueta',
        'dias_estimados', 'fecha_instalacion', 'fecha_reemplazo',
        'duracion_real_dias', 'activo', 'consumible_anterior_id',
        'observaciones', 'registrado_por', 
        'nivel_referencia', 'fecha_referencia_nivel',


    ];

    protected $casts = [
        'fecha_instalacion'  => 'date',
        'fecha_reemplazo'    => 'date',
        'dias_estimados'     => 'integer',
        'duracion_real_dias' => 'integer',
        'activo'             => 'boolean',
        'nivel_referencia'       => 'float',
        'fecha_referencia_nivel' => 'datetime',
    ];

    public const TIPOS = [
        'toner'             => 'Tóner',
        'cartucho'          => 'Cartucho de tinta',
        'tanque_tinta'      => 'Tanque de tinta',
        'tambor'            => 'Tambor',
        'fusor'             => 'Fusor',
        'cabezal'           => 'Cabezal',
        'kit_mantenimiento' => 'Kit de mantenimiento',
        'otro'              => 'Otro',
    ];

    public const COLORES = [
        'negro'      => 'Negro',
        'cyan'       => 'Cyan',
        'magenta'    => 'Magenta',
        'amarillo'   => 'Amarillo',
        'unico'      => 'Único',
        'no_aplica'  => 'No aplica',
    ];

    public const COLORES_HEX = [
        'negro'     => '#000000',
        'cyan'      => '#00bcd4',
        'magenta'   => '#e91e63',
        'amarillo'  => '#fbc02d',
        'unico'     => '#607d8b',
        'no_aplica' => '#9e9e9e',
    ];

    // ── Relaciones ──────────────────────────────────────────────────────

    public function impresora(): BelongsTo
    {
        return $this->belongsTo(Impresora::class);
    }

    public function consumibleAnterior(): BelongsTo
    {
        return $this->belongsTo(self::class, 'consumible_anterior_id');
    }

    public function registrador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'registrado_por')->withTrashed();
    }

    public function recargas(): HasMany
    {
        return $this->hasMany(ImpresoraConsumibleRecarga::class, 'consumible_id');
    }

    public function ultimaRecarga(): HasOne
    {
        return $this->hasOne(ImpresoraConsumibleRecarga::class, 'consumible_id')->latestOfMany();
    }

    // ── Cálculo del nivel estimado ──────────────────────────────────────

    /**
     * Fecha estimada de agotamiento: fecha_instalacion + dias_estimados.
     */
    public function getFechaAgotamientoEstimadaAttribute(): ?Carbon
    {
        if (! $this->fecha_instalacion || ! $this->dias_estimados) {
            return null;
        }
        return $this->fecha_instalacion->copy()->addDays($this->dias_estimados);
    }

    

    /**
     * Nivel estimado restante (0-100).
     * Lineal: 100% al instalar, 0% al llegar a fecha_agotamiento_estimada.
     * Devuelve null si no hay fecha de instalación.
     */
    public function getNivelEstimadoAttribute(): ?int
    {
        $nivel = app(NivelConsumible::class)->nivelEstimado($this);

        return $nivel === null ? null : (int) round($nivel);
    }

    /**
     * Estado visual según el nivel.
     */
    public function getEstadoNivelAttribute(): string
    {
        $nivel = $this->nivel_estimado;
        if ($nivel === null) return 'sin_datos';
        if ($nivel <= 5)  return 'agotado';
        if ($nivel <= 20) return 'critico';
        if ($nivel <= 40) return 'bajo';
        return 'normal';
    }

    /**
     * Días que faltan para agotarse según la estimación.
     */
    public function getDiasRestantesAttribute(): ?int
    {
        return app(NivelConsumible::class)->diasRestantes($this);
    }

    // ── Labels ──────────────────────────────────────────────────────────

    public function getLabelTipoAttribute(): string
    {
        return self::TIPOS[$this->tipo] ?? ucfirst($this->tipo);
    }

    public function getLabelColorAttribute(): string
    {
        return self::COLORES[$this->color] ?? ucfirst($this->color);
    }

    public function getColorHexAttribute(): string
    {
        return self::COLORES_HEX[$this->color] ?? '#9e9e9e';
    }

    public function getDescripcionCortaAttribute(): string
    {
        if ($this->etiqueta) return $this->etiqueta;

        $partes = [$this->label_tipo];
        if ($this->color !== 'no_aplica') {
            $partes[] = $this->label_color;
        }
        return implode(' — ', $partes);
    }
}