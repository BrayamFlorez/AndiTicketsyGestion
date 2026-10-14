<?php

namespace App\Rules;

use App\Models\Equipo;
use App\Models\User;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Valida que el número de serie no esté registrado en otro equipo.
 *
 * Reemplaza a Rule::unique('equipos', 'numero_serie') para dar un mensaje útil:
 * si el usuario tiene cobertura sobre el equipo que ya usa ese serial, le dice cuál es;
 * si no, solo avisa que existe (no expone equipos de otras empresas).
 */
class NumeroSerieDisponible implements ValidationRule
{
    public function __construct(
        private readonly ?User $usuario = null,
        private readonly ?int $ignorarId = null,
    ) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ($value === null || $value === '') {
            return;
        }

        $existente = Equipo::query()
            ->where('numero_serie', $value)
            ->when($this->ignorarId, fn ($q) => $q->whereKeyNot($this->ignorarId))
            ->first();

        if (! $existente) {
            return;
        }

        if ($this->usuario && $existente->puedeSerGestionadoPor($this->usuario)) {
            $fail("El número de serie ya está registrado en el equipo \"{$existente->nombre}\". Verificá que no sea el mismo equipo.");

            return;
        }

        $fail('El número de serie ya está registrado en otro equipo del sistema.');
    }
}
