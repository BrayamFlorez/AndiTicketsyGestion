<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Un paso de la línea de tiempo del ticket. Solo se crea, nunca se edita. */
class TicketEvento extends Model
{
    protected $table = 'ticket_eventos';

    public const UPDATED_AT = null;

    /** tipo => [etiqueta, icono, color] */
    public const TIPOS = [
        'creado'             => ['Ticket creado',        'fa-plus',          'primary'],
        'asignado'           => ['Técnico asignado',     'fa-user-check',    'primary'],
        'reasignado'         => ['Técnico reasignado',   'fa-exchange-alt',  'meta-6'],
        'iniciado'           => ['Trabajo iniciado',     'fa-play',          'primary'],
        'partes_solicitadas' => ['Partes solicitadas',   'fa-box-open',      'meta-6'],
        'partes_recibidas'   => ['Partes recibidas',     'fa-boxes',         'meta-3'],
        'resuelto'           => ['Resuelto',             'fa-check',         'meta-3'],
        'cerrado'            => ['Cerrado',              'fa-lock',          'bodydark2'],
        'cancelado'          => ['Cancelado',            'fa-ban',           'meta-1'],
    ];

    protected $fillable = [
        'ticket_id', 'tipo', 'usuario_id', 'estado_anterior', 'estado_nuevo',
        'tecnico_anterior_id', 'tecnico_nuevo_id', 'comentario', 'datos',
    ];

    protected $casts = [
        'datos'      => 'array',
        'created_at' => 'datetime',
    ];

    public function ticket(): BelongsTo         { return $this->belongsTo(Ticket::class); }
    public function usuario(): BelongsTo        { return $this->belongsTo(User::class, 'usuario_id')->withTrashed(); }
    public function tecnicoAnterior(): BelongsTo { return $this->belongsTo(User::class, 'tecnico_anterior_id')->withTrashed(); }
    public function tecnicoNuevo(): BelongsTo    { return $this->belongsTo(User::class, 'tecnico_nuevo_id')->withTrashed(); }

    public function getEtiquetaAttribute(): string { return self::TIPOS[$this->tipo][0] ?? ucfirst($this->tipo); }
    public function getIconoAttribute(): string    { return self::TIPOS[$this->tipo][1] ?? 'fa-circle'; }
    public function getColorAttribute(): string    { return self::TIPOS[$this->tipo][2] ?? 'bodydark2'; }
}
