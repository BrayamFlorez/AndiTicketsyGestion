<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
        'empresa_id',
        'sede_id',
        'activo',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'activo'            => 'boolean',
        ];
    }

    // ─────────────────────────────────────────────────────────────────────
    // Relaciones — donde TRABAJA el usuario (empleo)
    // ─────────────────────────────────────────────────────────────────────

    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    public function sede(): BelongsTo
    {
        return $this->belongsTo(Sede::class);
    }

    // ─────────────────────────────────────────────────────────────────────
    // Relaciones — qué CUBRE el técnico (asignaciones de soporte)
    // ─────────────────────────────────────────────────────────────────────

    /**
     * Empresas que el tecnico tiene asignadas (distinct).
     * Si tiene varias filas para una misma empresa (una por sede),
     * la empresa aparece UNA sola vez aqui gracias a distinct().
     */
    public function empresasAsignadas(): BelongsToMany
    {
        return $this->belongsToMany(Empresa::class, 'empresa_user')
            ->withTimestamps()
            ->withPivot('sede_id', 'asignado_at')
            ->distinct();
    }

    /**
     * Sedes especificamente asignadas (excluye las "toda la empresa").
     * Util cuando quieres saber "que sedes nominales tiene este tecnico".
     */
    public function sedesAsignadasDirectamente(): BelongsToMany
    {
        return $this->belongsToMany(Sede::class, 'empresa_user', 'user_id', 'sede_id')
            ->withTimestamps()
            ->withPivot('empresa_id', 'asignado_at')
            ->wherePivotNotNull('sede_id');
    }

    // ─────────────────────────────────────────────────────────────────────
    // Otras relaciones
    // ─────────────────────────────────────────────────────────────────────

    public function ticketsSolicitados(): HasMany
    {
        return $this->hasMany(Ticket::class, 'solicitante_id');
    }

    public function ticketsAsignados(): HasMany
    {
        return $this->hasMany(Ticket::class, 'asignado_a');
    }

    public function equiposRegistrados(): HasMany
    {
        return $this->hasMany(Equipo::class, 'creado_por');
    }

    // ─────────────────────────────────────────────────────────────────────
    // Helpers de acceso por cobertura
    // ─────────────────────────────────────────────────────────────────────

    /**
     * Sedes a las que el tecnico tiene acceso real.
     * Regla:
     *  - Si tiene fila (user, empresa, NULL) -> acceso a TODAS las sedes
     *    de esa empresa.
     *  - Si tiene fila (user, empresa, sede) -> acceso solo a esa sede.
     *
     * Devuelve un Builder para componer filtros adicionales.
     */
    public function sedesAccesibles(): Builder
    {
        return Sede::query()->whereExists(function ($sub) {
            $sub->from('empresa_user')
                ->where('empresa_user.user_id', $this->id)
                ->whereColumn('empresa_user.empresa_id', 'sedes.empresa_id')
                ->where(function ($w) {
                    $w->whereNull('empresa_user.sede_id')
                      ->orWhereColumn('empresa_user.sede_id', 'sedes.id');
                });
        });
    }

    /**
     * ¿Este técnico puede operar en esta empresa? (cubre al menos UNA sede de ella).
     */
    public function puedeAccederEmpresa(Empresa $empresa): bool
    {
        return DB::table('empresa_user')
            ->where('user_id', $this->id)
            ->where('empresa_id', $empresa->id)
            ->exists();
    }

    /**
     * ¿Este técnico puede operar en esta sede específicamente?
     * Verdadero si tiene asignación a toda la empresa O a esta sede.
     */
    public function puedeAccederSede(Sede $sede): bool
    {
        return DB::table('empresa_user')
            ->where('user_id', $this->id)
            ->where('empresa_id', $sede->empresa_id)
            ->where(function ($q) use ($sede) {
                $q->whereNull('sede_id')
                  ->orWhere('sede_id', $sede->id);
            })
            ->exists();
    }

    /**
     * ¿Tiene asignacion "global" a esta empresa (sede_id = NULL)?
     * Util para diferenciar acceso amplio vs sede puntual.
     */
    public function tieneAsignacionGlobalEmpresa(int $empresaId): bool
    {
        return DB::table('empresa_user')
            ->where('user_id', $this->id)
            ->where('empresa_id', $empresaId)
            ->whereNull('sede_id')
            ->exists();
    }

    // ─────────────────────────────────────────────────────────────────────
    // Helpers de rol
    // ─────────────────────────────────────────────────────────────────────

    public function esSuperAdmin(): bool   { return $this->hasRole('super-admin'); }
    public function esAdmin(): bool        { return $this->hasRole('admin'); }
    public function esTecnico(): bool      { return $this->hasRole('tecnico'); }
    public function esUsuarioFinal(): bool { return $this->hasRole('usuario'); }

    // ─────────────────────────────────────────────────────────────────────
    // Scopes
    // ─────────────────────────────────────────────────────────────────────

    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    public function scopeTecnicos($query)
    {
        return $query->role('tecnico');
    }

    public function scopeDeEmpresa($query, int $empresaId)
    {
        return $query->where('empresa_id', $empresaId);
    }
}
