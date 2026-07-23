<?php

namespace App\Http\Requests;

use App\Models\Sede;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTicketRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('tickets.crear') ?? false;
    }

    public function rules(): array
    {
        $rules = [
            'titulo'         => ['required', 'string', 'max:150'],
            'categoria'      => ['required', Rule::in(array_keys(Ticket::CATEGORIAS))],
            'prioridad'      => ['required', Rule::in(array_keys(Ticket::PRIORIDADES))],
            'descripcion'    => ['required', 'string', 'min:10', 'max:5000'],
            'foto_solicitud' => ['nullable', 'image', 'max:5120'], // 5MB
        ];

        // Reglas adicionales sólo para admin / super-admin
        if ($this->user()->hasAnyRole(['admin', 'super-admin'])) {
            $rules['solicitante_id'] = ['required', 'integer', 'exists:users,id'];
            $rules['empresa_id']     = ['required', 'integer', 'exists:empresas,id'];
            $rules['sede_id']        = ['nullable', 'integer', 'exists:sedes,id'];
            $rules['asignado_a']     = ['nullable', 'integer', 'exists:users,id'];
        }

        return $rules;
    }

    /**
     * Validación cruzada: la sede debe pertenecer a la empresa,
     * y el técnico asignado (si aplica) debe tener el rol 'tecnico'.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {

            if (! $this->user()->hasAnyRole(['admin', 'super-admin'])) {
                return;
            }

            // Sede ↔ Empresa coherentes
            if ($this->filled('sede_id') && $this->filled('empresa_id')) {
                $sedeValida = Sede::where('id', $this->sede_id)
                    ->where('empresa_id', $this->empresa_id)
                    ->exists();

                if (! $sedeValida) {
                    $validator->errors()->add('sede_id', 'La sede seleccionada no pertenece a la empresa elegida.');
                }
            }

            // El usuario asignado debe ser técnico activo
            if ($this->filled('asignado_a')) {
                $esTecnico = User::where('id', $this->asignado_a)
                    ->where('activo', true)
                    ->role('tecnico')
                    ->exists();

                if (! $esTecnico) {
                    $validator->errors()->add('asignado_a', 'El usuario seleccionado no es un técnico activo válido.');
                }
            }
        });
    }

    public function messages(): array
    {
        return [
            'titulo.required'      => 'El título es obligatorio.',
            'descripcion.min'      => 'La descripción debe tener al menos :min caracteres.',
            'foto_solicitud.image' => 'El archivo adjunto debe ser una imagen.',
            'foto_solicitud.max'   => 'La imagen no puede superar los 5MB.',
        ];
    }
}