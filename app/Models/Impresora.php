<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\ImpresoraConsumibleRecarga;
use App\Models\ImpresoraContador;


class Impresora extends Model
{
    protected $fillable = [
        'codigo',
        'codigo_interno',
        'modelo_id',
        'empresa_id',
        'sede_id',
        'nombre_asignado',
        'ubicacion_fisica',
        'ip_red',
        'hostname',
        'mac_address',
        'panel_web_usuario',
        'panel_web_password',
        'numero_serie',
        'fecha_adquisicion',
        'contador_paginas_actual',
        'contador_actualizado_en',
        'estado',
        'observaciones',
        'intervalo_mantenimiento_dias',
        'proximo_mantenimiento_sugerido',
        'creado_por',
    ];

    protected $casts = [
        'fecha_adquisicion'        => 'date',
        'contador_actualizado_en'  => 'datetime',
        'contador_paginas_actual'  => 'integer',
        'intervalo_mantenimiento_dias'    => 'integer',
        'proximo_mantenimiento_sugerido'  => 'date',
    ];

    public const ESTADO_ACTIVA           = 'activa';
    public const ESTADO_EN_MANTENIMIENTO = 'en_mantenimiento';
    public const ESTADO_DADA_DE_BAJA     = 'dada_de_baja';

    public const ESTADOS = [
        self::ESTADO_ACTIVA           => 'Activa',
        self::ESTADO_EN_MANTENIMIENTO => 'En mantenimiento',
        self::ESTADO_DADA_DE_BAJA     => 'Dada de baja',
    ];

    // ── Relaciones ──────────────────────────────────────────────────────

    public function modelo(): BelongsTo
    {
        return $this->belongsTo(ImpresoraModelo::class, 'modelo_id');
    }

    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    public function sede(): BelongsTo
    {
        return $this->belongsTo(Sede::class);
    }

    public function creador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creado_por')->withTrashed();
    }

    public function recargasConsumibles(): HasMany
    {
        return $this->hasMany(ImpresoraConsumibleRecarga::class);
    }

    public function contadores(): HasMany
    {
        return $this->hasMany(ImpresoraContador::class);
    }

    // ── Cifrado de credenciales del panel web ───────────────────────────

    protected function panelWebPassword(): Attribute
    {
        return Attribute::make(
            get: fn($valor) => $valor ? Crypt::decryptString($valor) : null,
            set: fn($valor) => $valor ? Crypt::encryptString($valor) : null,
        );
    }

    // ── Helpers ─────────────────────────────────────────────────────────

    public function getLabelEstadoAttribute(): string
    {
        return self::ESTADOS[$this->estado] ?? ucfirst($this->estado);
    }

    public function getNombreVisibleAttribute(): string
    {
        return $this->nombre_asignado
            ?? $this->codigo_interno
            ?? $this->modelo?->nombre_completo
            ?? "Impresora {$this->codigo}";
    }

    public function estaActiva(): bool
    {
        return $this->estado === self::ESTADO_ACTIVA;
    }

    /**
     * Genera el próximo código autoincremental por año: IMP-2026-000042
     */
    public static function generarCodigo(): string
    {
        $anio = now()->year;

        $ultimo = self::where('codigo', 'like', "IMP-{$anio}-%")
            ->orderByDesc('id')
            ->value('codigo');

        $numero = $ultimo
            ? ((int) substr($ultimo, -6)) + 1
            : 1;

        return sprintf('IMP-%d-%06d', $anio, $numero);
    }

    public function consumibles(): HasMany
    {
        return $this->hasMany(ImpresoraConsumible::class);
    }

    public function consumiblesActivos(): HasMany
    {
        return $this->hasMany(ImpresoraConsumible::class)->where('activo', true);
    }

    /**
     * ¿Tiene algún consumible en estado crítico o agotado?
     */
    public function tieneConsumiblesEnAlerta(): bool
    {
        return $this->consumiblesActivos
            ->contains(fn($c) => in_array($c->estado_nivel, ['critico', 'agotado']));
    }

        public function mantenimientos(): HasMany
    {
        return $this->hasMany(ImpresoraMantenimiento::class)->orderByDesc('fecha_realizado');
    }

    public function ultimoMantenimiento()
    {
        return $this->hasOne(ImpresoraMantenimiento::class)->latestOfMany('fecha_realizado');
    }

    public function alertasActivas(): HasMany
    {
        return $this->hasMany(ImpresoraAlertaEnviada::class)->whereNull('resuelta_en');
    }

        /**
     * ¿El próximo mantenimiento está próximo a vencer (≤7 días)?
     */
    public function mantenimientoProximo(): bool
    {
        if (! $this->proximo_mantenimiento_sugerido) {
            return false;
        }
        $dias = now()->startOfDay()->diffInDays($this->proximo_mantenimiento_sugerido->startOfDay(), false);
        return $dias >= 0 && $dias <= 7;
    }

    /**
     * ¿El próximo mantenimiento ya está vencido?
     */
    public function mantenimientoVencido(): bool
    {
        if (! $this->proximo_mantenimiento_sugerido) {
            return false;
        }
        return $this->proximo_mantenimiento_sugerido->startOfDay()->isPast();
    }

    /**
     * Recalcula proximo_mantenimiento_sugerido con base en el último mantenimiento + intervalo.
     */
    public function recalcularProximoMantenimiento(): void
    {
        if (! $this->intervalo_mantenimiento_dias) {
            $this->update(['proximo_mantenimiento_sugerido' => null]);
            return;
        }

        $ultimo = $this->ultimoMantenimiento()->first();
        if (! $ultimo) {
            $this->update(['proximo_mantenimiento_sugerido' => null]);
            return;
        }

        $this->update([
            'proximo_mantenimiento_sugerido' => $ultimo->fecha_realizado
                ->copy()
                ->addDays($this->intervalo_mantenimiento_dias),
        ]);
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }
}
