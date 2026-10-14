<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cliente extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'nombre',
        'tipo_documento',
        'numero_documento',
        'contacto_nombre',
        'contacto_cargo',
        'telefono',
        'email',
        'direccion',
        'ciudad',
        'observaciones',
        'activo',
        'creado_por',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    public const TIPOS_DOCUMENTO = [
        'nit'  => 'NIT',
        'cc'   => 'Cédula',
        'ce'   => 'Cédula extranjería',
        'otro' => 'Otro',
    ];

    // ── Relaciones ──────────────────────────────────────────────────────

    public function proyectos(): HasMany
    {
        return $this->hasMany(Proyecto::class);
    }

    public function creador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creado_por')->withTrashed();
    }

    // ── Scopes ──────────────────────────────────────────────────────────

    public function scopeActivos(Builder $query): Builder
    {
        return $query->where('activo', true);
    }

    public function scopeBuscar(Builder $query, ?string $texto): Builder
    {
        if (! $texto) {
            return $query;
        }

        $term = '%' . trim($texto) . '%';

        return $query->where(fn (Builder $q) => $q
            ->where('nombre', 'like', $term)
            ->orWhere('numero_documento', 'like', $term)
            ->orWhere('contacto_nombre', 'like', $term)
            ->orWhere('email', 'like', $term)
        );
    }

    // ── Helpers ─────────────────────────────────────────────────────────

    public function getDocumentoCompletoAttribute(): ?string
    {
        if (! $this->numero_documento) {
            return null;
        }

        $tipo = self::TIPOS_DOCUMENTO[$this->tipo_documento] ?? strtoupper($this->tipo_documento);

        return "{$tipo} {$this->numero_documento}";
    }
}
