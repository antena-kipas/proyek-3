<?php

namespace App\Filament\Resources\AbsensiResource\Pages;

use App\Filament\Resources\AbsensiResource;
use App\Models\Absensi;
use App\Models\Siswa;
use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

class AbsenPerTanggal extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string $resource = AbsensiResource::class;
    protected static string $view = 'filament.resources.absensi-resource.pages.absen-per-tanggal';
    protected static ?string $title = 'Absensi Per Tanggal';

    // WADAH 1: Untuk data Form (berisi String tanggal)
    public ?array $data = []; 

    // WADAH 2: Untuk logika query (berisi Object Carbon)
    // Kita hapus type hint public sementara agar aman, tapi kita kelola isinya sebagai Carbon
    public $tanggalLogika; 

    public ?Collection $siswas;
    public array $statuses = [];
    public ?int $kelas;

    // Arahkan Form agar menyimpan state-nya ke variabel $data
    protected function getFormStatePath(): ?string 
    {
        return 'data';
    }

    public function mount(): void
    {
        $user = Auth::user();

        if ($user->role !== 'guru' || is_null($user->kelas)) {
            abort(403, 'Akses ditolak. Fitur ini hanya untuk wali kelas.');
        }

        $this->kelas = $user->kelas;
        
        $today = now();

        // Isi form (masuk ke $this->data['tanggal'])
        $this->form->fill([
            'tanggal' => $today->toDateString(), 
        ]);

        // Isi logika (masuk ke $this->tanggalLogika)
        $this->tanggalLogika = $today;

        $this->loadAbsensiData();
    }

    protected function getFormSchema(): array
    {
        return [
            DatePicker::make('tanggal') // Ini akan mapping ke $data['tanggal']
                ->label('Pilih Tanggal')
                ->maxDate(now())
                ->required()
                ->reactive()
                ->afterStateUpdated(function ($state) {
                    // Saat user ganti tanggal di form, kita update variabel logika
                    // $state di sini berupa string, kita ubah jadi Carbon
                    $this->tanggalLogika = $state ? Carbon::parse($state) : null;
                    
                    $this->loadAbsensiData();
                }),
        ];
    }

    public function loadAbsensiData(): void
    {
        if (!$this->tanggalLogika) {
            $this->siswas = new Collection();
            return;
        }

        $this->siswas = Siswa::where('kelas_sekarang', $this->kelas)
            ->orderBy('nama_lengkap')
            ->get();
        
        // Pastikan kita menggunakan tanggalLogika yang sudah berbentuk Carbon
        // Jika karena suatu hal dia bukan Carbon, kita parse dulu
        $tgl = $this->tanggalLogika instanceof Carbon ? $this->tanggalLogika : Carbon::parse($this->tanggalLogika);

        $absensiUntukTanggal = Absensi::where('tanggal', $tgl->toDateString())
            ->whereIn('siswa_id', $this->siswas->pluck('id'))
            ->get()
            ->keyBy('siswa_id');

        $this->statuses = [];
        foreach ($this->siswas as $siswa) {
            $this->statuses[$siswa->id] = $absensiUntukTanggal->get($siswa->id)?->status ?? 'Hadir';
        }
    }

    public function save()
    {
        if (!$this->tanggalLogika) {
            Notification::make()->title('Tanggal belum dipilih')->danger()->send();
            return;
        }

        // Pastikan Carbon lagi sebelum save
        $tgl = $this->tanggalLogika instanceof Carbon ? $this->tanggalLogika : Carbon::parse($this->tanggalLogika);

        foreach ($this->statuses as $siswaId => $status) {
            Absensi::updateOrCreate(
                [
                    'siswa_id' => $siswaId,
                    'tanggal' => $tgl->toDateString(),
                ],
                [
                    'status' => $status,
                    'kelas_saat_ini' => $this->kelas,
                    'user_id' => Auth::id(),
                ]
            );
        }

        Notification::make()
            ->title('Absensi untuk ' . $tgl->isoFormat('D MMMM YYYY') . ' disimpan.')
            ->success()
            ->send();

        return $this->redirect(AbsensiResource::getUrl('index'));
    }
}