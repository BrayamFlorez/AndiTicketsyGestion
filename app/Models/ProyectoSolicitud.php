<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProyectoSolicitud extends Model
{
    use SoftDeletes;

    protected $table = 'proyecto_solicitudes';

    protected $fillable = [
        'proyecto_id',
        'titulo',
        'descripcion',
        'fecha_solicitud',
        'canal',
        'prioridad',
        'estado',
        'respuesta',
        'etapa_resolucion_id',
        'registrado_por',
    ];

    protected $casts = [
        'fecha_solicitud'     => 'date',
        'proyecto_id'         => 'integer',
        'registrado_por'      => 'integer',
        'etapa_resolucion_id' => 'integer',
    ];

    public const ESTADO_PENDIENTE     = 'pendiente';
    public const ESTADO_EN_PROCESO    = 'en_proceso';
    public const ESTADO_ATENDIDA      = 'atendida';
    public const ESTADO_RECHAZADA     = 'rechazada';
    public const ESTADO_FUERA_ALCANCE = 'fuera_alcance';

    /** Etiqueta y clases de badge por estado */
    public const ESTADOS = [
        self::ESTADO_PENDIENTE     => ['label' => 'Pendiente',        'clase' => 'bg-meta-6/10 text-meta-6 border-meta-6/30'],
        self::ESTADO_EN_PROCESO    => ['label' => 'En proceso',       'clase' => 'bg-primary/10 text-primary border-primary/30'],
        self::ESTADO_ATENDIDA      => ['label' => 'Atendida',         'clase' => 'bg-meta-3/10 text-meta-3 border-meta-3/30'],
        self::ESTADO_RECHAZADA     => ['label' => 'Rechazada',        'clase' => 'bg-meta-1/10 text-meta-1 border-meta-1/30'],
        self::ESTADO_FUERA_ALCANCE => ['label' => 'Fuera de alcance', 'clase' => 'bg-[#a855f7]/10 text-[#a855f7] border-[#a855f7]/30'],
    ];

    /** Estados que cuentan como "abiertos" */
    public const ESTADOS_ABIERTOS = [self::ESTADO_PENDIENTE, self::ESTADO_EN_PROCESO];

    public const CANALES = [
        'whatsapp'   => ['label' => 'WhatsApp',   'icono' => 'fab fa-whatsapp'],
        'correo'     => ['label' => 'Correo',     'icono' => 'fas fa-envelope'],
        'llamada'    => ['label' => 'Llamada',    'icono' => 'fas fa-phone'],
        'reunion'    => ['label' => 'Reunión',    'icono' => 'fas fa-users'],
        'presencial' => ['label' => 'Presencial', 'icono' => 'fas fa-handshake'],
    ];

    public const PRIORIDADES = [
        'baja'  => ['label' => 'Baja',  'clase' => 'text-bodydark2'],
        'media' => ['label' => 'Media', 'clase' => 'text-meta-6'],
        'alta'  => ['label' => 'Alta',  'clase' => 'text-meta-1'],
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

    public function etapaResolucion(): BelongsTo
    {
        return $this->belongsTo(ProyectoEtapa::class, 'etapa_resolucion_id')->withTrashed();
    }

    public function adjuntos(): MorphMany
    {
        return $this->morphMany(ProyectoAdjunto::class, 'adjuntable')->orderBy('id');
    }

    // ── Helpers ─────────────────────────────────────────────────────────

    public function estaAbierta(): bool
    {
        return in_array($this->estado, self::ESTADOS_ABIERTOS, true);
    }

    public function getLabelEstadoAttribute(): string
    {
        return self::ESTADOS[$this->estado]['label'] ?? ucfirst($this->estado);
    }

    public function getClaseEstadoAttribute(): string
    {
        return self::ESTADOS[$this->estado]['clase'] ?? '';
    }

    public function getCanalConfigAttribute(): array
    {
        return self::CANALES[$this->canal] ?? ['label' => ucfirst($this->canal), 'icono' => 'fas fa-comment'];
    }

    public function getPrioridadConfigAttribute(): array
    {
        return self::PRIORIDADES[$this->prioridad] ?? ['label' => ucfirst($this->prioridad), 'clase' => ''];
    }
}
