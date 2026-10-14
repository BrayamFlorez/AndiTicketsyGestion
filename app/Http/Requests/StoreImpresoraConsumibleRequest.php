<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreImpresoraConsumibleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('gestionarConsumibles', $this->route('impresora'));
    }

    public function rules(): array
    {
        return [
            'tipo'              => ['required', 'in:toner,cartucho,tanque_tinta,tambor,fusor,cabezal,kit_mantenimiento,otro'],
            'color'             => ['required', 'in:negro,cyan,magenta,amarillo,unico,no_aplica'],
            'etiqueta'          => ['nullable', 'string', 'max:80'],
            'dias_estimados'    => ['required', 'integer', 'min:1', 'max:3650'],
            'fecha_instalacion' => ['nullable', 'date', 'before_or_equal:today'],
            'observaciones'     => ['nullable', 'string', 'max:1000'],
        ];
    }
}