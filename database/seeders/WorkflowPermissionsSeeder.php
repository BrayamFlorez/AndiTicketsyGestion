<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class WorkflowPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Limpiamos caché de Spatie antes y después para evitar mezclar estados
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permisos = [
            'flujos.plantillas.gestionar',      // CRUD de plantillas de flujo
            'flujos.tipos-documento.gestionar', // CRUD del catálogo de tipos de documento
            'flujos.iniciar',                   // Iniciar una nueva instancia
            'flujos.ver-todos',                 // Bandeja global
            'flujos.ver-participando',          // Solo donde inició o participa
            'flujos.cancelar-cualquiera',       // Cancelar flujos ajenos
            'flujos.corregir-propio',           // Reenviar tras corrección
            'flujos.reabrir',                   // Reabrir cancelados o intervenir manualmente
            'flujos.desistir-propio',           // El iniciador cierra su propio flujo
        ];

        foreach ($permisos as $nombre) {
            Permission::firstOrCreate([
                'name'       => $nombre,
                'guard_name' => 'web',
            ]);
        }

        // super-admin: acceso total
        $this->asignar('super-admin', $permisos);

        // admin: todo salvo lo que sea exclusivo del super (aquí les damos todo)
        $this->asignar('admin', $permisos);

        // tecnico: puede iniciar y participar como cualquier usuario
        $this->asignar('tecnico', [
            'flujos.iniciar',
            'flujos.ver-participando',
            'flujos.corregir-propio',
            'flujos.desistir-propio',
        ]);

        // usuario: mismo alcance que técnico en flujos
        $this->asignar('usuario', [
            'flujos.iniciar',
            'flujos.ver-participando',
            'flujos.corregir-propio',
            'flujos.desistir-propio',
        ]);

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    private function asignar(string $rol, array $permisos): void
    {
        $role = Role::where('name', $rol)->first();

        if (! $role) {
            $this->command->warn("Rol '{$rol}' no existe; se omite asignación.");
            return;
        }

        $role->givePermissionTo($permisos);
    }
}