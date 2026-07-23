<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Crea usuarios de prueba para desarrollo.
 * NO ejecutar en producción directamente — usa DatabaseSeeder::$seedForProduction.
 */
class UserSeeder extends Seeder
{
    public function run(): void
    {
        $usuarios = [
            [
                'name'     => 'Super Administrador',
                'email'    => 'superadmin@andi.com',
                'password' => Hash::make('password'),
                'activo'   => true,
                'role'     => 'super-admin',
            ],
            [
                'name'     => 'Administrador',
                'email'    => 'admin@andi.com',
                'password' => Hash::make('password'),
                'activo'   => true,
                'role'     => 'admin',
            ],
            [
                'name'     => 'Técnico Demo',
                'email'    => 'tecnico@andi.com',
                'password' => Hash::make('password'),
                'activo'   => true,
                'role'     => 'tecnico',
            ],
            [
                'name'     => 'Usuario Final Demo',
                'email'    => 'usuario@andi.com',
                'password' => Hash::make('password'),
                'activo'   => true,
                'role'     => 'usuario',
            ],
        ];

        foreach ($usuarios as $data) {
            $role = $data['role'];
            unset($data['role']);

            $user = User::updateOrCreate(
                ['email' => $data['email']],
                $data,
            );

            // Asignar rol (syncRoles reemplaza roles existentes)
            $user->syncRoles([$role]);

            $this->command->info("✅ Usuario [{$user->email}] → rol [{$role}]");
        }
    }
}