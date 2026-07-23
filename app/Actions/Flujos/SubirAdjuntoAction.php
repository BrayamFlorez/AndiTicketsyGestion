<?php

namespace App\Actions\Flujos;

use App\Models\User;
use App\Models\WorkflowAttachment;
use App\Models\WorkflowInstance;
use App\Models\WorkflowInstanceStep;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SubirAdjuntoAction
{
    /**
     * Guarda un archivo en el disco privado y crea el registro correspondiente.
     * Etiqueta el adjunto con la revisión activa de la instancia.
     */
    public function ejecutar(
        WorkflowInstance $instance,
        User $usuario,
        UploadedFile $archivo,
        ?WorkflowInstanceStep $step = null,
    ): WorkflowAttachment {

        $extension = strtolower($archivo->getClientOriginalExtension() ?: 'bin');
        $nombreEnDisco = Str::uuid()->toString().'.'.$extension;
        $carpeta = "flujos/{$instance->id}/rev-{$instance->revision}";

        $ruta = $archivo->storeAs($carpeta, $nombreEnDisco, 'private');

        return WorkflowAttachment::create([
            'workflow_instance_id'      => $instance->id,
            'workflow_instance_step_id' => $step?->id,
            'subido_por'                => $usuario->id,
            'revision'                  => $instance->revision,
            'nombre_original'           => $archivo->getClientOriginalName(),
            'ruta'                      => $ruta,
            'mime_type'                 => $archivo->getMimeType() ?: 'application/octet-stream',
            'tamano_bytes'              => $archivo->getSize() ?: 0,
        ]);
    }
}