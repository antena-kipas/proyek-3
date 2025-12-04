@php
use App\Models\Absensi;
use App\Models\Siswa;
use Carbon\Carbon;
@endphp

<x-filament-panels::page>
    <div class="fi-header-heading text-2xl font-bold tracking-tight text-gray-950 dark:text-white">
        <h2>{{ static::$title }}</h2>
    </div>

    @if($rekaps->isEmpty())
        <div class="fi-fo-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <div class="fi-fo-section-content p-6 text-center text-gray-500 dark:text-gray-400">
                Tidak ada rekap absensi ditemukan.
            </div>
        </div>
    @else
        <div class="flex flex-col gap-y-6">
            @foreach($rekaps as $rekap)
                @php
                    $startDate = Carbon::create($rekap->tahun, $rekap->bulan, 1);
                    $endDate = $startDate->copy()->endOfMonth();
                    $daysInMonth = $startDate->daysInMonth;
                    $students = Siswa::where('kelas_sekarang', $rekap->kelas_saat_ini)->orderBy('nama_lengkap')->get();
                    $absensiData = Absensi::where('kelas_saat_ini', $rekap->kelas_saat_ini)
                        ->whereBetween('tanggal', [$startDate, $endDate])
                        ->get()
                        ->groupBy('siswa_id');
                    
                    // Overall summary counters
                    $totalHadir = 0;
                    $totalIzin = 0;
                    $totalSakit = 0;
                    $totalAlfa = 0;
                @endphp
                <div x-data="{ open: false }" class="fi-fo-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
                    {{-- Header Section for Each Rekap --}}
                    <div class="flex items-center justify-between p-4">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-950 dark:text-white">
                                Kelas {{ $rekap->kelas_saat_ini }} - {{ $startDate->monthName }} {{ $rekap->tahun }}
                            </h3>
                        </div>
                        <div class="flex items-center gap-x-2">
                             <x-filament::button
                                x-on:click="open = !open"
                                color="gray"
                                size="sm"
                            >
                                <span x-show="!open">Lihat Rekap</span>
                                <span x-show="open">Tutup Rekap</span>
                            </x-filament::button>
                            <x-filament::button
                                wire:click="downloadRekapXlsx({{ $rekap->kelas_saat_ini }}, {{ $rekap->bulan }}, {{ $rekap->tahun }})"
                                color="success"
                                size="sm"
                            >
                                Unduh Excel
                            </x-filament::button>
                            <x-filament::button
                                wire:click="deleteRekap({{ $rekap->kelas_saat_ini }}, {{ $rekap->bulan }}, {{ $rekap->tahun }})"
                                color="danger"
                                size="sm"
                            >
                                Hapus
                            </x-filament::button>
                        </div>
                    </div>

                    {{-- Collapsible Detailed Table --}}
                    <div x-show="open" x-collapse class="p-4 border-t border-gray-200 dark:border-white/10">
                        <div class="overflow-x-auto">
                            <table class="fi-table w-full min-w-full table-auto divide-y divide-gray-200 text-start dark:divide-white/10">
                                <thead class="bg-gray-50 dark:bg-white/5">
                                    <tr>
                                        <th class="fi-table-header-cell px-2 py-2 text-xs font-semibold text-gray-950 dark:text-white">Nama Siswa</th>
                                        @for ($day = 1; $day <= $daysInMonth; $day++)
                                            <th class="fi-table-header-cell p-1 text-center text-xs font-semibold text-gray-950 dark:text-white">{{ $day }}</th>
                                        @endfor
                                        <th class="fi-table-header-cell p-1 text-center text-xs font-bold text-gray-950 dark:text-white bg-green-100 dark:bg-green-800">H</th>
                                        <th class="fi-table-header-cell p-1 text-center text-xs font-bold text-gray-950 dark:text-white bg-yellow-100 dark:bg-yellow-800">S</th>
                                        <th class="fi-table-header-cell p-1 text-center text-xs font-bold text-gray-950 dark:text-white bg-blue-100 dark:bg-blue-800">I</th>
                                        <th class="fi-table-header-cell p-1 text-center text-xs font-bold text-gray-950 dark:text-white bg-red-100 dark:bg-red-800">A</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 dark:divide-white/10">
                                    @foreach ($students as $student)
                                        @php
                                            $hadirCount = 0;
                                            $izinCount = 0;
                                            $sakitCount = 0;
                                            $alfaCount = 0;
                                            $studentAbsensi = $absensiData->get($student->id);
                                        @endphp
                                        <tr class="fi-table-row hover:bg-gray-50 dark:hover:bg-white/5">
                                            <td class="fi-table-cell px-2 py-1 text-xs text-gray-950 dark:text-white whitespace-nowrap">{{ $student->nama_lengkap }}</td>
                                            @for ($day = 1; $day <= $daysInMonth; $day++)
                                                @php
                                                    $currentDay = Carbon::create($rekap->tahun, $rekap->bulan, $day);
                                                    $status = '-'; // Default
                                                    if ($studentAbsensi) {
                                                        $absensiForDay = $studentAbsensi->first(fn($item) => Carbon::parse($item->tanggal)->isSameDay($currentDay));
                                                        if ($absensiForDay) {
                                                            $status = $absensiForDay->status;
                                                            switch ($status) {
                                                                case 'Hadir': $hadirCount++; break;
                                                                case 'Sakit': $sakitCount++; break;
                                                                case 'Izin': $izinCount++; break;
                                                                case 'Alfa': $alfaCount++; break;
                                                            }
                                                        }
                                                    }
                                                @endphp
                                                <td class="fi-table-cell p-1 text-center text-xs text-gray-950 dark:text-white">{{ $status }}</td>
                                            @endfor
                                            @php
                                                $totalHadir += $hadirCount;
                                                $totalIzin += $izinCount;
                                                $totalSakit += $sakitCount;
                                                $totalAlfa += $alfaCount;
                                            @endphp
                                            <td class="fi-table-cell p-1 text-center text-xs font-medium text-gray-950 dark:text-white bg-green-50 dark:bg-green-900">{{ $hadirCount }}</td>
                                            <td class="fi-table-cell p-1 text-center text-xs font-medium text-gray-950 dark:text-white bg-yellow-50 dark:bg-yellow-900">{{ $sakitCount }}</td>
                                            <td class="fi-table-cell p-1 text-center text-xs font-medium text-gray-950 dark:text-white bg-blue-50 dark:bg-blue-900">{{ $izinCount }}</td>
                                            <td class="fi-table-cell p-1 text-center text-xs font-medium text-gray-950 dark:text-white bg-red-50 dark:bg-red-900">{{ $alfaCount }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="bg-gray-100 dark:bg-white/10">
                                     <tr class="font-bold text-gray-950 dark:text-white">
                                         <td class="fi-table-cell px-2 py-2 text-sm whitespace-nowrap">Total Keseluruhan</td>
                                         <td class="fi-table-cell" colspan="{{ $daysInMonth }}"></td>
                                         <td class="fi-table-cell p-1 text-center text-sm bg-green-100 dark:bg-green-800">{{ $totalHadir }}</td>
                                         <td class="fi-table-cell p-1 text-center text-sm bg-yellow-100 dark:bg-yellow-800">{{ $totalSakit }}</td>
                                         <td class="fi-table-cell p-1 text-center text-sm bg-blue-100 dark:bg-blue-800">{{ $totalIzin }}</td>
                                         <td class="fi-table-cell p-1 text-center text-sm bg-red-100 dark:bg-red-800">{{ $totalAlfa }}</td>
                                     </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</x-filament-panels::page>
