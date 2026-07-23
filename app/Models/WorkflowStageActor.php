<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Permission\Models\Role;

class WorkflowStageActor extends Model
{
    use HasFactory;

    public const TIPO_ROL     = 'role';
    public const TIPO_USUARIO = 'user';

    protected $fillable = [
        'workflow_stage_id',
        'actor_type',
        'actor_id',
        'etiqueta',
    ];

    public function stage(): BelongsTo
    {
        return $this->belongsTo(WorkflowStage::class, 'workflow_stage_id');
    }

    /**
     * Resuelve el actor concreto (User o Role) según el tipo.
     */
    public function resolverActor(): ?Model
    {
        return match ($this->actor_type) {
            self::TIPO_USUARIO => User::find($this->actor_id),
            self::TIPO_ROL     => Role::find($this->actor_id),
            default            => null,
        };
    }

    public function esRol(): bool
    {
        return $this->actor_type === self::TIPO_ROL;
    }

    public function esUsuario(): bool
    {
        return $this->actor_type === self::TIPO_USUARIO;
    }
}