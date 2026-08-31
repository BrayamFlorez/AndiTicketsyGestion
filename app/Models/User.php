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
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\WorkflowStepAssignment;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;
    use SoftDeletes;

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

    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    public function sede(): BelongsTo
    {
        return $this->belongsTo(Sede::class);
    }

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

    public function puedeAccederEmpresa(Empresa $empresa): bool
    {
        return DB::table('empresa_user')
            ->where('user_id', $this->id)
            ->where('empresa_id', $empresa->id)
            ->exists();
    }

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

    public function tieneAsignacionGlobalEmpresa(int $empresaId): bool
    {
        return DB::table('empresa_user')
            ->where('user_id', $this->id)
            ->where('empresa_id', $empresaId)
            ->whereNull('sede_id')
            ->exists();
    }

    public function esSuperAdmin(): bool
    {
        return $this->hasRole('super-admin');
    }
    public function esAdmin(): bool
    {
        return $this->hasRole('admin');
    }
    public function esTecnico(): bool
    {
        return $this->hasRole('tecnico');
    }
    public function esUsuarioFinal(): bool
    {
        return $this->hasRole('usuario');
    }

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

    protected static function booted(): void
    {
        static::forceDeleting(function (User $usuario) {
            $vinculos = [];

            // 1. Archivos adjuntos en flujos
            if (WorkflowAttachment::where('subido_por', $usuario->id)->exists()) {
                $vinculos[] = 'archivos adjuntos subidos';
            }

            // 2. Tickets
            $tieneTickets = Ticket::where('user_id', $usuario->id)
                // Si en tickets usas otras columnas para el técnico o creador, descoméntalas:
                // ->orWhere('tecnico_id', $usuario->id)
                // ->orWhere('creado_por', $usuario->id)
                ->exists();

            if ($tieneTickets) {
                $vinculos[] = 'tickets creados o asignados';
            }

            // 3. Actores y asignaciones de Flujos (Workflows)
            $tieneFlujos = WorkflowStepAssignment::where('user_id', $usuario->id)->exists()
                || WorkflowStageActor::where('user_id', $usuario->id)->exists();

            if ($tieneFlujos) {
                $vinculos[] = 'pasos o roles asignados en flujos de trabajo';
            }

            // 4. Mantenimientos preventivos
            if (MantenimientoPreventivo::where('user_id', $usuario->id)->exists()) {
                $vinculos[] = 'mantenimientos preventivos asociados';
            }

            // 5. Equipos a cargo
            if (Equipo::where('user_id', $usuario->id)->exists()) {
                $vinculos[] = 'equipos bajo su responsabilidad';
            }

            // Si se detectó alguna relación, frena el borrado y devuelve el motivo
            if (!empty($vinculos)) {
                $lista = implode(', ', $vinculos);
                throw new \Exception("No se puede eliminar a {$usuario->name} porque tiene registros en: {$lista}.");
            }
        });
    }

    public function workflowAssignmentsPendientes()
    {
        return $this->hasMany(WorkflowStepAssignment::class, 'user_id')
            ->where('estado', WorkflowStepAssignment::ESTADO_PENDIENTE);
    }
}
