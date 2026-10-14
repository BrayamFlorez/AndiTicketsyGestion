<?php

namespace App\Http\Requests;

use App\Models\Equipo;
use App\Models\Sede;
use App\Services\Reportes\InventarioReporteService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ReporteInventarioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('reportes.inventario');
    }

    public function rules(): array
    {
        return [
            'tipos'         => ['required', 'array', 'min:1'],
            'tipos.*'       => ['in:equipos,impresoras'],
            'empresa_id'    => ['nullable', 'integer', 'exists:empresas,id'],
            'sede_id'       => ['nullable', 'integer', 'exists:sedes,id'],
            'categoria'     => ['nullable', Rule::in(array_keys(Equipo::CATEGORIAS))],
            'incluir_bajas' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'tipos.required' => 'Elegí al menos un tipo de inventario (equipos o impresoras).',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // La empresa debe estar dentro del alcance del usuario
            if ($this->filled('empresa_id')) {
                $permitidas = InventarioReporteService::empresasDisponiblesPara($this->user())->pluck('id');

                if (! $permitidas->contains((int) $this->empresa_id)) {
                    $validator->errors()->add('empresa_id', 'No tenés alcance sobre la empresa seleccionada.');
                }
            }

            // La sede exige empresa y debe pertenecerle
            if ($this->filled('sede_id')) {
                if (! $this->filled('empresa_id')) {
                    $validator->errors()->add('sede_id', 'Para filtrar por sede primero elegí la empresa.');
                    return;
                }

                $valida = Sede::where('id', $this->sede_id)
                    ->where('empresa_id', $this->empresa_id)
                    ->exists();

                if (! $valida) {
                    $validator->errors()->add('sede_id', 'La sede no pertenece a la empresa seleccionada.');
                }
            }
        });
    }

    /**
     * Filtros normalizados para el servicio.
     */
    public function filtros(): array
    {
        return [
            'tipos'         => array_values(array_unique($this->validated('tipos'))),
            'empresa_id'    => $this->filled('empresa_id') ? (int) $this->empresa_id : null,
            'sede_id'       => $this->filled('sede_id') ? (int) $this->sede_id : null,
            'categoria'     => $this->validated('categoria') ?: null,
            'incluir_bajas' => $this->boolean('incluir_bajas'),
        ];
    }
}