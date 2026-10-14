<?php

namespace App\Services\Proyectos;

use App\Models\Proyecto;
use App\Models\ProyectoAdjunto;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Guarda evidencias en el disco privado y crea sus registros.
 * Estructura: proyectos/{proyecto_id}/{YYYY-MM}/{uuid}.{ext}
 */
class AdjuntoService
{
    /**
     * @param  Model          $adjuntable  ProyectoEtapa | ProyectoSolicitud
     * @param  UploadedFile[] $archivos
     * @param  array          $rutasGuardadas  Se llena por referencia para limpiar si la transacción falla
     */
    public function guardar(Proyecto $proyecto, Model $adjuntable, array $archivos, User $user, array &$rutasGuardadas = []): void
    {
        foreach ($archivos as $archivo) {
            if (! $archivo instanceof UploadedFile || ! $archivo->isValid()) {
                continue;
            }

            $extension = strtolower($archivo->getClientOriginalExtension() ?: $archivo->guessExtension() ?: 'bin');
            $carpeta   = "proyectos/{$proyecto->id}/" . now()->format('Y-m');
            $nombre    = Str::uuid() . '.' . $extension;

            $ruta = $archivo->storeAs($carpeta, $nombre, ProyectoAdjunto::DISCO);
            $rutasGuardadas[] = $ruta;

            $adjuntable->adjuntos()->create([
                'proyecto_id'     => $proyecto->id,
                'tipo'            => ProyectoAdjunto::detectarTipo($archivo->getMimeType(), $extension),
                'nombre_original' => mb_substr($archivo->getClientOriginalName(), 0, 255),
                'ruta'            => $ruta,
                'mime'            => $archivo->getMimeType(),
                'tamano'          => $archivo->getSize(),
                'subido_por'      => $user->id,
            ]);
        }
    }

    /** Borra archivos físicos que quedaron huérfanos tras un rollback */
    public function limpiar(array $rutas): void
    {
        if ($rutas) {
            Storage::disk(ProyectoAdjunto::DISCO)->delete($rutas);
        }
    }
}
