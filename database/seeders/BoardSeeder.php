<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Board;
use App\Models\User;

class BoardSeeder extends Seeder
{
    public function run(): void
    {
        // Obtener usuarios existentes
        $admin = User::where('email', 'admin@kodigo.com')->first();
        $testUser = User::where('email', 'test@example.com')->first();
        $maria = User::where('email', 'maria@example.com')->first();
        $carlos = User::where('email', 'carlos@example.com')->first();

        if (!$admin || !$testUser || !$maria || !$carlos) {
            $this->command->error('Usuarios no encontrados. Asegúrate de ejecutar UserSeeder primero.');
            return;
        }

        // Crear tableros
        $boards = [
            [
                'name' => 'Proyecto Kodigo Kanban',
                'description' => 'Sistema de gestión de tareas para el equipo de desarrollo',
                'is_public' => false,
                'user_id' => $admin->id,
            ],
            [
                'name' => 'Tablero Personal',
                'description' => 'Tareas personales y recordatorios',
                'is_public' => false,
                'user_id' => $testUser->id,
            ],
            [
                'name' => 'Sprint Actual',
                'description' => 'Tareas del sprint en curso',
                'is_public' => true,
                'user_id' => $maria->id,
            ],
            [
                'name' => 'Ideas y Mejoras',
                'description' => 'Espacio para brainstorming y nuevas ideas',
                'is_public' => true,
                'user_id' => $carlos->id,
            ],
            [
                'name' => 'Proyecto Frontend',
                'description' => 'Desarrollo de la interfaz de usuario',
                'is_public' => false,
                'user_id' => $admin->id,
            ],
        ];

        foreach ($boards as $boardData) {
            Board::firstOrCreate(
                [
                    'name' => $boardData['name'],
                    'user_id' => $boardData['user_id']
                ],
                $boardData
            );
        }

        $this->command->info('Tableros creados exitosamente.');
    }
}