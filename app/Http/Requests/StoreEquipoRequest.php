<?php

namespace App\Http\Requests;

use App\Models\Empresa;
use App\Models\Equipo;
use App\Models\Sede;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreEquipoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('equipos.crear') ?? false;
    }

    public function rules(): array
    {
        $categoria = $this->input('categoria');
        $schema    = Equipo::ESPECIFICACIONES_SCHEMA[$categoria] ?? [];

        $rules = [
            'categoria'      => ['required', Rule::in(array_keys(Equipo::CATEGORIAS))],
            'empresa_id'     => ['required', 'integer', 'exists:empresas,id'],
            'sede_id'        => ['nullable', 'integer', 'exists:sedes,id'],
            'responsable_id' => ['nullable', 'integer', 'exists:users,id'],
            'nombre'         => ['required', 'string', 'max:150'],
            'marca'          => ['nullable', 'string', 'max:100'],
            'modelo'         => ['nullable', 'string', 'max:100'],
            'numero_serie'   => ['nullable', 'string', 'max:100'],
            'ubicacion'      => ['nullable', 'string', 'max:255'],
            'estado'         => ['nullable', Rule::in(array_keys(Equipo::ESTADOS))],
            'notas'          => ['nullable', 'string', 'max:2000'],
        ];

        // Reglas dinámicas según schema de la categoría
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

    /**
     * Validaciones cruzadas — coherencia y autorizacion granular.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $user = $this->user();
            $esAdmin = $user && $user->hasAnyRole(['admin', 'super-admin']);

            // 1. Sede pertenece a la empresa
            if ($this->filled('sede_id') && $this->filled('empresa_id')) {
                $sede = Sede::find($this->sede_id);
                if (! $sede || $sede->empresa_id != $this->empresa_id) {
                    $validator->errors()->add('sede_id', 'La sede seleccionada no pertenece a la empresa elegida.');
                    return;
                }
            }

            // 2. Responsable pertenece a la empresa
            if ($this->filled('responsable_id')) {
                $valido = User::where('id', $this->responsable_id)
                    ->where('empresa_id', $this->empresa_id)
                    ->exists();
                if (! $valido) {
                    $validator->errors()->add('responsable_id', 'El responsable seleccionado no pertenece a la empresa elegida.');
                }
            }

            // 3. Si NO es admin, validar acceso por cobertura empresa+sede
            if (! $esAdmin && $user) {

                $empresa = Empresa::find($this->empresa_id);

                if (! $empresa || ! $user->puedeAccederEmpresa($empresa)) {
                    $validator->errors()->add('empresa_id',
                        'No tienes esta empresa asignada para gestionar.');
                    return;
                }

                // Si especificó sede, debe tener acceso a esa sede específicamente
                if ($this->filled('sede_id')) {
                    $sede = Sede::find($this->sede_id);
                    if ($sede && ! $user->puedeAccederSede($sede)) {
                        $validator->errors()->add('sede_id',
                            'No tienes acceso a esta sede. Solo puedes registrar equipos en las sedes que tienes asignadas.');
                    }
                }
                // Si NO especificó sede (equipo "de empresa"), exigir asignación global
                else {
                    if (! $user->tieneAsignacionGlobalEmpresa($this->empresa_id)) {
                        $validator->errors()->add('sede_id',
                            'Tu asignación es solo a sedes específicas. Debes elegir una sede para el equipo.');
                    }
                }
            }
        });
    }
}
