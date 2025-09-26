<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BoardList;
use App\Models\Card;
use App\Models\User;
use Carbon\Carbon;

class CardSeeder extends Seeder
{
    public function run(): void
    {
        // Obtener usuarios para asignar tarjetas
        $users = User::all();
        if ($users->isEmpty()) {
            $this->command->error('No se encontraron usuarios. Asegúrate de ejecutar UserSeeder primero.');
            return;
        }

        // Obtener todas las listas
        $lists = BoardList::with('board')->get();
        if ($lists->isEmpty()) {
            $this->command->error('No se encontraron listas. Asegúrate de ejecutar BoardListSeeder primero.');
            return;
        }

        // Tarjetas de ejemplo para cada tipo de lista
        $cardsByListType = [
            'Por Hacer' => [
                ['title' => 'Configurar entorno de desarrollo', 'description' => 'Instalar dependencias y configurar el proyecto localmente'],
                ['title' => 'Revisar documentación de la API', 'description' => 'Leer y entender los endpoints disponibles'],
                ['title' => 'Crear wireframes de la interfaz', 'description' => 'Diseñar la estructura visual de las pantallas'],
                ['title' => 'Implementar autenticación de usuarios', 'description' => 'Sistema de login y registro de usuarios'],
                ['title' => 'Configurar base de datos', 'description' => 'Crear tablas y relaciones necesarias'],
            ],
            'En Progreso' => [
                ['title' => 'Desarrollar componente de tablero', 'description' => 'Crear el componente principal para mostrar tableros Kanban'],
                ['title' => 'Implementar drag & drop', 'description' => 'Permitir mover tarjetas entre listas'],
                ['title' => 'Crear formulario de nueva tarjeta', 'description' => 'Interfaz para crear tarjetas con validación'],
                ['title' => 'Agregar sistema de etiquetas', 'description' => 'Permitir categorizar tarjetas con colores'],
            ],
            'En Revisión' => [
                ['title' => 'Revisar código de autenticación', 'description' => 'Code review del sistema de login'],
                ['title' => 'Testing de funcionalidades core', 'description' => 'Pruebas unitarias e integración'],
                ['title' => 'Validar responsive design', 'description' => 'Asegurar que funcione en móviles y tablets'],
            ],
            'Completado' => [
                ['title' => 'Configurar proyecto Laravel', 'description' => 'Proyecto base configurado y funcionando'],
                ['title' => 'Implementar JWT', 'description' => 'Sistema de tokens implementado correctamente'],
                ['title' => 'Crear modelos de datos', 'description' => 'Todos los modelos Eloquent creados'],
                ['title' => 'Documentar API', 'description' => 'Swagger generado con todos los endpoints'],
            ],
        ];

        foreach ($lists as $list) {
            $listType = $list->name;
            $cards = $cardsByListType[$listType] ?? [];

            foreach ($cards as $index => $cardData) {
                // Asignar usuario aleatorio
                $randomUser = $users->random();

                // Crear fecha de vencimiento aleatoria (algunas tarjetas la tendrán)
                $dueDate = rand(0, 1) ? Carbon::now()->addDays(rand(1, 30)) : null;

                Card::firstOrCreate(
                    [
                        'title' => $cardData['title'],
                        'board_list_id' => $list->id
                    ],
                    [
                        'description' => $cardData['description'],
                        'user_id' => $randomUser->id,
                        'position' => $index + 1,
                        'due_date' => $dueDate,
                        'is_completed' => $listType === 'Completado',
                        'is_archived' => false,
                    ]
                );
            }
        }

        $this->command->info('Tarjetas creadas exitosamente para todas las listas.');
    }
}