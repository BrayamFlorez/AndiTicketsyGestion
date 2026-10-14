<?php

namespace App\Http\Requests\Proyectos;

use App\Models\ProyectoAdjunto;
use App\Models\ProyectoEtapa;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EtapaRequest extends FormRequest
{
    /** Alta: permiso de registrar en el proyecto. Edición: solo autor, responsable o editor. */
    public function authorize(): bool
    {
        $proyecto = $this->route('proyecto');
        $registro = $this->route('etapa');

        return $registro
            ? $this->user()->can('modificarRegistro', [$proyecto, $registro])
            : $this->user()->can('registrar', $proyecto);
    }

    public function rules(): array
    {
        return array_merge([
            'tipo'          => ['required', Rule::in(array_keys(ProyectoEtapa::TIPOS))],
            'fecha'         => ['required', 'date'],
            'estado'        => ['required', Rule::in(array_keys(ProyectoEtapa::ESTADOS))],
            'titulo'        => ['nullable', 'string', 'max:150'],
            'descripcion'   => ['required', 'string', 'max:5000'],
            'monto'         => ['nullable', 'numeric', 'min:0', 'max:999999999999'],
            'fecha_validez' => ['nullable', 'date'],
            'medio_pago'    => ['nullable', Rule::in(array_keys(ProyectoEtapa::MEDIOS_PAGO))],
            'version'       => ['nullable', 'string', 'max:30'],
            'ronda'         => ['nullable', 'integer', 'min:1', 'max:999'],
            'avance'        => ['nullable', 'integer', 'min:0', 'max:100'],
            'enlace'        => ['nullable', 'url:http,https', 'max:500'],
            'atiende'       => ['nullable', 'array'],
            'atiende.*'     => ['integer'],
        ], ProyectoAdjunto::reglas());
    }

    public function attributes(): array
    {
        return [
            'descripcion'   => 'descripción',
            'fecha_validez' => 'válida hasta',
            'medio_pago'    => 'medio de pago',
            'archivos.*'    => 'archivo',
        ];
    }

    public function messages(): array
    {
        return [
            'archivos.*.max'        => 'Cada archivo puede pesar máximo 20 MB.',
            'archivos.*.extensions' => 'Tipo de archivo no permitido.',
            'archivos.*.uploaded'   => 'Un archivo no se pudo subir. Revisá que no supere 20 MB.',
        ];
    }
}
