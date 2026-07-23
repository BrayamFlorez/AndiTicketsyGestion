<?php

namespace App\Http\Requests;

use App\Models\Sede;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('usuarios.crear') ?? false;
    }

    public function rules(): array
    {
        return [
            'name'     => ['required', 'string', 'max:150'],
            'email'    => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'rol'      => ['required', Rule::in($this->rolesPermitidos())],
            'activo'   => ['nullable', 'boolean'],

            // Para usuario final
            'empresa_id' => ['nullable', 'integer', 'exists:empresas,id', 'required_if:rol,usuario'],
            'sede_id'    => ['nullable', 'integer', 'exists:sedes,id'],

            // Para técnicos (array de coberturas)
            'coberturas'                => ['nullable', 'array'],
            'coberturas.*.empresa_id'   => ['required_with:coberturas.*.sede_id', 'integer', 'exists:empresas,id'],
            'coberturas.*.sede_id'      => ['nullable', 'integer', 'exists:sedes,id'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // Si es usuario final, la sede (si se da) debe pertenecer a la empresa
            if ($this->input('rol') === 'usuario'
                && $this->filled('sede_id') && $this->filled('empresa_id')) {
                $valido = Sede::where('id', $this->sede_id)
                    ->where('empresa_id', $this->empresa_id)
                    ->exists();
                if (! $valido) {
                    $validator->errors()->add('sede_id', 'La sede no pertenece a la empresa elegida.');
                }
            }

            // Si es técnico, cada cobertura (si tiene sede) debe coincidir empresa+sede
            if ($this->input('rol') === 'tecnico' && $this->filled('coberturas')) {
                foreach ($this->coberturas as $i => $cob) {
                    if (! empty($cob['empresa_id']) && ! empty($cob['sede_id'])) {
                        $valido = Sede::where('id', $cob['sede_id'])
                            ->where('empresa_id', $cob['empresa_id'])
                            ->exists();
                        if (! $valido) {
                            $validator->errors()->add("coberturas.{$i}.sede_id",
                                'La sede no pertenece a la empresa de esta fila.');
                        }
                    }
                }
            }
        });
    }

    /**
     * Roles que el creador puede asignar — admin no puede crear admins ni super-admins.
     */
    private function rolesPermitidos(): array
    {
        $todos = Role::pluck('name')->toArray();
        if ($this->user()->hasRole('super-admin')) {
            return $todos;
        }
        return array_values(array_diff($todos, ['super-admin', 'admin']));
    }

    public function messages(): array
    {
        return [
            'password.confirmed'    => 'La confirmación de contraseña no coincide.',
            'password.min'          => 'La contraseña debe tener al menos :min caracteres.',
            'empresa_id.required_if' => 'Los usuarios finales deben tener una empresa asignada.',
        ];
    }
}
