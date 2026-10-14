<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Partes que se pidieron para poder continuar con un ticket. */
class TicketSolicitudParte extends Model
{
    protected $table = 'ticket_solicitudes_partes';

    protected $fillable = [
        'ticket_id', 'descripcion', 'fecha_estimada', 'costo_estimado', 'evidencia',
        'solicitado_por', 'recibido_at', 'recibido_por', 'notas_recepcion',
    ];

    protected $casts = [
        'fecha_estimada' => 'date',
        'costo_estimado' => 'decimal:2',
        'recibido_at'    => 'datetime',
    ];

    public function ticket(): BelongsTo      { return $this->belongsTo(Ticket::class); }
    public function solicitante(): BelongsTo { return $this->belongsTo(User::class, 'solicitado_por')->withTrashed(); }
    public function receptor(): BelongsTo    { return $this->belongsTo(User::class, 'recibido_por')->withTrashed(); }

    public function scopePendientes(Builder $query): Builder
    {
        return $query->whereNull('recibido_at');
    }

    public function getPendienteAttribute(): bool
    {
        return $this->recibido_at === null;
    }

    /** Días esperando: hasta la recepción o hasta hoy si sigue pendiente. */
    public function getDiasEsperaAttribute(): int
    {
        return (int) $this->created_at->copy()->startOfDay()
            ->diffInDays(($this->recibido_at ?? now())->copy()->startOfDay());
    }

    /** Pasó la fecha estimada y las partes no han llegado. */
    public function getAtrasadaAttribute(): bool
    {
        return $this->pendiente && $this->fecha_estimada && $this->fecha_estimada->lt(today());
    }

    public function getEvidenciaUrlAttribute(): ?string
    {
        return $this->evidencia ? url('archivos-privados/' . $this->evidencia) : null;
    }

    public function getEvidenciaEsImagenAttribute(): bool
    {
        return (bool) preg_match('/\.(jpe?g|png|webp|gif)$/i', (string) $this->evidencia);
    }
}
