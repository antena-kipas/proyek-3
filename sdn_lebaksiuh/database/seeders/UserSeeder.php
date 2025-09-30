<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            [
                'name' => 'Teti Sukaesih',
                'email' => 'tetisukesih@example.com',
                'password' => Hash::make('password'),
                'mapel' => null,
                'role' => 'guru',
                'kelas' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Eneng Siti Nurjanah',
                'email' => 'enengsitinurjanah@example.com',
                'password' => Hash::make('password'),
                'mapel' => null,
                'role' => 'guru',
                'kelas' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Carman',
                'email' => 'carman@example.com',
                'password' => Hash::make('password'),
                'mapel' => null,
                'role' => 'guru',
                'kelas' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Irfan Taufik Ginanjar',
                'email' => 'irfantaufikginanjar@example.com',
                'password' => Hash::make('password'),
                'mapel' => null,
                'role' => 'guru',
                'kelas' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Dewi Meliasari',
                'email' => 'dewimeliasari@example.com',
                'password' => Hash::make('password'),
                'mapel' => null,
                'role' => 'guru',
                'kelas' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Ating Herawati',
                'email' => 'atingherawati@example.com',
                'password' => Hash::make('password'),
                'mapel' => null,
                'role' => 'guru',
                'kelas' => 6,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Ela',
                'email' => 'ela@example.com',
                'password' => Hash::make('password'),
                'mapel' => 'Pendidikan Agama Islam',
                'role' => 'guru',
                'kelas' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Riki Irmawan Nasution',
                'email' => 'rikiirmawannasution@example.com',
                'password' => Hash::make('password'),
                'mapel' => 'Pendidikan olahraga dan jasmani',
                'role' => 'guru',
                'kelas' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Super User',
                'email' => 'superuser013@example.com',
                'password' => Hash::make('password'),
                'mapel' => null,
                'role' => 'super-user',
                'kelas' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
