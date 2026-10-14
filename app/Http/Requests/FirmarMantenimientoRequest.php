<?php

namespace App\Http\Requests;

use App\Services\Impresoras\FirmaDigital;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

/** Firma del técnico y del cliente: con ambas el mantenimiento queda finalizado. */
class FirmarMantenimientoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('gestionarMantenimientos', $this->route('impresora'));
    }

    public function rules(): array
    {
        return [
            'firma_tecnico'     => ['required', 'string'],
            'firma_cliente'     => ['required', 'string'],
            'cliente_nombre'    => ['required', 'string', 'max:150'],
            'cliente_cargo'     => ['nullable', 'string', 'max:100'],
            'cliente_documento' => ['nullable', 'string', 'max:30'],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator) {
                $firmas = app(FirmaDigital::class);

                foreach (['firma_tecnico' => 'del técnico', 'firma_cliente' => 'del cliente'] as $campo => $de) {
                    if (! $validator->errors()->has($campo) && ($error = $firmas->error($this->input($campo)))) {
                        $validator->errors()->add($campo, "Firma {$de}: " . lcfirst($error));
                    }
                }
            },
        ];
    }

    public function messages(): array
    {
        return [
            'firma_tecnico.required'  => 'Falta la firma del técnico.',
            'firma_cliente.required'  => 'Falta la firma del cliente.',
            'cliente_nombre.required' => 'Escriba el nombre de quien recibe y firma por el cliente.',
        ];
    }
}
