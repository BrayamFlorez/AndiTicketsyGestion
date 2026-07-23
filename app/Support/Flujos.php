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

    /**
     * Devuelve las iniciales de un nombre (máximo 2 caracteres).
     * "María del Carmen Torres" → "MT" · "Ana" → "AN"
     */
    public static function iniciales(string $nombre): string
    {
        $words = preg_split('/\s+/', trim($nombre)) ?: [];
        $words = array_values(array_filter($words));

        if (count($words) >= 2) {
            return strtoupper(mb_substr($words[0], 0, 1) . mb_substr(end($words), 0, 1));
        }

        return strtoupper(mb_substr($words[0] ?? '', 0, 2));
    }
}