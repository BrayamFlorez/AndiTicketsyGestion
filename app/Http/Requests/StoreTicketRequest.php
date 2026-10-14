<?php

namespace App\Http\Requests;

use App\Models\Equipo;
use App\Models\Impresora;
use App\Models\Sede;
use App\Models\Ticket;
use App\Models\User;
use App\Support\Roles;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTicketRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('tickets.crear') ?? false;
    }

    /**
     * El formulario envía el equipo vinculado en un solo campo "vinculo" ("equipo:12" o "impresora:5");
     * aquí se separa en equipo_id / impresora_id.
     */
    protected function prepareForValidation(): void
    {
        if (preg_match('/^(equipo|impresora):(\d+)$/', (string) $this->input('vinculo'), $m)) {
            $this->merge([$m[1] . '_id' => (int) $m[2]]);
        }
    }

    public function rules(): array
    {
        $rules = [
            'titulo'         => ['required', 'string', 'max:150'],
            'categoria'      => ['required', Rule::in(array_keys(Ticket::CATEGORIAS))],
            'prioridad'      => ['required', Rule::in(array_keys(Ticket::PRIORIDADES))],
            'descripcion'    => ['required', 'string', 'min:10', 'max:5000'],
            'foto_solicitud' => ['nullable', 'image', 'max:5120'], // 5MB

            // Equipo o impresora vinculada (opcional, nunca ambos)
            'vinculo'        => ['nullable', 'regex:/^(equipo|impresora):\d+$/'],
            'equipo_id'      => ['nullable', 'integer', 'exists:equipos,id', 'prohibits:impresora_id'],
            'impresora_id'   => ['nullable', 'integer', 'exists:impresoras,id'],
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

            // El equipo vinculado debe ser de la empresa (y sede, si se eligió) del ticket
            $this->validarActivoVinculado($validator);

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

    /** El equipo o la impresora elegida, ya validada (o null). */
    public function activoVinculado(): Equipo|Impresora|null
    {
        return match (true) {
            $this->filled('equipo_id')    => Equipo::find($this->integer('equipo_id')),
            $this->filled('impresora_id') => Impresora::find($this->integer('impresora_id')),
            default                       => null,
        };
    }

    private function validarActivoVinculado($validator): void
    {
        if ($validator->errors()->hasAny(['vinculo', 'equipo_id', 'impresora_id'])) {
            return;
        }

        $activo = $this->activoVinculado();
        if (! $activo) {
            return;
        }

        // Misma regla que el controlador: quien elige destino usa la empresa del formulario
        $empresaId = Roles::usuarioCumple($this->user(), 'eligeDestinoTicket')
            ? (int) $this->input('empresa_id')
            : (int) $this->user()->empresa_id;

        if ((int) $activo->empresa_id !== $empresaId) {
            $validator->errors()->add('vinculo', 'El equipo seleccionado no pertenece a la empresa del ticket.');
        } elseif ($this->filled('sede_id') && $activo->sede_id && (int) $activo->sede_id !== (int) $this->input('sede_id')) {
            $validator->errors()->add('vinculo', 'El equipo seleccionado está en otra sede.');
        }
    }

    public function messages(): array
    {
        return [
            'titulo.required'      => 'El título es obligatorio.',
            'descripcion.min'      => 'La descripción debe tener al menos :min caracteres.',
            'foto_solicitud.image' => 'El archivo adjunto debe ser una imagen.',
            'foto_solicitud.max'   => 'La imagen no puede superar los 5MB.',
            'vinculo.regex'        => 'El equipo seleccionado no es válido.',
            'equipo_id.exists'     => 'El equipo seleccionado no existe.',
            'impresora_id.exists'  => 'La impresora seleccionada no existe.',
            'equipo_id.prohibits'  => 'Vincule un equipo o una impresora, no ambos.',
        ];
    }
}
