<?php

namespace App\Http\Requests;

use App\Models\ImpresoraMantenimientoEvidencia;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SubirEvidenciaMantenimientoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('gestionarMantenimientos', $this->route('impresora'));
    }

    public function rules(): array
    {
        return [
            'momento'     => ['required', Rule::in(array_keys(ImpresoraMantenimientoEvidencia::MOMENTOS))],
            'descripcion' => ['nullable', 'string', 'max:255'],
            'fotos'       => ['required', 'array', 'min:1', 'max:12'],
            'fotos.*'     => ['image', 'mimes:jpg,jpeg,png,webp', 'max:10240'],
        ];
    }

    public function messages(): array
    {
        return [
            'fotos.required' => 'Seleccione al menos una foto.',
            'fotos.max'      => 'Suba como máximo :max fotos a la vez.',
            'fotos.*.image'  => 'Todos los archivos deben ser imágenes.',
            'fotos.*.mimes'  => 'Las fotos deben ser JPG, PNG o WEBP.',
            'fotos.*.max'    => 'Cada foto puede pesar como máximo 10 MB.',
        ];
    }
}
