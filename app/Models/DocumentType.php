<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class DocumentType extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'slug',
        'descripcion',
        'icono',
        'workflow_template_id',
        'activo',
    ];

    protected $casts = ['activo' => 'boolean'];

    /**
     * Genera el slug automáticamente si no viene en la petición.
     */
    protected static function booted(): void
    {
        static::creating(function (self $tipo) {
            $tipo->slug ??= Str::slug($tipo->nombre);
        });
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(WorkflowTemplate::class, 'workflow_template_id');
    }

    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }
}