<?php

namespace App\Filament\Resources\AbsensiResource\Widgets;

use App\Models\Absensi;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class AbsensiRecapWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $user = Auth::user();
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        $query = Absensi::whereBetween('tanggal', [$startOfMonth, $endOfMonth]);

        // Filter berdasarkan kelas jika user adalah guru (wali kelas)
        if ($user->role === 'guru' && !is_null($user->kelas)) {
            $query->where('kelas_saat_ini', $user->kelas);
        } elseif ($user->role !== 'super-user') {
            // Jika bukan super-user dan bukan wali kelas, tampilkan 0
            return [
                Stat::make('Hadir', 0),
                Stat::make('Sakit', 0),
                Stat::make('Izin', 0),
                Stat::make('Alpa', 0),
            ];
        }

        $attendanceCounts = $query
            ->selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');
        
        return [
            Stat::make('Hadir', $attendanceCounts->get('Hadir', 0)),
            Stat::make('Sakit', $attendanceCounts->get('Sakit', 0)),
            Stat::make('Izin', $attendanceCounts->get('Izin', 0)),
            Stat::make('Alfa', $attendanceCounts->get('Alfa', 0)),
        ];
    }
}
