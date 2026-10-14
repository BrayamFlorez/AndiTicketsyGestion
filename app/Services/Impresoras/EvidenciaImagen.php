<?php

namespace App\Services\Impresoras;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Guarda fotos de evidencia: corrige la orientación de las fotos del celular y las reduce
 * a 1600 px como máximo (JPEG), para que el PDF no pese decenas de megas.
 * Si el servidor no tiene GD, guarda el archivo original.
 */
class EvidenciaImagen
{
    private const LADO_MAXIMO = 1600;
    private const CALIDAD = 82;

    public function guardar(UploadedFile $archivo, string $carpeta, string $disco = 'local'): string
    {
        $ruta = trim($carpeta, '/') . '/' . Str::uuid() . '.jpg';
        $jpeg = $this->procesar($archivo);

        if ($jpeg === null) {
            return $archivo->store($carpeta, $disco);
        }

        Storage::disk($disco)->put($ruta, $jpeg);

        return $ruta;
    }

    private function procesar(UploadedFile $archivo): ?string
    {
        if (! function_exists('imagecreatefromstring')) {
            return null;
        }

        $imagen = @imagecreatefromstring((string) file_get_contents($archivo->getRealPath()));
        if (! $imagen) {
            return null;
        }

        $imagen = $this->orientar($imagen, $archivo->getRealPath());

        [$ancho, $alto] = [imagesx($imagen), imagesy($imagen)];
        $escala = min(1, self::LADO_MAXIMO / max($ancho, $alto));

        if ($escala < 1) {
            $reducida = imagescale($imagen, (int) round($ancho * $escala), (int) round($alto * $escala), IMG_BICUBIC);
            imagedestroy($imagen);
            $imagen = $reducida;
        }

        // Fondo blanco para PNG/WEBP con transparencia
        $lienzo = imagecreatetruecolor(imagesx($imagen), imagesy($imagen));
        imagefill($lienzo, 0, 0, imagecolorallocate($lienzo, 255, 255, 255));
        imagecopy($lienzo, $imagen, 0, 0, 0, 0, imagesx($imagen), imagesy($imagen));
        imagedestroy($imagen);

        ob_start();
        imagejpeg($lienzo, null, self::CALIDAD);
        imagedestroy($lienzo);

        return ob_get_clean() ?: null;
    }

    /** Las cámaras guardan la foto "acostada" con una marca EXIF de rotación */
    private function orientar(\GdImage $imagen, string $ruta): \GdImage
    {
        if (! function_exists('exif_read_data')) {
            return $imagen;
        }

        $orientacion = @exif_read_data($ruta)['Orientation'] ?? 1;
        $grados = [3 => 180, 6 => -90, 8 => 90][$orientacion] ?? 0;

        if ($grados === 0) {
            return $imagen;
        }

        $rotada = imagerotate($imagen, $grados, 0);
        imagedestroy($imagen);

        return $rotada;
    }
}
