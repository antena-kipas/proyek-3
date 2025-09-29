<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Create the Super User
        User::create([
            'name' => 'Super User',
            'email' => 'superuser@example.com',
            'password' => Hash::make('password'),
            'role' => 'super-user',
            'kelas' => null,
            'mapel' => null,
        ]);

        // 2. Create 6 Class Teachers (Wali Kelas)
        for ($i = 1; $i <= 6; $i++) {
            User::factory()->create([
                'name' => 'Guru Kelas ' . $i,
                'email' => 'guru.kelas.' . $i . '@example.com',
                'role' => 'guru',
                'kelas' => $i, // Use integer as per new schema
                'mapel' => null,
            ]);
        }

        // 3. Create Subject Teachers (Guru Mata Pelajaran)
        User::factory()->create([
            'name' => 'Guru Penjas',
            'email' => 'guru.penjas@example.com',
            'role' => 'guru',
            'kelas' => null, // Not a homeroom teacher
            'mapel' => 'Penjas', // Fill the new 'mapel' column
        ]);

        User::factory()->create([
            'name' => 'Guru Agama',
            'email' => 'guru.agama@example.com',
            'role' => 'guru',
            'kelas' => null, // Not a homeroom teacher
            'mapel' => 'Agama', // Fill the new 'mapel' column
        ]);
    }
}
