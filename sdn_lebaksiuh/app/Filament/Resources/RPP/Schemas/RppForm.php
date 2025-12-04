<?php

namespace App\Filament\Resources\RPP\Schemas;

use App\Services\GeminiAIService;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Forms\Form;

class RppForm
{
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('kelas')
                    ->options([
                        1 => 'Kelas 1',
                        2 => 'Kelas 2',
                        3 => 'Kelas 3',
                        4 => 'Kelas 4',
                        5 => 'Kelas 5',
                        6 => 'Kelas 6',
                    ])
                    ->required()
                    ->default(fn () => auth()->user()->role === 'guru' ? auth()->user()->kelas : null)
                    ->disabled(fn () => auth()->user()->role === 'guru'),

                Select::make('semester')
                    ->options([
                        1 => 'Semester 1',
                        2 => 'Semester 2',
                    ])
                    ->required(),

                TextInput::make('pembelajaran_ke')
                    ->label('Pembelajaran Ke berapa?')
                    ->numeric()
                    ->required(),

                TextInput::make('tema_id')
                    ->label('Tema ke berapa ?')
                    ->numeric()
                    ->required(),

                TextInput::make('tema_name')
                    ->label('Nama Buku Tematik')
                    ->required(),

                TextInput::make('sub_tema_id')
                    ->label('Sub TEMA ke berapa ?')
                    ->numeric()
                    ->required(),

                TextInput::make('sub_tema_name')
                    ->label('Nama Sub Tema pada buku tematik ')
                    ->required(),

                Repeater::make('tujuanPembelajarans')
                    ->relationship()
                    ->schema([
                        TextInput::make('urutan')->numeric()->hidden(),
                        Textarea::make('tujuan_pembelajaran')->required(),
                    ])
                    ->label('Tujuan Pembelajaran')
                    ->addActionLabel('Tambah Tujuan Pembelajaran')
                    ->columns(1)
                    ->columnSpanFull(),

                Repeater::make('muatan_terpadus')
                    ->relationship()
                    ->schema([
                        TextInput::make('mata_pelajaran')
                            ->required(),
                    ])
                    ->label('Muatan Terpadu')
                    ->addActionLabel('Tambah Mata Pelajaran')
                    ->columns(1)
                    ->columnSpanFull(),

                Actions::make([
                    Action::make('generate_kegiatan_inti')
                        ->label('Generate Kegiatan Inti dengan AI')
                        ->color('primary')
                        ->action(function (Get $get, Set $set) {
                            $kelas = $get('kelas');
                            $tema = $get('tema_name');
                            $sub_tema = $get('sub_tema_name');
                            $tujuan_pembelajarans = $get('tujuanPembelajarans');

                            if (!$kelas || !$tema || !$sub_tema || empty($tujuan_pembelajarans)) {
                                Notification::make()
                                    ->title('Data tidak lengkap')
                                    ->body('Pastikan Kelas, Tema, Sub Tema, dan Tujuan Pembelajaran sudah diisi.')
                                    ->danger()
                                    ->send();
                                return;
                            }

                            $tujuan_string = collect($tujuan_pembelajarans)
                                ->values()
                                ->map(fn ($item, $key) => ($key + 1) . '. ' . $item['tujuan_pembelajaran'])
                                ->implode("\n");

                            try {
                                $geminiService = new GeminiAIService();
                                $generatedActivities = $geminiService->generateKegiatanInti($kelas, $tema, $sub_tema, $tujuan_string);

                                if (isset($generatedActivities['error'])) {
                                    Notification::make()
                                        ->title('Gagal menghasilkan Kegiatan Inti')
                                        ->body($generatedActivities['error'])
                                        ->danger()
                                        ->send();
                                    return;
                                }

                                $set('kegiatan_intis', $generatedActivities);

                                Notification::make()
                                    ->title('Kegiatan Inti berhasil dihasilkan')
                                    ->success()
                                    ->send();
                            } catch (Exception $e) {
                                Notification::make()
                                    ->title('Terjadi kesalahan')
                                    ->body($e->getMessage())
                                    ->danger()
                                    ->send();
                            }
                        })
                ])->columnSpanFull(),

                Repeater::make('kegiatan_intis')
                    ->relationship()
                    ->schema([
                        Select::make('kelompok')
                            ->options([
                                'ayo_mengamati' => 'Ayo Mengamati',
                                'ayo_berdiskusi' => 'Ayo Berdiskusi',
                                'ayo_membaca' => 'Ayo Membaca',
                                'ayo_berlatih' => 'Ayo Berlatih',
                                'ayo_renungkan' => 'Ayo Renungkan',
                            ])
                            ->required(),
                        Textarea::make('konten')
                            ->label('Konten Kegiatan')
                            ->required(),
                        TextInput::make('urutan')
                            ->numeric()
                            ->default(0)
                            ->required(),
                    ])
                    ->label('Kegiatan Inti (Hasil AI)')
                    ->columns(3)
                    ->columnSpanFull(),
            ])->columns(2);
    }
}