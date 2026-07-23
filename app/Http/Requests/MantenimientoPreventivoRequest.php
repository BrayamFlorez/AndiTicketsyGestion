<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MantenimientoPreventivoRequest extends FormRequest
{
    public function authorize(): bool
    {
        // La autorización real la haremos con Spatie en el Controlador
        return true; 
    }

    public function rules(): array
    {
        return [
            'fecha_programada'           => 'nullable|date',
            'fecha_inicio'               => 'nullable|date',
            'fecha_finalizacion'         => 'nullable|date|after_or_equal:fecha_inicio',
            'estado'                     => 'required|in:programado,en_progreso,completado,cancelado',
            
            // Checklists
            'hw_limpieza_interna'        => 'nullable|boolean',
            'hw_limpieza_externa'        => 'nullable|boolean',
            'hw_cambio_pasta_termica'    => 'nullable|boolean',
            'hw_revision_ventiladores'   => 'nullable|boolean',
            'hw_verificacion_cables'     => 'nullable|boolean',
            'sw_limpieza_temporales'     => 'nullable|boolean',
            'sw_actualizacion_so'        => 'nullable|boolean',
            'sw_revision_antivirus'      => 'nullable|boolean',
            'sw_revision_disco_duro'     => 'nullable|boolean',
            'sw_desinstalacion_basura'   => 'nullable|boolean',
            
            // Textos y evidencias
            'observaciones_tecnicas'     => 'nullable|string|max:1000',
            'recomendaciones_cliente'    => 'nullable|string|max:1000',
            'firma_cliente_path'         => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'evidencia_fotografica_path' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ];
    }
}