<?php

namespace App\Filament\Resources\AbsensiResource\Pages;

use App\Filament\Resources\AbsensiResource;
use App\Models\Absensi;
use App\Models\Siswa;
use Carbon\Carbon;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Collection;

class AbsenHariIni extends Page
{
    protected static string $resource = AbsensiResource::class;

    protected static string $view = 'filament.resources.absensi-resource.pages.absen-hari-ini';

    protected static ?string $title = 'Absensi Hari Ini';

    public ?Collection $siswas;
    public array $statuses = [];
    public Carbon $tanggal;
    public ?int $kelas;

    public function mount(): void
    {
        $user = Auth::user();
        $this->tanggal = now();

        // Fitur ini hanya untuk wali kelas. Super-user bisa menggunakan create/edit biasa.
        // Juga, tolak akses jika hari ini hari Minggu.
        if (($user->role !== 'guru' || is_null($user->kelas)) || $this->tanggal->isSunday()) {
            // Meskipun tombol sudah di-disable, ini adalah pengaman tambahan.
            abort(403, 'Akses ditolak.');
        }

        $this->kelas = $user->kelas;
        $this->siswas = Siswa::where('kelas_sekarang', $this->kelas)->orderBy('nama_lengkap')->get();

        // Cek absensi yang sudah ada untuk hari ini
        $absensiHariIni = Absensi::where('tanggal', $this->tanggal->toDateString())
            ->whereIn('siswa_id', $this->siswas->pluck('id'))
            ->get()
            ->keyBy('siswa_id');

        // Inisialisasi status
        foreach ($this->siswas as $siswa) {
            $this->statuses[$siswa->id] = $absensiHariIni->get($siswa->id)?->status ?? 'Hadir';
        }
    }

    public function save()
    {
        foreach ($this->statuses as $siswaId => $status) {
            Absensi::updateOrCreate(
                [
                    'siswa_id' => $siswaId,
                    'tanggal' => $this->tanggal->toDateString(),
                ],
                [
                    'status' => $status,
                    'kelas_saat_ini' => $this->kelas,
                ]
            );
        }

        Notification::make()
            ->title('Absensi berhasil disimpan')
            ->success()
            ->send();

        return $this->redirect(AbsensiResource::getUrl('index'));
    }
}
