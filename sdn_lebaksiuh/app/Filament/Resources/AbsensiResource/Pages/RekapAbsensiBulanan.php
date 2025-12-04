<?php

namespace App\Filament\Resources\AbsensiResource\Pages;

use App\Filament\Resources\AbsensiResource;
use App\Models\Absensi;
use App\Models\Siswa;
use Carbon\Carbon;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Symfony\Component\HttpFoundation\StreamedResponse;





class RekapAbsensiBulanan extends Page


{


    protected static string $resource = AbsensiResource::class;





    protected static string $view = 'filament.resources.absensi-resource.pages.rekap-absensi-bulanan';





    protected static ?string $title = 'Rekap Absensi Bulanan';





    public ?Collection $rekaps;


    public ?int $kelas;





    public function mount(): void


    {


        $user = Auth::user();





        $query = Absensi::query();





        // Filter berdasarkan kelas jika user adalah guru (wali kelas)


        if ($user->role === 'guru' && !is_null($user->kelas)) {


            $this->kelas = $user->kelas;


            $query->where('kelas_saat_ini', $this->kelas);


        } elseif ($user->role === 'super-user') {


            $this->kelas = null; // Super-user can see all classes


        } else {


            // Should not happen as navigation is guarded, but as a safeguard


            abort(403, 'Akses ditolak.');


        }





        $this->rekaps = $query


            ->selectRaw('DISTINCT kelas_saat_ini, CAST(EXTRACT(YEAR FROM tanggal) AS INTEGER) as tahun, CAST(EXTRACT(MONTH FROM tanggal) AS INTEGER) as bulan')


            ->orderBy('kelas_saat_ini')


            ->orderBy('tahun', 'desc')


            ->orderBy('bulan', 'desc')


            ->get();


    }





    public function downloadRekapXlsx(int $kelas, int $bulan, int $tahun): StreamedResponse


