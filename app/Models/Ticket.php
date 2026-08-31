<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ticket extends Model
{
    const CATEGORIAS = [
        'hardware'  => 'Hardware',
        'software'  => 'Software',
        'red'       => 'Red / Conectividad',
        'impresora' => 'Impresora / Escáner',
        'telefonia' => 'Telefonía',
        'acceso'    => 'Accesos / Permisos',
        'otro'      => 'Otro',
    ];

    const ICONOS_CATEGORIA = [
        'hardware'  => 'fa-desktop',
        'software'  => 'fa-code',
        'red'       => 'fa-network-wired',
        'impresora' => 'fa-print',
        'telefonia' => 'fa-phone',
        'acceso'    => 'fa-key',
        'otro'      => 'fa-ticket-alt',
    ];

    const PRIORIDADES = [
        'baja'    => 'Baja',
        'media'   => 'Media',
        'alta'    => 'Alta',
        'critica' => 'Crítica',
    ];

    const ESTADOS = [
        'abierto'     => 'Abierto',
        'asignado'    => 'Asignado',
        'en_progreso' => 'En progreso',
        'resuelto'    => 'Resuelto',
        'cerrado'     => 'Cerrado',
        'cancelado'   => 'Cancelado',
    ];

    const COLORES_ESTADO = [
        'abierto'     => 'secondary',
        'asignado'    => 'primary',
        'en_progreso' => 'warning',
        'resuelto'    => 'success',
        'cerrado'     => 'dark',
        'cancelado'   => 'danger',
    ];

    const COLORES_PRIORIDAD = [
        'baja'    => 'success',
        'media'   => 'info',
        'alta'    => 'warning',
        'critica' => 'danger',
    ];

    protected $fillable = [
        'solicitante_id', 'asignado_a', 'empresa_id', 'sede_id',
        'titulo', 'categoria', 'prioridad', 'estado', 'descripcion',
        'foto_solicitud', 'evidencia_resolucion', 'notas_resolucion',
        'asignado_at', 'iniciado_at', 'resuelto_at', 'cerrado_at',
    ];

    protected $casts = [
        'asignado_at' => 'datetime',
        'iniciado_at' => 'datetime',
        'resuelto_at' => 'datetime',
        'cerrado_at'  => 'datetime',
    ];

    // Relaciones

    public function solicitante()
    {
        // Cambiar 'user_id' por 'solicitante_id'
        return $this->belongsTo(User::class, 'solicitante_id')->withTrashed(); 
    }

    public function asignado()
    {
        return $this->belongsTo(User::class, 'asignado_a')->withTrashed();
    }
    public function empresa(): BelongsTo      { return $this->belongsTo(Empresa::class); }
    public function sede(): BelongsTo         { return $this->belongsTo(Sede::class); }

    // Scopes

    public function scopeDelSolicitante($query, int $userId)
    {
        return $query->where('solicitante_id', $userId);
    }

    public function scopeAsignadosA($query, int $userId)
    {
        return $query->where('asignado_a', $userId);
    }

    public function scopeAbiertos($query)
    {
        return $query->where('estado', 'abierto');
    }

    public function scopePendientesDeAsignacion($query)
    {
        return $query->where('estado', 'abierto')->whereNull('asignado_a');
    }

    public function scopeDeEmpresa($query, int $empresaId)
    {
        return $query->where('empresa_id', $empresaId);
    }

    // ─────────────────────────────────────────────────────────────────────
    // Tecnicos con cobertura para ESTE ticket
    // ─────────────────────────────────────────────────────────────────────

    /**
     * Devuelve los tecnicos activos que tienen cobertura para la
     * empresa+sede de este ticket en la tabla empresa_user.
     *
     * Reglas:
     *  - Si el ticket no tiene empresa, devuelve todos los tecnicos activos
     *    (no hay forma de filtrar; el admin decide).
     *  - Si tiene empresa pero NO sede: tecnicos con cualquier cobertura
     *    (global o sede especifica) de esa empresa.
     *  - Si tiene empresa Y sede: tecnicos con cobertura global de la empresa
     *    O cobertura especifica de esa sede.
     */
    public function tecnicosDisponibles(): Collection
    {
        $query = User::activos()->role(\App\Support\Roles::CON_COBERTURA);

        if ($this->empresa_id) {
            $query->whereExists(function ($sub) {
                $sub->from('empresa_user')
                    ->whereColumn('empresa_user.user_id', 'users.id')
                    ->where('empresa_user.empresa_id', $this->empresa_id)
                    ->when($this->sede_id, function ($q) {
                        $sedeId = $this->sede_id;
                        $q->where(function ($w) use ($sedeId) {
                            $w->whereNull('empresa_user.sede_id')
                              ->orWhere('empresa_user.sede_id', $sedeId);
                        });
                    });
            });
        }

        return $query->orderBy('name')->get(['id', 'name']);
    }

    // ─────────────────────────────────────────────────────────────────────
    // Accessors y helpers
    // ─────────────────────────────────────────────────────────────────────

    public function getColorEstadoAttribute(): string
    {
        return self::COLORES_ESTADO[$this->estado] ?? 'secondary';
    }

    public function getColorPrioridadAttribute(): string
    {
        return self::COLORES_PRIORIDAD[$this->prioridad] ?? 'secondary';
    }

    public function getLabelEstadoAttribute(): string
    {
        return self::ESTADOS[$this->estado] ?? ucfirst($this->estado);
    }

    public function getLabelPrioridadAttribute(): string
    {
        return self::PRIORIDADES[$this->prioridad] ?? ucfirst($this->prioridad);
    }

    public function getLabelCategoriaAttribute(): string
    {
        return self::CATEGORIAS[$this->categoria] ?? ucfirst($this->categoria);
    }

    public function getFotoSolicitudUrlAttribute(): ?string
    {
        return $this->foto_solicitud
            ? url('archivos-privados/' . $this->foto_solicitud)
            : null;
    }

    public function getEvidenciaResolucionUrlAttribute(): ?string
    {
        return $this->evidencia_resolucion
            ? url('archivos-privados/' . $this->evidencia_resolucion)
            : null;
    }

    public function getIconoCategoriaAttribute(): string
    {
        return self::ICONOS_CATEGORIA[$this->categoria] ?? 'fa-ticket-alt';
    }

    // Estados del ciclo de vida

    public function puedeIniciar(): bool   { return $this->estado === 'asignado'; }
    public function puedeResolver(): bool  { return $this->estado === 'en_progreso'; }
    public function puedeCerrar(): bool    { return in_array($this->estado, ['resuelto', 'en_progreso']); }
    public function puedeCancelar(): bool  { return in_array($this->estado, ['abierto', 'asignado', 'en_progreso']); }
    public function estaActivo(): bool     { return ! in_array($this->estado, ['cerrado', 'cancelado']); }
}
