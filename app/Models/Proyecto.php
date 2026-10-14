<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Proyecto extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'codigo',
        'nombre',
        'descripcion',
        'cliente_id',
        'responsable_id',
        'estado',
        'fecha_inicio',
        'fecha_entrega_estimada',
        'fecha_cierre',
        'creado_por',
    ];

    protected $casts = [
        'fecha_inicio'           => 'date',
        'fecha_entrega_estimada' => 'date',
        'fecha_cierre'           => 'date',
        'cliente_id'             => 'integer',
        'responsable_id'         => 'integer',
    ];

    public const ESTADO_ACTIVO     = 'activo';
    public const ESTADO_PAUSADO    = 'pausado';
    public const ESTADO_FINALIZADO = 'finalizado';
    public const ESTADO_CANCELADO  = 'cancelado';

    /** Etiqueta y clases de badge por estado */
    public const ESTADOS = [
        self::ESTADO_ACTIVO     => ['label' => 'Activo',     'clase' => 'bg-meta-3/10 text-meta-3 border-meta-3/20'],
        self::ESTADO_PAUSADO    => ['label' => 'Pausado',    'clase' => 'bg-meta-6/10 text-meta-6 border-meta-6/20'],
        self::ESTADO_FINALIZADO => ['label' => 'Finalizado', 'clase' => 'bg-primary/10 text-primary border-primary/20'],
        self::ESTADO_CANCELADO  => ['label' => 'Cancelado',  'clase' => 'bg-meta-1/10 text-meta-1 border-meta-1/20'],
    ];

    /** Estados que cierran el proyecto (llenan fecha_cierre) */
    public const ESTADOS_CERRADOS = [self::ESTADO_FINALIZADO, self::ESTADO_CANCELADO];

    // ── Relaciones ──────────────────────────────────────────────────────

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class)->withTrashed();
    }

    public function responsable(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responsable_id')->withTrashed();
    }

    public function creador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creado_por')->withTrashed();
    }

    /** Equipo completo (incluye al responsable) */
    public function equipo(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'proyecto_user')
            ->withPivot('rol')
            ->withTimestamps();
    }

    public function etapas(): HasMany
    {
        return $this->hasMany(ProyectoEtapa::class);
    }

    public function solicitudes(): HasMany
    {
        return $this->hasMany(ProyectoSolicitud::class);
    }

    public function solicitudesAbiertas(): HasMany
    {
        return $this->hasMany(ProyectoSolicitud::class)
            ->whereIn('estado', ProyectoSolicitud::ESTADOS_ABIERTOS);
    }

    public function adjuntos(): HasMany
    {
        return $this->hasMany(ProyectoAdjunto::class);
    }

    public function actividades(): HasMany
    {
        return $this->hasMany(ProyectoActividad::class)->latest('created_at');
    }

    // ── Scopes ──────────────────────────────────────────────────────────

    /**
     * Proyectos que el usuario puede ver:
     * - con 'proyectos.ver-todos': todos.
     * - resto: solo donde es miembro del equipo.
     */
    public function scopeVisiblesPara(Builder $query, User $user): Builder
    {
        if ($user->can('proyectos.ver-todos')) {
            return $query;
        }

        return $query->whereHas('equipo', fn (Builder $q) => $q->where('users.id', $user->id));
    }

    public function scopeBuscar(Builder $query, ?string $texto): Builder
    {
        if (! $texto) {
            return $query;
        }

        $term = '%' . trim($texto) . '%';

        return $query->where(fn (Builder $q) => $q
            ->where('codigo', 'like', $term)
            ->orWhere('nombre', 'like', $term)
            ->orWhereHas('cliente', fn (Builder $c) => $c->where('nombre', 'like', $term))
        );
    }

    // ── Helpers ─────────────────────────────────────────────────────────

    public function esMiembro(User $user): bool
    {
        if ($this->responsable_id === $user->id) {
            return true;
        }

        return $this->relationLoaded('equipo')
            ? $this->equipo->contains('id', $user->id)
            : $this->equipo()->where('users.id', $user->id)->exists();
    }

    public function getLabelEstadoAttribute(): string
    {
        return self::ESTADOS[$this->estado]['label'] ?? ucfirst($this->estado);
    }

    public function getClaseEstadoAttribute(): string
    {
        return self::ESTADOS[$this->estado]['clase'] ?? 'bg-gray-2 text-bodydark2 border-stroke';
    }

    public function estaCerrado(): bool
    {
        return in_array($this->estado, self::ESTADOS_CERRADOS, true);
    }

    /**
     * Resumen económico a partir de las etapas:
     * - Cotizado: la cotización más reciente con monto (la vigente).
     * - Pagado: suma de pagos en estado completada (los planeados son pagos programados).
     * Requiere `etapas` cargadas para no repetir consultas.
     */
    public function resumenEconomico(): array
    {
        $etapas = $this->relationLoaded('etapas') ? $this->etapas : $this->etapas()->get();

        $cotizacion = $etapas
            ->where('tipo', ProyectoEtapa::TIPO_COTIZACION)
            ->filter(fn ($e) => $e->monto !== null)
            ->sortByDesc(fn ($e) => $e->fecha->format('Y-m-d') . str_pad($e->id, 10, '0', STR_PAD_LEFT))
            ->first();

        $pagos = $etapas
            ->where('tipo', ProyectoEtapa::TIPO_PAGO)
            ->where('estado', ProyectoEtapa::ESTADO_COMPLETADA)
            ->sortBy('fecha')
            ->values();

        $cotizado = (float) ($cotizacion?->monto ?? 0);
        $pagado   = (float) $pagos->sum('monto');

        return [
            'cotizado'   => $cotizado,
            'pagado'     => $pagado,
            'saldo'      => max(0, $cotizado - $pagado),
            'porcentaje' => $cotizado > 0 ? (int) min(100, round($pagado / $cotizado * 100)) : 0,
            'pagos'      => $pagos,
            'cotizacion' => $cotizacion,
        ];
    }

    /**
     * Genera el próximo código por año: PRY-2026-000042.
     * Debe llamarse dentro de una transacción (usa bloqueo para evitar duplicados).
     */
    public static function generarCodigo(): string
    {
        $anio = now()->year;

        $ultimo = self::withTrashed()
            ->where('codigo', 'like', "PRY-{$anio}-%")
            ->lockForUpdate()
            ->orderByDesc('codigo')
            ->value('codigo');

        $numero = $ultimo ? ((int) substr($ultimo, -6)) + 1 : 1;

        return sprintf('PRY-%d-%06d', $anio, $numero);
    }
}
