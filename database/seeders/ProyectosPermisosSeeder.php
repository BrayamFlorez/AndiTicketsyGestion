<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

/**
 * Crea los permisos del módulo de Proyectos y los asigna a los roles base.
 * Es aditivo (givePermissionTo): no quita permisos existentes.
 *
 *   php artisan db:seed --class=ProyectosPermisosSeeder
 */
class ProyectosPermisosSeeder extends Seeder
{
    public const PERMISOS = [
        'proyectos.ver',        // Ver los proyectos donde es miembro del equipo
        'proyectos.ver-todos',  // Ver todos los proyectos
        'proyectos.crear',
        'proyectos.editar',     // Editar cabecera/equipo/estado y registros de otros
        'proyectos.eliminar',
        'proyectos.registrar',  // Registrar etapas y solicitudes
        'clientes.gestionar',
    ];

    /** Rol => permisos. Los roles que no existan se omiten. */
    private const ASIGNACION = [
        'super-admin' => self::PERMISOS,
        'admin'       => self::PERMISOS,
        'gerente'     => [
            'proyectos.ver', 'proyectos.ver-todos', 'proyectos.crear',
            'proyectos.editar', 'proyectos.registrar', 'clientes.gestionar',
        ],
    ];

    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        foreach (self::PERMISOS as $permiso) {
            Permission::findOrCreate($permiso, 'web');
        }

        foreach (self::ASIGNACION as $rolNombre => $permisos) {
            $rol = Role::where('name', $rolNombre)->where('guard_name', 'web')->first();
            $rol?->givePermissionTo($permisos);
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $this->command?->info('Permisos de proyectos creados y asignados.');
    }
}
