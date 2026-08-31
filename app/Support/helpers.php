<?php

use App\Models\Setting;

if (! function_exists('settings')) {
    /**
     * Helper global para leer ajustes.
     * Ejemplo: settings('flujos.edicion_post_cierre', false)
     */
    function settings(string $clave, mixed $default = null): mixed
    {
        return Setting::get($clave, $default);
    }
}