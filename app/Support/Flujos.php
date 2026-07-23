<?php

namespace App\Support;

class Flujos
{
    /**
     * Devuelve la clase FontAwesome apropiada para un mime type de adjunto.
     */
    public static function iconoAdjunto(string $mime): string
    {
        return match (true) {
            str_contains($mime, 'pdf')         => 'fa-file-pdf',
            str_contains($mime, 'word')        => 'fa-file-word',
            str_contains($mime, 'excel'),
            str_contains($mime, 'spreadsheet') => 'fa-file-excel',
            str_contains($mime, 'image')       => 'fa-file-image',
            str_contains($mime, 'zip')         => 'fa-file-archive',
            str_contains($mime, 'text')        => 'fa-file-alt',
            default                            => 'fa-file',
        };
    }
}