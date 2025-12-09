<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Siswa;
use Illuminate\Support\Facades\DB;

class SiswaController extends Controller
{
    /**
     * Menampilkan daftar siswa yang dikelompokkan per kelas.
     */
    public function daftarPerKelas()
    {
        // Ambil semua siswa aktif, urutkan berdasarkan kelas lalu nama
        $siswas = Siswa::where('status_aktif', 'Y')
                                   ->orderBy('kelas_sekarang')
                                   ->orderBy(DB::raw('LOWER(nama_lengkap)'))
                                   ->get();

        // Kelompokkan siswa berdasarkan kelas
        $siswaPerKelas = $siswas->groupBy('kelas_sekarang');

        // Kirim data ke view
        return view('siswa.daftar-per-kelas', ['siswaPerKelas' => $siswaPerKelas]);
    }
}