<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

/**
 * Agente de Windows instalado en un equipo.
 * El token en claro solo existe en el PC; aquí se guarda su hash.
 */
class EquipoAgente extends Model
{
    protected $table = 'equipo_agentes';

    protected $fillable = [
        'equipo_id', 'token_hash', 'hostname', 'usuario_sesion', 'ip', 'mac',
        'version_agente', 'ultimo_inventario', 'ultimo_reporte_at', 'vinculado_at',
        'revocado_at', 'revocado_por',
    ];

    protected $hidden = ['token_hash'];

    protected function casts(): array
    {
        return [
            'equipo_id'         => 'integer',
            'revocado_por'      => 'integer',
            'ultimo_inventario' => 'array',
            'ultimo_reporte_at' => 'datetime',
            'vinculado_at'      => 'datetime',
            'revocado_at'       => 'datetime',
        ];
    }

    // ── Relaciones ──────────────────────────────────────────────────────

    public function equipo(): BelongsTo
    {
        return $this->belongsTo(Equipo::class);
    }

    public function revocador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'revocado_por');
    }

    // ── Scopes ──────────────────────────────────────────────────────────

    public function scopeActivos(Builder $query): Builder
    {
        return $query->whereNull('revocado_at');
    }

    // ── Token ───────────────────────────────────────────────────────────

    /** Genera un token nuevo. Devuelve [token en claro, hash para guardar] */
    public static function generarToken(): array
    {
        $token = 'agt_' . Str::random(48);

        return [$token, self::hashToken($token)];
    }

    public static function hashToken(string $token): string
    {
        return hash('sha256', $token);
    }

    public static function buscarPorToken(?string $token): ?self
    {
        if (! $token || ! str_starts_with($token, 'agt_')) {
            return null;
        }

        return self::activos()->where('token_hash', self::hashToken($token))->first();
    }

    // ── Estado ──────────────────────────────────────────────────────────

    public function estaActivo(): bool
    {
        return $this->revocado_at === null;
    }

    /** activo | sin_conexion | pendiente | revocado */
    public function getEstadoConexionAttribute(): string
    {
        if (! $this->estaActivo()) {
            return 'revocado';
        }

        if (! $this->ultimo_reporte_at) {
            return 'pendiente';
        }

        return $this->ultimo_reporte_at->lt(now()->subMinutes(config('agente.minutos_sin_conexion')))
            ? 'sin_conexion'
            : 'activo';
    }
}
