<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WorkflowTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'descripcion',
        'version',
        'activo',
        'created_by',
    ];

    protected $casts = [
        'activo'  => 'boolean',
        'version' => 'integer',
    ];

    public function stages(): HasMany
    {
        return $this->hasMany(WorkflowStage::class)->orderBy('orden');
    }

    public function documentTypes(): HasMany
    {
        return $this->hasMany(DocumentType::class);
    }

    public function instances(): HasMany
    {
        return $this->hasMany(WorkflowInstance::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }
}