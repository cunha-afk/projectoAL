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
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        $admin = User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@local.com',
            'password' => bcrypt('123456'),
            'role' => 'admin'
        ]);

        $aloj = Alojamento::create([
            'titulo' => 'Casa da Serra',
            'descricao' => 'Alojamento com vista panorâmica...',
            'preco_noite' => 90.00,
        ]);
    }
}
