<?php

namespace App\Http\Requests\Proyectos;

use App\Models\ProyectoAdjunto;
use App\Models\ProyectoSolicitud;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SolicitudRequest extends FormRequest
{
    /** Alta: permiso de registrar en el proyecto. Edición: solo autor, responsable o editor. */
    public function authorize(): bool
    {
        $proyecto = $this->route('proyecto');
        $registro = $this->route('solicitud');

        return $registro
            ? $this->user()->can('modificarRegistro', [$proyecto, $registro])
            : $this->user()->can('registrar', $proyecto);
    }

    public function rules(): array
    {
        return array_merge([
            'fecha_solicitud' => ['required', 'date', 'before_or_equal:today'],
            'canal'           => ['required', Rule::in(array_keys(ProyectoSolicitud::CANALES))],
            'prioridad'       => ['required', Rule::in(array_keys(ProyectoSolicitud::PRIORIDADES))],
            'titulo'          => ['required', 'string', 'max:150'],
            'descripcion'     => ['required', 'string', 'max:5000'],
        ], ProyectoAdjunto::reglas());
    }

    public function attributes(): array
    {
        return [
            'fecha_solicitud' => 'fecha de la solicitud',
            'descripcion'     => 'descripción',
            'titulo'          => 'título',
            'archivos.*'      => 'archivo',
        ];
    }

    public function messages(): array
    {
        return [
            'fecha_solicitud.before_or_equal' => 'La fecha de la solicitud no puede ser futura.',
            'archivos.*.max'                  => 'Cada archivo puede pesar máximo 20 MB.',
            'archivos.*.extensions'           => 'Tipo de archivo no permitido.',
            'archivos.*.uploaded'             => 'Un archivo no se pudo subir. Revisá que no supere 20 MB.',
        ];
    }
}
