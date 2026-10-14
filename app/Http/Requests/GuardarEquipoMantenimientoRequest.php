<?php

namespace App\Http\Requests;

use App\Models\EquipoMantenimiento;
use App\Models\Ticket;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

/** Crear o editar un servicio técnico a un PC (los datos del formato, sin fotos ni firmas). */
class GuardarEquipoMantenimientoRequest extends FormRequest
{
    public function authorize(): bool
    {
        $equipo = $this->route('equipo');

        return $this->user()->can('equipos.mantenimientos.gestionar')
            && $equipo->puedeSerGestionadoPor($this->user());
    }

    /** Las casillas sin marcar no se envían: se completan en false */
    protected function prepareForValidation(): void
    {
        $unidades = collect($this->input('unidades', []))
            ->filter(fn ($u) => is_array($u) && trim((string) ($u['unidad'] ?? '')) !== '')
            ->map(function (array $u) {
                foreach (array_keys(EquipoMantenimiento::ACCIONES) as $accion) {
                    $u[$accion] = filter_var($u[$accion] ?? false, FILTER_VALIDATE_BOOLEAN);
                }
                return $u;
            })
            ->values()->all();

        $partes = collect($this->input('partes', []))
            ->filter(fn ($p) => is_array($p) && collect($p)->filter(fn ($v) => trim((string) $v) !== '')->isNotEmpty())
            ->values()->all();

        $this->merge(['unidades' => $unidades, 'partes' => $partes]);
    }

    public function rules(): array
    {
        $acciones = collect(EquipoMantenimiento::ACCIONES)
            ->keys()
            ->mapWithKeys(fn ($a) => ["unidades.*.{$a}" => ['boolean']])
            ->all();

        return [
            'tipo'             => ['required', Rule::in(array_keys(EquipoMantenimiento::TIPOS))],
            'modalidad'        => ['nullable', Rule::in(array_keys(EquipoMantenimiento::MODALIDADES))],
            'deposito'         => ['nullable', 'string', 'max:100'],
            'ticket_id'        => ['nullable', 'integer', 'exists:tickets,id'],
            'tecnico_id'       => ['required', 'integer', Rule::exists('users', 'id')->where('activo', true)],

            'fecha_ingreso'    => ['nullable', 'date', 'before_or_equal:fecha_servicio'],
            'fecha_servicio'   => ['required', 'date', 'before_or_equal:today'],
            'hora_inicio'      => ['nullable', 'date_format:H:i'],
            'hora_fin'         => ['nullable', 'date_format:H:i', 'after:hora_inicio'],
            'contador_inicial' => ['nullable', 'string', 'max:50'],
            'contador_final'   => ['nullable', 'string', 'max:50'],

            'falla'            => ['required', 'string', 'min:5', 'max:3000'],
            'observaciones'    => ['nullable', 'string', 'max:5000'],

            'unidades'                 => ['array', 'max:40'],
            'unidades.*.unidad'        => ['required', 'string', 'max:150'],
            'unidades.*.periferico_id' => ['nullable', 'integer'],

            'partes'                    => ['array', 'max:20'],
            'partes.*.nombre'           => ['required', 'string', 'max:150'],
            'partes.*.numero_parte'     => ['nullable', 'string', 'max:60'],
            'partes.*.serial_original'  => ['nullable', 'string', 'max:100'],
            'partes.*.serial_reemplazo' => ['nullable', 'string', 'max:100'],
        ] + $acciones;
    }

    public function after(): array
    {
        return [
            function (Validator $validator) {
                $equipo = $this->route('equipo');

                // La orden de trabajo debe ser un ticket de este equipo
                if ($this->filled('ticket_id') && ! $validator->errors()->has('ticket_id')
                    && ! Ticket::whereKey($this->integer('ticket_id'))->where('equipo_id', $equipo->id)->exists()) {
                    $validator->errors()->add('ticket_id', 'El ticket elegido no está vinculado a este equipo.');
                }

                // Los periféricos de las filas deben ser de este equipo
                $ids = collect($this->input('unidades', []))->pluck('periferico_id')->filter()->unique();
                if ($ids->isNotEmpty() && $equipo->perifericos()->whereIn('id', $ids)->count() !== $ids->count()) {
                    $validator->errors()->add('unidades', 'Hay periféricos que no pertenecen a este equipo.');
                }
            },
        ];
    }

    public function messages(): array
    {
        return [
            'falla.required'                => 'Describa la falla o el motivo del servicio.',
            'fecha_servicio.before_or_equal' => 'La fecha de servicio no puede ser futura.',
            'fecha_ingreso.before_or_equal' => 'La fecha de ingreso no puede ser posterior a la de servicio.',
            'hora_fin.after'                => 'La hora final debe ser posterior a la inicial.',
            'unidades.*.unidad.required'    => 'Cada unidad revisada necesita un nombre.',
            'partes.*.nombre.required'      => 'Cada parte necesita un nombre.',
        ];
    }

    public function attributes(): array
    {
        return [
            'tecnico_id'     => 'técnico',
            'fecha_servicio' => 'fecha de servicio',
            'fecha_ingreso'  => 'fecha de ingreso',
            'hora_inicio'    => 'hora inicial',
            'hora_fin'       => 'hora final',
        ];
    }
}
