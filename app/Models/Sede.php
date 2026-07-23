<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sede extends Model
{
    protected $fillable = [
        'empresa_id',
        'nombre',
        'ciudad',
        'direccion',
        'telefono',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    // Relaciones

    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    /**
     * Usuarios que trabajan en esta sede (su sede principal).
     */
    public function usuarios(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Tecnicos asignados especificamente a esta sede.
     * NO incluye tecnicos con asignacion global a la empresa.
     * Para "todos los tecnicos que pueden operar aqui", usar tecnicosConAcceso().
     */
    public function tecnicosAsignados(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'empresa_user', 'sede_id', 'user_id')
            ->withTimestamps()
            ->withPivot('empresa_id', 'asignado_at');
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    public function equipos(): HasMany
    {
        return $this->hasMany(Equipo::class);
    }

    // Scopes

    public function scopeActivas($query)
    {
        return $query->where('activo', true);
    }

    public function scopeDeEmpresa($query, int $empresaId)
    {
        return $query->where('empresa_id', $empresaId);
    }
}
