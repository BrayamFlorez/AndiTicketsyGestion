<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * Sirve archivos almacenados en el disco privado (storage/app/private)
 * con autorización a nivel de aplicación.
 *
 * Reglas:
 *  - Solo usuarios autenticados (middleware 'auth' en la ruta).
 *  - El archivo debe estar registrado en la BD (foto_solicitud o
 *    evidencia_resolucion de algún ticket).
 *  - El usuario debe tener permiso para ver el ticket asociado.
 *  - Para evitar enumeración: si no puede verlo → 404, no 403.
 */
class ArchivoPrivadoController extends Controller
{
    public function show(string $path): BinaryFileResponse
    {
        // ── 1. Validación defensiva del path ──────────────────────────────
        // Bloquea path traversal y rutas absolutas.
        if (
            $path === ''
            || str_contains($path, '..')
            || str_starts_with($path, '/')
            || str_contains($path, "\0")
        ) {
            abort(404);
        }

        // ── 2. Busca el ticket dueño del archivo ──────────────────────────
        // Si el archivo no está registrado en ningún ticket, no existe
        // desde el punto de vista de la app, independiente del filesystem.
        $ticket = Ticket::query()
            ->where('foto_solicitud', $path)
            ->orWhere('evidencia_resolucion', $path)
            ->first();

        if (! $ticket) {
            abort(404);
        }

        // ── 3. Autorización: misma regla que TicketController::show ──────
        // Si no tiene permiso → 404 (no filtrar existencia del archivo).
        if (! $this->puedeVerTicket($ticket)) {
            abort(404);
        }

        // ── 4. Verifica que el archivo exista físicamente ─────────────────
        if (! Storage::disk('local')->exists($path)) {
            abort(404);
        }

        // ── 5. Endurecimiento: solo se sirven imágenes ───────────────────
        $fullPath = Storage::disk('local')->path($path);
        $mime = mime_content_type($fullPath) ?: 'application/octet-stream';

        if (! str_starts_with($mime, 'image/')) {
            abort(404);
        }

        // ── 6. Servir con cabeceras adecuadas ─────────────────────────────
        return response()->file($fullPath, [
            'Content-Type'  => $mime,
            'Cache-Control' => 'private, max-age=300',  // 5 min — privado al usuario
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    /**
     * Misma lógica de autorización que TicketController::autorizarVista.
     * Esto debería migrar a una Policy compartida cuando formalicemos el módulo.
     */
    private function puedeVerTicket(Ticket $ticket): bool
    {
        $user = Auth::user();

        return match (true) {
            $user->can('tickets.ver-todos')     => true,
            $user->can('tickets.ver-asignados') => $ticket->asignado_a === $user->id,
            $user->can('tickets.ver-propios')   => $ticket->solicitante_id === $user->id,
            default                             => false,
        };
    }
}
