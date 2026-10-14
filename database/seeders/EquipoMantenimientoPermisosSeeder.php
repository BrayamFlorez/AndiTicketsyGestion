<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

/**
 * Permisos del nuevo mantenimiento de equipos. Los roles que tenían los permisos del módulo
 * anterior (mpreventivo.*) reciben los nuevos equivalentes, así nadie pierde acceso.
 *
 *   php artisan db:seed --class=EquipoMantenimientoPermisosSeeder
 */
class EquipoMantenimientoPermisosSeeder extends Seeder
{
    /** permiso nuevo => permisos anteriores que lo otorgan */
    private const EQUIVALENCIAS = [
        'equipos.mantenimientos.ver'       => ['mpreventivo.ver', 'mpreventivo.crear', 'mpreventivo.editar', 'mpreventivo.eliminar'],
        'equipos.mantenimientos.gestionar' => ['mpreventivo.crear', 'mpreventivo.editar'],
        'equipos.mantenimientos.eliminar'  => ['mpreventivo.eliminar'],
    ];

    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        foreach (self::EQUIVALENCIAS as $nuevo => $anteriores) {
            Permission::findOrCreate($nuevo, 'web');

            Role::query()
                ->whereHas('permissions', fn ($q) => $q->whereIn('name', $anteriores))
                ->get()
                ->each(fn (Role $rol) => $rol->givePermissionTo($nuevo));
        }

        foreach (['admin', 'super-admin'] as $nombre) {
            Role::where('name', $nombre)->first()?->givePermissionTo(array_keys(self::EQUIVALENCIAS));
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
