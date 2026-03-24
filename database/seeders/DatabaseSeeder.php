<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Usuario Root — equivalente a root@local.es de SeedData.cs
        User::firstOrCreate(
            ['email' => 'root@local.es'],
            [
                'name'           => 'Root',
                'password'       => Hash::make('Root123!'),
                'role'           => 'Root',
                'is_active'      => true,
                'fecha_registro' => now(),
            ]
        );

        // Usuario normal de prueba
        User::firstOrCreate(
            ['email' => 'user@local.es'],
            [
                'name'           => 'User',
                'password'       => Hash::make('User123!'),
                'role'           => 'Usuario',
                'is_active'      => true,
                'fecha_registro' => now(),
            ]
        );
    }

}
