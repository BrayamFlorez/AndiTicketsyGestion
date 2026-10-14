<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreImpresoraRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('impresoras.crear');
    }

    public function rules(): array
    {
        return [
            'modelo_id'            => ['nullable', 'integer', 'exists:impresora_modelos,id'],
            'empresa_id'           => ['required', 'integer', 'exists:empresas,id'],
            'sede_id'              => ['nullable', 'integer', 'exists:sedes,id'],
            'codigo_interno'       => ['nullable', 'string', 'max:50'],
            'nombre_asignado'      => ['nullable', 'string', 'max:120'],
            'ubicacion_fisica'     => ['nullable', 'string', 'max:200'],
            'ip_red'               => ['nullable', 'ip'],
            'hostname'             => ['nullable', 'string', 'max:120'],
            'mac_address'          => ['nullable', 'string', 'max:17', 'regex:/^([0-9A-Fa-f]{2}[:-]){5}[0-9A-Fa-f]{2}$/'],
            'panel_web_usuario'    => ['nullable', 'string', 'max:80'],
            'panel_web_password'   => ['nullable', 'string', 'max:200'],
            'numero_serie'         => ['nullable', 'string', 'max:80'],
            'fecha_adquisicion'    => ['nullable', 'date', 'before_or_equal:today'],
            'estado'               => ['required', 'in:activa,en_mantenimiento,dada_de_baja'],
            'observaciones'        => ['nullable', 'string', 'max:2000'],
            'intervalo_mantenimiento_dias' => ['nullable', 'integer', 'min:1', 'max:3650'],
        ];
    }

    public function messages(): array
    {
        return [
            'mac_address.regex' => 'El formato de la MAC debe ser AA:BB:CC:DD:EE:FF o AA-BB-CC-DD-EE-FF.',
        ];
    }
}