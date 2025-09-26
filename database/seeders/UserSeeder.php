<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Obtener roles existentes
        $adminRole = Role::where('name', 'admin')->first();
        $userRole = Role::where('name', 'user')->first();

        if (!$adminRole || !$userRole) {
            $this->command->error('Roles no encontrados. Asegúrate de ejecutar RoleSeeder primero.');
            return;
        }

        // Crear usuario administrador
        $admin = User::firstOrCreate(
            ['email' => 'admin@kodigo.com'],
            [
                'name' => 'Administrador Kodigo',
                'password' => Hash::make('password'),
            ]
        );

        if (!$admin->hasRole('admin')) {
            $admin->roles()->attach($adminRole);
        }

        // Crear usuario de prueba
        $testUser = User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Usuario de Prueba',
                'password' => Hash::make('password'),
            ]
        );

        if (!$testUser->hasRole('user')) {
            $testUser->roles()->attach($userRole);
        }

        // Crear usuarios adicionales para pruebas
        $users = [
            [
                'name' => 'María González',
                'email' => 'maria@example.com',
                'password' => Hash::make('password'),
            ],
            [
                'name' => 'Carlos Rodríguez',
                'email' => 'carlos@example.com',
                'password' => Hash::make('password'),
            ],
            [
                'name' => 'Ana López',
                'email' => 'ana@example.com',
                'password' => Hash::make('password'),
            ],
            [
                'name' => 'Pedro Martínez',
                'email' => 'pedro@example.com',
                'password' => Hash::make('password'),
            ],
            [
                'name' => 'Laura Sánchez',
                'email' => 'laura@example.com',
                'password' => Hash::make('password'),
            ],
        ];

        foreach ($users as $userData) {
            $user = User::firstOrCreate(
                ['email' => $userData['email']],
                $userData
            );

            if (!$user->hasRole('user')) {
                $user->roles()->attach($userRole);
            }
        }

        $this->command->info('Usuarios creados exitosamente con roles asignados.');
    }
}