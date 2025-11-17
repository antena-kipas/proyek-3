<?php

namespace App\Services;

use Gemini;
use Gemini\Data\Content;
use Gemini\Enums\Role;

class GeminiAIService
{
    protected $client;

    public function __construct()
    {
        $apiKey = config('services.gemini.api_key');
        $this->client = Gemini::client($apiKey);
    }

    public function generateKegiatanInti(int $kelas, string $tema, string $sub_tema, string $tujuan_pembelajaran): array
    {
        $prompt = "PERINTAH TEGAS: Anda adalah ahli RPP untuk Sekolah Dasar di Indonesia.
        TUGAS UTAMA: Buat deskripsi Kegiatan Inti Pembelajaran berdasarkan data di bawah ini.
        DATA:
        - Kelas: {$kelas}
        - Tema: {$tema}
        - Sub Tema: {$sub_tema}
        - Tujuan Pembelajaran: {$tujuan_pembelajaran}

        ATURAN WAJIB:
        1.  Anda **WAJIB** dan **HARUS** membuat deskripsi untuk **SEMUA 5 (LIMA) KEGIATAN** berikut, tanpa terkecuali. Jangan pernah menghilangkan satu pun.
        2.  Kelima kegiatan yang **WAJIB** ada di respons Anda adalah:
            - **Ayo Mengamati**
            - **Ayo Berdiskusi**
            - **Ayo Membaca**
            - **Ayo Berlatih**
            - **Ayo Renungkan**
        3.  Sajikan output **HANYA** dalam format tabel markdown dengan dua kolom: | Kegiatan | Deskripsi |.
        4.  Jangan menambahkan teks pembuka, penutup, kesimpulan, atau apa pun di luar tabel markdown. Respons Anda harus dimulai dengan `| Kegiatan | Deskripsi |` dan diakhiri dengan baris terakhir dari tabel.
        5.  Pastikan nama kegiatan di kolom pertama persis seperti daftar di atas (termasuk `**`).";

        try {
            $response = $this->client->generativeModel('gemini-2.5-flash')->generateContent($prompt);
            $markdown = $response->text();
            return $this->parseMarkdownTable($markdown);
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    private function parseMarkdownTable(string $markdown): array
    {
        $lines = explode("\n", trim($markdown));
        $activities = [];
        $urutan = 1;

        $keyMap = [
            'Ayo Mengamati' => 'ayo_mengamati',
            'Ayo Berdiskusi' => 'ayo_berdiskusi',
            'Ayo Membaca' => 'ayo_membaca',
            'Ayo Berlatih' => 'ayo_berlatih',
            'Ayo Renungkan' => 'ayo_renungkan',
        ];

        foreach ($lines as $line) {
            if (!str_contains($line, '|')) {
                continue;
            }
            if (str_contains($line, '---')) {
                continue;
            }
            if (str_contains($line, 'Kegiatan') && str_contains($line, 'Deskripsi')) {
                continue;
            }

            $parts = array_map('trim', explode('|', $line));
            
            if (count($parts) >= 3 && !empty($parts[1])) {
                $kegiatanName = str_replace('**', '', $parts[1]);

                if (isset($keyMap[$kegiatanName])) {
                    $activities[] = [
                        'kelompok' => $keyMap[$kegiatanName],
                        'konten' => $parts[2] ?? '',
                        'urutan' => $urutan++,
                    ];
                }
            }
        }
        return $activities;
    }

    public function generateSilabusDetails(array $konteks): ?array
    {
        // Clean up context for a cleaner prompt
        $promptContext = [
            'kelas' => $konteks['kelas'],
            'semester' => $konteks['semester'],
            'tema' => $konteks['tema'],
            'sub_tema' => $konteks['sub_tema'],
            'mata_pelajaran' => \App\Models\MataPelajaran::find($konteks['mata_pelajaran_id'])->nama_pelajaran ?? 'N/A',
            'kompetensi_inti' => array_map(fn($ki) => $ki['kompetensi_inti'], $konteks['kompetensi_intis']),
            'kompetensi_dasar' => array_map(fn($kd) => $kd['deskripsi_kd'], $konteks['kompetensi_dasars']),
            'indikator' => array_map(fn($ind) => $ind['deskripsi_indikator'], $konteks['indikators']),
        ];

        $konteksJson = json_encode($promptContext, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        $prompt = <<<PROMPT
PERINTAH TEGAS: Anda adalah seorang ahli dalam kurikulum pendidikan dasar di Indonesia.
TUGAS UTAMA: Berdasarkan konteks silabus yang diberikan, buatkan detail rincian silabus.

KONTEKS SILABUS:
```json
$konteksJson
```

ATURAN WAJIB:
1.  Buatkan detail untuk 3 (TIGA) bagian berikut: `materi_pelajaran`, `kegiatan_pembelajaran`, dan `penilaian_diri`.
2.  Sajikan output **HANYA** dalam format JSON yang valid dan lengkap, sesuai dengan struktur di bawah ini. Jangan ada teks pembuka, penutup, atau penjelasan lain di luar JSON.
3.  Setiap bagian harus berupa array of strings.
4.  Untuk `materi_pelajaran`, buat minimal 2 item.
5.  Untuk `kegiatan_pembelajaran`, buat minimal 3 item.
6.  Untuk `penilaian_diri`, buat minimal 2 item (pertanyaan reflektif untuk siswa).

STRUKTUR JSON YANG WAJIB DIIKUTI:
{
  "materi_pelajaran": [
    "Isi lengkap materi pelajaran 1...",
    "Isi lengkap materi pelajaran 2..."
  ],
  "kegiatan_pembelajaran": [
    "Deskripsi lengkap kegiatan pembelajaran 1...",
    "Deskripsi lengkap kegiatan pembelajaran 2...",
    "Deskripsi lengkap kegiatan pembelajaran 3..."
  ],
  "penilaian_diri": [
    "Pertanyaan reflektif untuk penilaian diri siswa 1?",
    "Pertanyaan reflektif untuk penilaian diri siswa 2?"
  ]
}
PROMPT;

        try {
            $response = $this->client->generativeModel('gemini-2.5-flash')->generateContent($prompt);
            $jsonString = $this->extractJson($response->text());
            $decoded = json_decode($jsonString, true);

            if (!$decoded) {
                return null;
            }

            // Transform the data for Filament Repeaters
            $transformed = [];
            $keys = ['materi_pelajaran', 'kegiatan_pembelajaran', 'penilaian_diri'];

            foreach ($keys as $key) {
                if (isset($decoded[$key])) {
                    $transformed[$key] = array_map(function ($item) use ($key) {
                        return [$key => $item];
                    }, $decoded[$key]);
                }
            }

            return $transformed;

        } catch (\Exception $e) {
            // Log the error or handle it as needed
            \Illuminate\Support\Facades\Log::error('Gemini AI Service Error: ' . $e->getMessage());
            return null;
        }
    }

    private function extractJson(string $text): ?string
    {
        $start = strpos($text, '{');
        $end = strrpos($text, '}');
        if ($start === false || $end === false) {
            return null;
        }
        return substr($text, $start, $end - $start + 1);
    }
}
