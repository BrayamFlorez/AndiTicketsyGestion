<?php

namespace App\Http\Requests\Flujos;

use Illuminate\Foundation\Http\FormRequest;

class StoreWorkflowAttachmentRequest extends FormRequest
{
    /**
     * Autorización fina se resuelve en el controller vía Policy
     * (el destino puede ser instancia o step, con reglas distintas).
     */
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'archivo' => [
                'required',
                'file',
                'max:20480', // 20 MB
                'mimes:pdf,doc,docx,xls,xlsx,csv,txt,jpg,jpeg,png,zip',
            ],
            'workflow_instance_step_id' => [
                'nullable',
                'integer',
                'exists:workflow_instance_steps,id',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'archivo.max'   => 'El archivo no puede superar los 20 MB.',
            'archivo.mimes' => 'Formatos permitidos: PDF, Word, Excel, CSV, TXT, imágenes o ZIP.',
        ];
    }
}