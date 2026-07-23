<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Empresa extends Model
{
    protected $fillable = [
        'nombre',
        'nit',
        'telefono',
        'email',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    // Relaciones

    public function sedes(): HasMany
    {
        return $this->hasMany(Sede::class);
    }

    /**
     * Usuarios cuya empresa "principal" es esta (sobre todo usuarios finales).
     */
    public function usuarios(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Tecnicos asignados a cubrir esta empresa.
     */
    public function tecnicosAsignados(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'empresa_user')
            ->withTimestamps()
            ->withPivot('asignado_at');
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
}
