<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesEmpresaSeeder extends Seeder
{
    /**
     * Catálogo canónico de permisos que este seeder puede asignar.
     * El seeder los crea con firstOrCreate para asegurar que existan.
     * Si un rol referencia un permiso fuera de esta lista, se aborta (protección contra typos).
     */
    private const CATALOGO_PERMISOS = [
        // ── Tickets ─────────────────────────────────────────────
        'tickets.crear',
        'tickets.ver-todos',
        'tickets.ver-asignados',
        'tickets.ver-propios',
        'tickets.asignar',
        'tickets.iniciar',
        'tickets.resolver',
        'tickets.cerrar',
        'tickets.cancelar',
        'tickets.eliminar',

        // ── Flujos (motor de aprobaciones) ──────────────────────
        'flujos.plantillas.gestionar',
        'flujos.tipos-documento.gestionar',
        'flujos.iniciar',
        'flujos.ver-todos',
        'flujos.ver-participando',
        'flujos.cancelar-cualquiera',
        'flujos.corregir-propio',
        'flujos.reabrir',
        'flujos.desistir-propio',
        'flujos.editar-cabecera',

        // ── Gestión operativa ───────────────────────────────────
        'empresas.gestionar',
        'sedes.gestionar',
        'equipos.ver',
        'equipos.crear',
        'equipos.editar',
        'equipos.eliminar',
        'equipos.gestionar',

        // ── Usuarios y sistema ──────────────────────────────────
        'usuarios.ver',
        'usuarios.crear',
        'usuarios.editar',
        'usuarios.eliminar',
        'roles.gestionar',
        'auditoria.ver',
    ];

    /**
     * Definición completa de roles. El seeder usa syncPermissions() —
     * cada rol termina con EXACTAMENTE los permisos listados, ni más ni menos.
     * Por eso incluyo los flujos.* correspondientes en cada rol,
     * para no perder las asignaciones del WorkflowPermissionsSeeder al re-ejecutar.
     */
    private const ROLES = [

        'gerente' => [
            'descripcion' => 'Dirección general — acceso amplio a operación, gestión y flujos.',
            'permisos' => [
                'tickets.crear', 'tickets.ver-todos', 'tickets.asignar',
                'tickets.iniciar', 'tickets.resolver', 'tickets.cerrar', 'tickets.cancelar',

                'flujos.plantillas.gestionar', 'flujos.tipos-documento.gestionar',
                'flujos.iniciar', 'flujos.ver-todos', 'flujos.cancelar-cualquiera',
                'flujos.corregir-propio', 'flujos.reabrir', 'flujos.desistir-propio',

                'empresas.gestionar', 'sedes.gestionar', 'equipos.gestionar',

                'usuarios.ver', 'usuarios.crear', 'usuarios.editar',
                'auditoria.ver',
            ],
        ],

        'lider-tecnico' => [
            'descripcion' => 'Coordina el equipo técnico — asigna tickets, gestiona equipos.',
            'permisos' => [
                'tickets.crear', 'tickets.ver-todos', 'tickets.asignar',
                'tickets.iniciar', 'tickets.resolver', 'tickets.cerrar',

                'equipos.gestionar',

                'flujos.iniciar', 'flujos.ver-participando',
                'flujos.corregir-propio', 'flujos.desistir-propio',

                'usuarios.ver',
            ],
        ],

        'tecnico' => [
            'descripcion' => 'Ejecutor de tickets asignados. Consulta inventario de equipos.',
            'permisos' => [
                'tickets.crear', 'tickets.ver-asignados',
                'tickets.iniciar', 'tickets.resolver',

                'equipos.ver',

                'flujos.iniciar', 'flujos.ver-participando',
                'flujos.corregir-propio', 'flujos.desistir-propio',
            ],
        ],

        'lider-ventas' => [
            'descripcion' => 'Coordina el equipo comercial — supervisa cotizaciones y ventas.',
            'permisos' => [
                'tickets.crear', 'tickets.ver-todos',

                'flujos.iniciar', 'flujos.ver-todos',
                'flujos.corregir-propio', 'flujos.desistir-propio',

                'usuarios.ver',
            ],
        ],

        'asesor-ventas' => [
            'descripcion' => 'Ejecutor comercial — inicia cotizaciones y trámites de venta.',
            'permisos' => [
                'tickets.crear', 'tickets.ver-propios',

                'flujos.iniciar', 'flujos.ver-participando',
                'flujos.corregir-propio', 'flujos.desistir-propio',
            ],
        ],

        'auxiliar-bodega' => [
            'descripcion' => 'Manejo de inventario y despacho.',
            'permisos' => [
                'tickets.crear', 'tickets.ver-propios',
                'equipos.ver',
                'flujos.iniciar', 'flujos.ver-participando',
                'flujos.corregir-propio', 'flujos.desistir-propio',
            ],
        ],

        'auxiliar-administrativo' => [
            'descripcion' => 'Soporte administrativo transversal.',
            'permisos' => [
                'tickets.crear', 'tickets.ver-propios',
                'flujos.iniciar', 'flujos.ver-participando',
                'flujos.corregir-propio', 'flujos.desistir-propio',
            ],
        ],

        'talento-humano' => [
            'descripcion' => 'Gestión de personal — consulta usuarios del sistema.',
            'permisos' => [
                'tickets.crear', 'tickets.ver-propios',
                'usuarios.ver',
                'flujos.iniciar', 'flujos.ver-participando',
                'flujos.corregir-propio', 'flujos.desistir-propio',
            ],
        ],

        'auxiliar-cartera' => [
            'descripcion' => 'Manejo de cartera y cobros.',
            'permisos' => [
                'tickets.crear', 'tickets.ver-propios',
                'flujos.iniciar', 'flujos.ver-participando',
                'flujos.corregir-propio', 'flujos.desistir-propio',
            ],
        ],

        'auxiliar-contabilidad' => [
            'descripcion' => 'Contabilidad — soporte operativo del área financiera.',
            'permisos' => [
                'tickets.crear', 'tickets.ver-propios',
                'flujos.iniciar', 'flujos.ver-participando',
                'flujos.corregir-propio', 'flujos.desistir-propio',
            ],
        ],

        'recepcion' => [
            'descripcion' => 'Recepción — atención al cliente y trámites básicos.',
            'permisos' => [
                'tickets.crear', 'tickets.ver-propios',
                'flujos.iniciar', 'flujos.ver-participando',
                'flujos.corregir-propio', 'flujos.desistir-propio',
            ],
        ],
    ];

    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $this->command->info('▸ Sembrando catálogo de permisos...');
        foreach (self::CATALOGO_PERMISOS as $permiso) {
            Permission::firstOrCreate([
                'name'       => $permiso,
                'guard_name' => 'web',
            ]);
        }

        $this->command->info('▸ Sembrando roles y asignaciones...');
        foreach (self::ROLES as $nombre => $definicion) {

            // Protección contra typos: cualquier permiso fuera del catálogo aborta ese rol
            $desconocidos = array_diff($definicion['permisos'], self::CATALOGO_PERMISOS);
            if (! empty($desconocidos)) {
                $this->command->error(
                    "  ✗ Rol '{$nombre}' referencia permisos fuera del catálogo: "
                    . implode(', ', $desconocidos)
                );
                continue;
            }

            $rol = Role::firstOrCreate([
                'name'       => $nombre,
                'guard_name' => 'web',
            ]);

            $rol->syncPermissions($definicion['permisos']);
            $rol->fresh();

            $this->command->line("  ▸ {$nombre} ({$rol->permissions()->count()} permisos)");
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $this->command->info('');
        $this->command->info('✔  Seeder completado.');
        $this->command->line('   Roles preservados sin tocar: super-admin, admin, usuario');
        $this->command->line('   Asignación en tinker: $user->assignRole(\'gerente\');');
    }
}