<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreImpresoraContadorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('gestionarMantenimientos', $this->route('impresora'));
    }

    public function rules(): array
    {
        return [
            'contador' => ['required', 'integer', 'min:0', 'max:999999999'],
            // El contador solo baja si se cambió la tarjeta o se reinició el equipo
            'reinicio' => ['nullable', 'boolean'],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator) {
                $actual = $this->route('impresora')->contador_paginas_actual;

                if ($actual !== null && (int) $this->input('contador') < $actual && ! $this->boolean('reinicio')) {
                    $validator->errors()->add('contador',
                        'El contador es menor que el último registrado (' . number_format($actual, 0, ',', '.') . '). '
                        . 'Si se reinició (cambio de tarjeta, reseteo), marque la casilla correspondiente.');
                }
            },
        ];
    }

    public function attributes(): array
    {
        return ['contador' => 'contador de páginas'];
    }
}
