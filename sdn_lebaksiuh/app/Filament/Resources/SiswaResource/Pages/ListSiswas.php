<?php

namespace App\Filament\Resources\SiswaResource\Pages;

use App\Filament\Resources\SiswaResource;
use App\Models\Siswa;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\DB;

class ListSiswas extends ListRecords
{
    protected static string $resource = SiswaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('prosesKenaikanKelas')
                ->label('Proses Kenaikan Kelas Tahunan')
                ->color('warning')
                ->icon('heroicon-o-server-stack')
                ->requiresConfirmation()
                ->modalHeading('Konfirmasi Kenaikan Kelas Massal')
                ->modalDescription('Apakah Anda yakin ingin memproses kenaikan kelas untuk SEMUA siswa aktif? Siswa kelas 6 akan diluluskan, siswa lainnya akan naik satu tingkat, dan kelas 1 akan dikosongkan. Aksi ini tidak dapat dibatalkan.')
                ->modalSubmitActionLabel('Ya, Lanjutkan')
                ->action(function () {
                    try {
                        DB::transaction(function () {
                            // Luluskan siswa kelas 6
                            Siswa::where('kelas_sekarang', 6)->where('status_aktif', 'Y')
                                ->update(['status_aktif' => 'N']);

                            // Naikkan kelas 1-5
                            for ($i = 5; $i >= 1; $i--) {
                                Siswa::where('kelas_sekarang', $i)->where('status_aktif', 'Y')
                                    ->update(['kelas_sekarang' => $i + 1]);
                            }
                        });

                        Notification::make()
                            ->title('Proses Kenaikan Kelas Berhasil')
                            ->body('Semua siswa aktif telah berhasil dinaikkan kelas.')
                            ->success()
                            ->send();

                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('Proses Kenaikan Kelas Gagal')
                            ->body('Terjadi kesalahan saat memproses data. ' . $e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),

            Action::make('cetakDaftarSiswa')
                ->label('Daftar Siswa per Kelas')
                ->icon('heroicon-o-printer')
                ->color('info')
                ->url(fn() => route('siswa.daftarPerKelas'))
                ->openUrlInNewTab(),
                
            Actions\CreateAction::make(),
        ];
    }
}
