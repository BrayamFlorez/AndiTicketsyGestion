<?php

namespace App\Http\Controllers;

use App\Models\Proyecto;
use App\Models\ProyectoAdjunto;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * Sirve las evidencias desde el disco privado.
 * BinaryFileResponse soporta rangos, así que audio y video se pueden adelantar.
 */
class ProyectoAdjuntoController extends Controller
{
    public function show(Request $request, Proyecto $proyecto, ProyectoAdjunto $adjunto): BinaryFileResponse
    {
        abort_unless($adjunto->proyecto_id === $proyecto->id, 404);
        Gate::authorize('view', $proyecto);

        $disco = Storage::disk(ProyectoAdjunto::DISCO);
        abort_unless($disco->exists($adjunto->ruta), 404, 'El archivo ya no existe en el servidor.');

        $ruta = $disco->path($adjunto->ruta);

        if ($request->boolean('descargar')) {
            return response()->download($ruta, str_replace(['/', '\\', '%'], '_', $adjunto->nombre_original));
        }

        $respuesta = response()->file($ruta, [
            'Content-Type'           => $adjunto->mime ?: 'application/octet-stream',
            'X-Content-Type-Options' => 'nosniff',
            'Cache-Control'          => 'private, max-age=86400',
        ]);
        $respuesta->setContentDisposition(
            'inline',
            str_replace(['/', '\\', '%'], '_', $adjunto->nombre_original),
            $this->nombreAscii($adjunto->nombre_original),
        );

        return $respuesta;
    }

    public function destroy(Proyecto $proyecto, ProyectoAdjunto $adjunto): RedirectResponse
    {
        abort_unless($adjunto->proyecto_id === $proyecto->id, 404);
        Gate::authorize('modificarRegistro', [$proyecto, $adjunto]);

        $nombre = $adjunto->nombre_original;
        $adjunto->delete(); // El modelo borra también el archivo físico

        return redirect()
            ->route('proyectos.show', $proyecto)
            ->with('exito', "Adjunto \"{$nombre}\" eliminado.");
    }

    /** Content-Disposition exige un nombre ASCII de respaldo */
    private function nombreAscii(string $nombre): string
    {
        $ascii = preg_replace('/[^\x20-\x7E]/', '_', $nombre);

        return str_replace(['"', '/', '\\', '%'], '_', $ascii) ?: 'archivo';
    }
}
