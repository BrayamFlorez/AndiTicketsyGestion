<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreImpresoraModeloRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('impresoras.gestionar-catalogo');
    }

    public function rules(): array
    {
        return [
            'marca'             => ['required', 'string', 'max:80'],
            'modelo'            => [
                'required', 'string', 'max:120',
                Rule::unique('impresora_modelos')->where(fn ($q) =>
                    $q->where('marca', $this->marca)
                ),
            ],
            'tipo_tecnologia'   => ['required', 'in:laser,inyeccion,termica,matricial'],
            'color'             => ['nullable', 'boolean'],
            'es_multifuncional' => ['nullable', 'boolean'],
            'activo'            => ['nullable', 'boolean'],

            'consumibles'                => ['nullable', 'array'],
            'consumibles.*.tipo'         => ['required_with:consumibles', 'in:toner,cartucho,tanque_tinta,tambor,fusor,cabezal,kit_mantenimiento,otro'],
            'consumibles.*.color'        => ['required_with:consumibles', 'in:negro,cyan,magenta,amarillo,unico,no_aplica'],
        ];
    }

    public function messages(): array
    {
        return [
            'modelo.unique' => 'Ya existe ese modelo para esta marca.',
        ];
    }
}