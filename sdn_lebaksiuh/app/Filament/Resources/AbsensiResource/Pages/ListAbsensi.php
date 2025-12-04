<?php

namespace App\Filament\Resources\AbsensiResource\Pages;

use App\Filament\Resources\AbsensiResource;
use App\Filament\Resources\AbsensiResource\Widgets\AbsensiRecapWidget;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAbsensi extends ListRecords
{
    protected static string $resource = AbsensiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('absen-hari-ini')
                ->label('Absen Hari Ini')
                ->url(AbsensiResource::getUrl('absen-hari-ini'))
                ->icon('heroicon-o-calendar-days')
                ->disabled(fn() => now()->isSunday()),
            Actions\Action::make('absen-per-tanggal')
                ->label('Absensi Per Tanggal')
                ->url(AbsensiResource::getUrl('absen-per-tanggal'))
                ->icon('heroicon-o-calendar'),
            Actions\Action::make('rekap-bulanan')
                ->label('Rekap Absensi')
                ->url(AbsensiResource::getUrl('rekap-bulanan'))
                ->icon('heroicon-o-document-text'),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            AbsensiRecapWidget::class,
        ];
    }
}
