<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    /**
     * Catálogo de ajustes del sistema.
     * Cada uno se crea con firstOrCreate — no pisa valores ya configurados por el admin.
     */
    private const AJUSTES = [
        [
            'clave'       => 'flujos.edicion_post_cierre',
            'valor'       => 'false',
            'tipo'        => 'bool',
            'categoria'   => 'flujos',
            'descripcion' => 'Permite a los actores de una etapa agregar adjuntos y comentarios adicionales después de que la etapa fue aprobada o rechazada. Los datos originales (firma, comentario del actor, adjuntos previos) siempre quedan inmutables.',
        ],
    ];

    public function run(): void
    {
        foreach (self::AJUSTES as $ajuste) {
            Setting::firstOrCreate(
                ['clave' => $ajuste['clave']],
                $ajuste,
            );
        }

        Setting::forgetCache();

        $this->command->info('✔  Ajustes del sistema sembrados.');
    }
}