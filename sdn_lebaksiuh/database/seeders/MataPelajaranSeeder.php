<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MataPelajaranSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $mataPelajaran = [
            ['nama_pelajaran' => 'Bahasa Indonesia'],
            ['nama_pelajaran' => 'Bahasa Inggris'],
            ['nama_pelajaran' => 'Ilmu Pengetahuan Alam dan Sosial'],
            ['nama_pelajaran' => 'Matematika'],
            ['nama_pelajaran' => 'Pendidikan Pancasila'],
            ['nama_pelajaran' => 'PJOK'],
            ['nama_pelajaran' => 'Seni Budaya'],
            ['nama_pelajaran' => 'Pendidikan Agama Islam'],
        ];

        DB::table('mata_pelajarans')->insert($mataPelajaran);
    }
}