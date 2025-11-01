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
}
