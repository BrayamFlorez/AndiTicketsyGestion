<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/** Pasar el ticket a "Esperando partes". La autorización la hace el controlador. */
class SolicitarPartesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('tickets.resolver') ?? false;
    }

    public function rules(): array
    {
        return [
            'partes'         => ['required', 'string', 'min:3', 'max:2000'],
            'fecha_estimada' => ['nullable', 'date', 'after_or_equal:today'],
            'costo_estimado' => ['nullable', 'numeric', 'min:0', 'max:999999999999'],
            'evidencia'      => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,pdf', 'max:5120'],
        ];
    }

    public function messages(): array
    {
        return [
            'partes.required'               => 'Indica qué partes se solicitaron.',
            'fecha_estimada.after_or_equal' => 'La fecha estimada de llegada no puede ser anterior a hoy.',
            'evidencia.mimes'               => 'La evidencia debe ser una imagen (JPG, PNG, WEBP) o un PDF.',
            'evidencia.max'                 => 'La evidencia no puede superar los 5MB.',
        ];
    }

    public function attributes(): array
    {
        return [
            'partes'         => 'partes solicitadas',
            'fecha_estimada' => 'fecha estimada de llegada',
            'costo_estimado' => 'costo estimado',
        ];
    }
}
