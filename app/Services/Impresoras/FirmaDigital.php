<?php

namespace App\Services\Impresoras;

use Illuminate\Support\Facades\Storage;

/**
 * Firmas dibujadas en pantalla (canvas → PNG en base64).
 * Valida que sea un PNG real y que tenga trazo; las guarda como archivo privado.
 */
class FirmaDigital
{
    private const PREFIJO = 'data:image/png;base64,';
    private const MAX_BYTES = 600 * 1024;
    private const MIN_PIXELES_CON_TRAZO = 150;

    /** null si es válida; si no, el motivo */
    public function error(?string $dataUri): ?string
    {
        $png = $this->decodificar($dataUri);

        if ($png === null) {
            return 'La firma no es válida. Vuelva a firmar.';
        }
        if (strlen($png) > self::MAX_BYTES) {
            return 'La firma es demasiado grande.';
        }
        if (! $this->tieneTrazo($png)) {
            return 'La firma está vacía.';
        }

        return null;
    }

    public function guardar(string $dataUri, string $ruta, string $disco = 'local'): string
    {
        Storage::disk($disco)->put($ruta, $this->decodificar($dataUri));

        return $ruta;
    }

    private function decodificar(?string $dataUri): ?string
    {
        if (! is_string($dataUri) || ! str_starts_with($dataUri, self::PREFIJO)) {
            return null;
        }

        $png = base64_decode(substr($dataUri, strlen(self::PREFIJO)), true);

        // Firma de archivo PNG
        return $png !== false && str_starts_with($png, "\x89PNG\r\n\x1a\n") ? $png : null;
    }

    /** Cuenta píxeles no transparentes en una muestra: un lienzo en blanco no es una firma */
    private function tieneTrazo(string $png): bool
    {
        if (! function_exists('imagecreatefromstring')) {
            return true;
        }

        $imagen = @imagecreatefromstring($png);
        if (! $imagen) {
            return false;
        }

        [$ancho, $alto] = [imagesx($imagen), imagesy($imagen)];
        $paso = max(1, (int) floor(min($ancho, $alto) / 150));
        $conTrazo = 0;

        for ($x = 0; $x < $ancho; $x += $paso) {
            for ($y = 0; $y < $alto; $y += $paso) {
                $alfa = (imagecolorat($imagen, $x, $y) >> 24) & 0x7F; // 127 = transparente
                if ($alfa < 100 && ++$conTrazo >= self::MIN_PIXELES_CON_TRAZO / max(1, $paso)) {
                    imagedestroy($imagen);

                    return true;
                }
            }
        }

        imagedestroy($imagen);

        return false;
    }
}
