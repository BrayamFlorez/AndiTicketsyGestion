<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreImpresoraMantenimientoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('gestionarMantenimientos', $this->route('impresora'));
    }

    public function rules(): array
    {
        return [
            'tipo'                => ['required', 'in:preventivo,correctivo,limpieza,cambio_consumible'],
            'fecha_realizado'     => ['required', 'date', 'before_or_equal:today'],
            'realizado_por'       => ['nullable', 'integer', 'exists:users,id'],
            'proximo_programado'  => ['nullable', 'date', 'after:fecha_realizado'],
            'contador_paginas'    => ['nullable', 'integer', 'min:0'],
            'descripcion'         => ['required', 'string', 'min:5', 'max:3000'],
            'observaciones'       => ['nullable', 'string', 'max:2000'],

            // Reemplazo integrado de consumibles
            'consumibles_reemplazados'                    => ['nullable', 'array'],
            'consumibles_reemplazados.*.consumible_id'    => ['required_with:consumibles_reemplazados', 'integer', 'exists:impresora_consumibles,id'],
            'consumibles_reemplazados.*.dias_estimados'   => ['required_with:consumibles_reemplazados', 'integer', 'min:1', 'max:3650'],
            'consumibles_reemplazados.*.recalibrar'       => ['nullable', 'boolean'],
        ];
    }
}