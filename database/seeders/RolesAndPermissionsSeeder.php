<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    private array $permissions = [

        'tickets' => [
            'tickets.ver-todos',
            'tickets.ver-asignados',
            'tickets.ver-propios',
            'tickets.crear',
            'tickets.editar',
            'tickets.asignar',
            'tickets.iniciar',
            'tickets.resolver',
            'tickets.cerrar',
            'tickets.cancelar',
            'tickets.eliminar',
        ],

        'usuarios' => [
            'usuarios.ver',
            'usuarios.crear',
            'usuarios.editar',
            'usuarios.eliminar',
            'usuarios.asignar-empresa',
        ],

        'infraestructura' => [
            'empresas.gestionar',
            'sedes.gestionar',
            'equipos.ver',
            'equipos.crear',
            'equipos.editar',
            'equipos.eliminar',
        ],

        'sistema' => [
            'auditoria.ver',
            'roles.gestionar',
        ],
    ];

    private array $rolePermissions = [

        'super-admin' => '*',

        'admin' => [
            'tickets.ver-todos',
            'tickets.ver-asignados',
            'tickets.ver-propios',
            'tickets.crear',
            'tickets.editar',
            'tickets.asignar',
            'tickets.iniciar',
            'tickets.resolver',
            'tickets.cerrar',
            'tickets.cancelar',
            'usuarios.ver',
            'usuarios.crear',
            'usuarios.editar',
            'usuarios.asignar-empresa',
            'empresas.gestionar',
            'sedes.gestionar',
            'equipos.ver',
            'equipos.crear',
            'equipos.editar',
        ],

        'tecnico' => [
            'tickets.ver-asignados',
            'tickets.iniciar',
            'tickets.resolver',
            'equipos.ver',
            'equipos.crear',
            'equipos.editar',
        ],

        'usuario' => [
            'tickets.ver-propios',
            'tickets.crear',
            'tickets.editar',
            'tickets.cancelar',
        ],
    ];

    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $allPermissions = collect($this->permissions)->flatten();

        foreach ($allPermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $this->command->info('Permisos creados: ' . $allPermissions->count());

        foreach ($this->rolePermissions as $roleName => $perms) {
            $role = Role::firstOrCreate(['name' => $roleName]);

            if ($perms === '*') {
                $role->syncPermissions(Permission::all());
            } else {
                $role->syncPermissions($perms);
            }

            $count = $perms === '*' ? Permission::count() : count($perms);
            $this->command->info("Rol [{$roleName}] -> {$count} permisos asignados");
        }

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $this->command->info('Roles y permisos configurados correctamente.');
    }
}
