<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ImpresoraModelo extends Model
{
    protected $table = 'impresora_modelos';

    protected $fillable = [
        'marca', 'modelo', 'tipo_tecnologia', 'color',
        'es_multifuncional', 'consumibles_sugeridos', 'activo',
    ];

    protected $casts = [
        'color'                 => 'boolean',
        'es_multifuncional'     => 'boolean',
        'activo'                => 'boolean',
        'consumibles_sugeridos' => 'array',
    ];

    public const TIPOS_TECNOLOGIA = [
        'laser'     => 'Láser',
        'inyeccion' => 'Inyección de tinta',
        'termica'   => 'Térmica',
        'matricial' => 'Matricial',
    ];

    public function impresoras(): HasMany
    {
        return $this->hasMany(Impresora::class, 'modelo_id');
    }

    public function getNombreCompletoAttribute(): string
    {
        return trim("{$this->marca} {$this->modelo}");
    }

    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }
}