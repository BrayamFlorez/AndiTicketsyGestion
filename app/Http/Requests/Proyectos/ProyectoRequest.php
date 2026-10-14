<?php

namespace App\Http\Requests\Proyectos;

use App\Models\Proyecto;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProyectoRequest extends FormRequest
{
    public function authorize(): bool
    {
        $proyecto = $this->route('proyecto');

        return $proyecto
            ? $this->user()->can('update', $proyecto)
            : $this->user()->can('create', Proyecto::class);
    }

    public function rules(): array
    {
        return [
            'nombre'                 => ['required', 'string', 'max:150'],
            'descripcion'            => ['nullable', 'string', 'max:5000'],
            'cliente_id'             => ['required', 'integer', Rule::exists('clientes', 'id')->whereNull('deleted_at')],
            'responsable_id'         => ['required', 'integer', Rule::exists('users', 'id')],
            'estado'                 => ['required', Rule::in(array_keys(Proyecto::ESTADOS))],
            'fecha_inicio'           => ['required', 'date'],
            'fecha_entrega_estimada' => ['nullable', 'date', 'after_or_equal:fecha_inicio'],
            'equipo'                 => ['nullable', 'array'],
            'equipo.*.activo'        => ['nullable', 'boolean'],
            'equipo.*.rol'           => ['nullable', 'string', 'max:60'],
        ];
    }

    public function withValidator($validator): void
    {
        // Las llaves de 'equipo' son ids de usuario: validar que existan
        $validator->after(function ($validator) {
            $ids = array_map('intval', array_keys((array) $this->input('equipo', [])));

            if ($ids && \App\Models\User::whereIn('id', $ids)->count() !== count($ids)) {
                $validator->errors()->add('equipo', 'El equipo contiene usuarios que no existen.');
            }
        });
    }

    public function attributes(): array
    {
        return [
            'cliente_id'             => 'cliente',
            'responsable_id'         => 'responsable',
            'fecha_inicio'           => 'fecha de inicio',
            'fecha_entrega_estimada' => 'fecha de entrega estimada',
        ];
    }
}
