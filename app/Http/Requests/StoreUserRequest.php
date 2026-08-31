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
            'coberturas'              => ['nullable', 'array'],
            'coberturas.*.empresa_id' => ['nullable', 'integer', 'exists:empresas,id'],
            'coberturas.*.sede_id'    => ['nullable', 'integer', 'exists:sedes,id'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $rol = $this->input('rol');

            if (! \App\Support\Roles::requiereCobertura($rol)) {
                return;
            }

            $validas = collect($this->input('coberturas', []))
                ->filter(fn ($c) => ! empty($c['empresa_id']))
                ->count();

            if ($validas === 0) {
                $validator->errors()->add(
                    'coberturas',
                    'Este rol requiere al menos una cobertura de empresa asignada.'
                );
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
