<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Ejecutar seeders en orden de dependencias
        $this->call([
            RoleSeeder::class,        // Crear roles básicos
            UserSeeder::class,        // Crear usuarios con roles asignados
            BoardSeeder::class,       // Crear tableros
            BoardListSeeder::class,   // Crear listas en tableros
            LabelSeeder::class,       // Crear etiquetas de prioridad en tableros
            CardSeeder::class,        // Crear tarjetas en listas con prioridad asignada
            CommentSeeder::class,     // Crear comentarios en tarjetas
            BoardUserSeeder::class,   // Asignar colaboradores a tableros
        ]);
    }
}
