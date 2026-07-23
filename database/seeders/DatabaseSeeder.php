<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seeders que se ejecutan en TODOS los entornos (producción incluida).
     * Solo datos estructurales que el sistema necesita para funcionar.
     */
    private array $coreSeeds = [
        RolesAndPermissionsSeeder::class,
    ];

    /**
     * Seeders que se ejecutan SOLO en desarrollo/staging.
     * Datos de prueba — nunca en producción.
     */
    private array $devSeeds = [
        UserSeeder::class,
    ];

    public function run(): void
    {
        // Seeders de estructura — siempre
        $this->call($this->coreSeeds);

        // Seeders de datos demo — solo si no es producción
        if (! app()->isProduction()) {
            $this->call($this->devSeeds);
        }
    }
}