    {


        try {


            $templatePath = storage_path('template/template_absensi.xlsx');


            if (!file_exists($templatePath)) {


                Notification::make()


                    ->title('Gagal mengunduh rekap absensi')


                    ->body('Template Excel tidak ditemukan.')


                    ->danger()


                    ->send();


                abort(404, 'Template Excel tidak ditemukan.');


            }





            $spreadsheet = IOFactory::load($templatePath);


            $sheet = $spreadsheet->getActiveSheet();





            // === 1. Fetch and Process Data ===


            $startDate = Carbon::createFromDate($tahun, $bulan, 1);


            $endDate = $startDate->copy()->endOfMonth();


            $daysInMonth = $startDate->daysInMonth;





                        // Get all students in the class





                        $students = Siswa::where('kelas_sekarang', $kelas)->orderBy('nama_lengkap')->get();





            // Get all attendance for the month for that class


            $absensiData = Absensi::where('kelas_saat_ini', $kelas)


                ->whereBetween('tanggal', [$startDate, $endDate])


                ->get()


                ->groupBy('siswa_id');


            


            // Overall summary counters


            $totalHadir = 0;


            $totalIzin = 0;


            $totalSakit = 0;


            $totalAlfa = 0;





            // === 2. Populate Spreadsheet ===





            // Populate dates


            $sheet->setCellValue('C34', $startDate->format('d F Y'));


            $sheet->setCellValue('C35', $endDate->format('d F Y'));





            $studentRow = 3; // Starting row for student names


            foreach ($students as $student) {


                $sheet->setCellValue('A' . $studentRow, $student->nama_lengkap);





                // Per-student summary counters


                $hadirCount = 0;


                $izinCount = 0;


                $sakitCount = 0;


                $alfaCount = 0;





                $studentAbsensi = $absensiData->get($student->id);





                for ($day = 1; $day <= 31; $day++) {


                    $col = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($day + 1); // +1 because col B is the 2nd col


                    


                    if ($day > $daysInMonth) {


                        // If the template has more days than the month, fill with '-'


                         $sheet->setCellValue($col . $studentRow, '-');


                         continue;


                    }





                    $currentDay = Carbon::createFromDate($tahun, $bulan, $day);


                    $status = '-'; // Default status





                    if ($studentAbsensi) {


                       $absensiForDay = $studentAbsensi->first(function ($item) use ($currentDay) {
                           return Carbon::parse($item->tanggal)->isSameDay($currentDay);
                       });


                       if ($absensiForDay) {


                           $status = $absensiForDay->status;


                           switch ($status) {


                               case 'Hadir':


                                   $hadirCount++;


                                   break;


                               case 'Izin':


                                   $izinCount++;


                                   break;


                               case 'Sakit':


                                   $sakitCount++;


                                   break;


                               case 'Alfa':


                                   $alfaCount++;


                                   break;


                           }


                       }


                    }


                    $sheet->setCellValue($col . $studentRow, $status);


                }





                // Populate per-student summary


                $sheet->setCellValue('AG' . $studentRow, $hadirCount);


                $sheet->setCellValue('AH' . $studentRow, $izinCount);


                $sheet->setCellValue('AI' . $studentRow, $sakitCount);


                $sheet->setCellValue('AJ' . $studentRow, $alfaCount);





                // Add to overall totals


                $totalHadir += $hadirCount;


                $totalIzin += $izinCount;


                $totalSakit += $sakitCount;


                $totalAlfa += $alfaCount;





                $studentRow++;


            }





            // Populate overall summary


            $sheet->setCellValue('AG34', $totalHadir);


            $sheet->setCellValue('AH34', $totalIzin);


            $sheet->setCellValue('AI34', $totalSakit);


            $sheet->setCellValue('AJ34', $totalAlfa);








            // === 3. Prepare and Send Response ===


            $monthName = $startDate->isoFormat('MMMM YYYY');


            $fileName = "Rekap Absensi Kelas {$kelas} - {$monthName}.xlsx";





            $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');


            $response = new StreamedResponse(function () use ($writer) {


                $writer->save('php://output');


            });





            $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');


            $response->headers->set('Content-Disposition', 'attachment;filename="' . $fileName . '"');


            $response->headers->set('Cache-Control', 'max-age=0');


            


            // This notification will be sent, but the page won't refresh because of the download.


            // It's a known behavior with file downloads in Livewire.


            Notification::make()


                ->title('Rekap absensi berhasil diunduh')


                ->success()


                ->send();





            return $response;





        } catch (\Throwable $e) {


            Notification::make()


                ->title('Gagal mengunduh rekap absensi')


                ->body('Terjadi kesalahan: ' . $e->getMessage())


                ->danger()


                ->send();


            


            throw $e;


        }


    }





    public function deleteRekap(int $kelas, int $bulan, int $tahun): void


    {


        $this->dispatchBrowserEvent('swal:confirm', [


            'title' => 'Hapus Rekap Absensi?',


            'text' => "Anda yakin ingin menghapus semua data absensi untuk Kelas {$kelas} bulan " . Carbon::createFromDate($tahun, $bulan, 1)->isoFormat('MMMM YYYY') . "? Tindakan ini tidak dapat dibatalkan!",


            'icon' => 'warning',


            'showCancelButton' => true,


            'confirmButtonColor' => '#d33',


            'cancelButtonColor' => '#3085d6',


            'confirmButtonText' => 'Ya, hapus!',


            'cancelButtonText' => 'Batal',


            'onConfirmed' => 'performDeleteRekap', // This will call a Livewire method


            'data' => ['kelas' => $kelas, 'bulan' => $bulan, 'tahun' => $tahun],


        ]);


    }





    public function performDeleteRekap(array $data): void


    {


        $kelas = $data['kelas'];


        $bulan = $data['bulan'];


        $tahun = $data['tahun'];





        Absensi::where('kelas_saat_ini', $kelas)


            ->whereYear('tanggal', $tahun)


            ->whereMonth('tanggal', $bulan)


            ->delete();





        Notification::make()


            ->title('Rekap absensi berhasil dihapus')


            ->success()


            ->send();


        


        $this->mount(); // Refresh the rekaps list


    }


}
