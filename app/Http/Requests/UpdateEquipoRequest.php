<?php

namespace App\Http\Requests;

use App\Models\Empresa;
use App\Models\Equipo;
use App\Models\Sede;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEquipoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('equipos.editar') ?? false;
    }

    public function rules(): array
    {
        $categoria = $this->input('categoria');
        $schema    = Equipo::ESPECIFICACIONES_SCHEMA[$categoria] ?? [];
        $equipoId  = $this->route('equipo')->id;

        $rules = [
            'categoria'      => ['required', Rule::in(array_keys(Equipo::CATEGORIAS))],
            'empresa_id'     => ['required', 'integer', 'exists:empresas,id'],
            'sede_id'        => ['nullable', 'integer', 'exists:sedes,id'],
            'responsable_id' => ['nullable', 'integer', 'exists:users,id'],
            'nombre'         => ['required', 'string', 'max:150'],
            'marca'          => ['nullable', 'string', 'max:100'],
            'modelo'         => ['nullable', 'string', 'max:100'],
            'numero_serie'   => [
                'nullable', 'string', 'max:100',
                // Unique por empresa, ignorando el equipo actual
                Rule::unique('equipos', 'numero_serie')
                    ->where(fn ($q) => $q->where('empresa_id', $this->input('empresa_id')))
                    ->ignore($equipoId),
            ],
            'ubicacion'      => ['nullable', 'string', 'max:255'],
            'estado'         => ['nullable', Rule::in(array_keys(Equipo::ESTADOS))],
            'notas'          => ['nullable', 'string', 'max:2000'],
            'codigo_interno' => ['nullable', 'string', 'max:60'],
        ];

        foreach ($schema as $campo => $config) {
            $required = $config['required'] ?? true;
            $reglasCampo = [$required ? 'required' : 'nullable'];

            if (($config['type'] ?? 'text') === 'number') {
                $reglasCampo[] = 'numeric';
                $reglasCampo[] = 'min:0';
            } else {
                $reglasCampo[] = 'string';
                $reglasCampo[] = 'max:200';
            }

            if (($config['type'] ?? null) === 'select' && isset($config['options'])) {
                $reglasCampo[] = Rule::in($config['options']);
            }

            $rules["especificaciones.{$campo}"] = $reglasCampo;
        }

        return $rules;
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $user    = $this->user();
            $esAdmin = $user && $user->hasAnyRole(['admin', 'super-admin']);

            // Sede pertenece a la empresa
            if ($this->filled('sede_id') && $this->filled('empresa_id')) {
                $sede = Sede::find($this->sede_id);
                if (! $sede || $sede->empresa_id != $this->empresa_id) {
                    $validator->errors()->add('sede_id', 'La sede seleccionada no pertenece a la empresa elegida.');
                    return;
                }
            }

            // Responsable pertenece a la empresa
            if ($this->filled('responsable_id')) {
                $valido = User::where('id', $this->responsable_id)
                    ->where('empresa_id', $this->empresa_id)
                    ->exists();
                if (! $valido) {
                    $validator->errors()->add('responsable_id', 'El responsable no pertenece a la empresa elegida.');
                }
            }

            // Técnico: validar acceso por cobertura empresa+sede
            if (! $esAdmin && $user) {
                $empresa = Empresa::find($this->empresa_id);

                if (! $empresa || ! $user->puedeAccederEmpresa($empresa)) {
                    $validator->errors()->add('empresa_id', 'No tienes esta empresa asignada para gestionar.');
                    return;
                }

                if ($this->filled('sede_id')) {
                    $sede = Sede::find($this->sede_id);
                    if ($sede && ! $user->puedeAccederSede($sede)) {
                        $validator->errors()->add('sede_id',
                            'No tienes acceso a esta sede para gestionar equipos aquí.');
                    }
                } else {
                    if (! $user->tieneAsignacionGlobalEmpresa($this->empresa_id)) {
                        $validator->errors()->add('sede_id',
                            'Tu asignación es solo a sedes específicas. Elige una sede para el equipo.');
                    }
                }
            }
        });
    }
}
