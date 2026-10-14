<?php

namespace App\Support\Proyectos;

/**
 * Avatares de iniciales con color estable por usuario (mismo usuario = mismo color).
 */
class Avatar
{
    private const COLORES = ['#007acc', '#a855f7', '#f97316', '#10b981', '#ec4899', '#06b6d4', '#f59e0b', '#8b5cf6', '#14b8a6', '#ef4444'];

    public static function color(int $id): string
    {
        return self::COLORES[$id % count(self::COLORES)];
    }

    public static function iniciales(string $nombre): string
    {
        $partes = preg_split('/\s+/', trim($nombre)) ?: [];

        return mb_strtoupper(
            collect($partes)->filter()->take(2)->map(fn ($p) => mb_substr($p, 0, 1))->join('')
        ) ?: '?';
    }
}
