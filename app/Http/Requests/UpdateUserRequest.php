<?php

namespace App\Http\Requests;

use App\Models\Sede;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

    class UpdateUserRequest extends FormRequest
    {
    public function authorize(): bool
    {
        return $this->user()?->can('usuarios.editar') ?? false;
    }

    public function rules(): array
    {
        $usuario = $this->route('usuario'); // Modelo bindeado

        return [
            'name'     => ['required', 'string', 'max:150'],
            'email'    => ['required', 'email', 'max:255',
                            Rule::unique('users', 'email')->ignore($usuario->id)],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'rol'      => ['required', Rule::in($this->rolesPermitidos())],
            'activo'   => ['nullable', 'boolean'],

            'empresa_id' => ['nullable', 'integer', 'exists:empresas,id', 'required_if:rol,usuario'],
            'sede_id'    => ['nullable', 'integer', 'exists:sedes,id'],

            'coberturas'                => ['nullable', 'array'],
            'coberturas.*.empresa_id'   => ['required_with:coberturas.*.sede_id', 'integer', 'exists:empresas,id'],
            'coberturas.*.sede_id'      => ['nullable', 'integer', 'exists:sedes,id'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $usuario = $this->route('usuario');
            $actor   = $this->user();

            // Evitar que el usuario se cambie a sí mismo a un rol sin acceso
            if ($usuario->id === $actor->id && $this->input('rol') !== ($usuario->roles->first()?->name)) {
                $validator->errors()->add('rol', 'No puedes cambiar tu propio rol.');
            }

            // Sede de usuario final pertenece a la empresa
            if ($this->input('rol') === 'usuario'
                && $this->filled('sede_id') && $this->filled('empresa_id')) {
                $valido = Sede::where('id', $this->sede_id)
                    ->where('empresa_id', $this->empresa_id)
                    ->exists();
                if (! $valido) {
                    $validator->errors()->add('sede_id', 'La sede no pertenece a la empresa elegida.');
                }
            }

            // Coberturas de técnico: sede pertenece a empresa
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
            'password.confirmed' => 'La confirmación de contraseña no coincide.',
            'email.unique'       => 'Este email ya está registrado por otro usuario.',
        ];
    }
}
