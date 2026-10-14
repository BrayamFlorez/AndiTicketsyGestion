<?php

namespace App\Http\Requests\Proyectos;

use App\Models\Cliente;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ClienteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('clientes.gestionar');
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'activo'           => $this->boolean('activo'),
            'numero_documento' => $this->filled('numero_documento') ? trim($this->input('numero_documento')) : null,
        ]);
    }

    public function rules(): array
    {
        $cliente = $this->route('cliente');

        return [
            'nombre'           => ['required', 'string', 'max:150'],
            'tipo_documento'   => ['required', Rule::in(array_keys(Cliente::TIPOS_DOCUMENTO))],
            'numero_documento' => ['nullable', 'string', 'max:30', Rule::unique('clientes', 'numero_documento')->ignore($cliente?->id)],
            'contacto_nombre'  => ['nullable', 'string', 'max:120'],
            'contacto_cargo'   => ['nullable', 'string', 'max:80'],
            'telefono'         => ['nullable', 'string', 'max:30'],
            'email'            => ['nullable', 'email', 'max:150'],
            'direccion'        => ['nullable', 'string', 'max:200'],
            'ciudad'           => ['nullable', 'string', 'max:80'],
            'observaciones'    => ['nullable', 'string', 'max:2000'],
            'activo'           => ['boolean'],
        ];
    }

    public function attributes(): array
    {
        return [
            'numero_documento' => 'número de documento',
            'contacto_nombre'  => 'nombre del contacto',
        ];
    }
}
