<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RecargarConsumibleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('gestionarConsumibles', $this->route('impresora'));
    }

    public function rules(): array
    {
        return [
            // Confirmación del aviso: la recarga se está haciendo ahora mismo
            'en_tiempo_real' => ['accepted'],
            'nivel_antes'    => ['required', 'numeric', 'min:0', 'max:99'],
            'nivel_despues'  => ['required', 'numeric', 'max:100', 'gt:nivel_antes'],
            'observaciones'  => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'en_tiempo_real.accepted' => 'Esta función solo sirve si la recarga se está haciendo en este momento.',
            'nivel_despues.gt'        => 'El nivel después de recargar debe ser mayor que el nivel antes de recargar.',
        ];
    }

    public function attributes(): array
    {
        return [
            'nivel_antes'   => 'nivel antes de recargar',
            'nivel_despues' => 'nivel después de recargar',
        ];
    }
}
