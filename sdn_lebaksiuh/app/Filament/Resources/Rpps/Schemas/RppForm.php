<?php

namespace App\Filament\Resources\Rpps\Schemas;

use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Actions;
use Filament\Actions\Action;
use Filament\Schemas\Schema;

class RppForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                Select::make('semester')
                    ->options([
                        1 => 'Semester 1',
                        2 => 'Semester 2',
                    ])
                    ->required(),

                TextInput::make('pembelajaran_ke')
                    ->label('Pembelajaran Ke')
                    ->numeric()
                    ->required(),

                TextInput::make('tema_id')
                    ->label('Tema ID')
                    ->numeric()
                    ->required(),

                TextInput::make('tema_name')
                    ->label('Nama Tema')
                    ->required(),

                TextInput::make('sub_tema_id')
                    ->label('Sub Tema ID')
                    ->numeric()
                    ->required(),

                TextInput::make('sub_tema_name')
                    ->label('Nama Sub Tema')
                    ->required(),
                
                Repeater::make('muatan_terpadus')
                    ->relationship()
                    ->schema([
                        TextInput::make('mata_pelajaran')
                            ->required(),
                    ])
                    ->label('Muatan Terpadu')
                    ->columns(1)
                    ->columnSpanFull(),

                Actions::make([
                    Action::make('generate_kegiatan_inti')
                        ->label('Generate Kegiatan Inti dengan AI')
                        ->color('primary')
                        ->action(function () {
                            // Logika generasi AI akan ditambahkan di sini nanti
                            // Untuk saat ini, ini hanya placeholder
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
                            ->required()
                            ->disabled()
                            ->dehydrated(false),
                        Textarea::make('konten')
                            ->label('Konten Kegiatan')
                            ->required()
                            ->disabled()
                            ->columnSpanFull(),
                        TextInput::make('urutan')
                            ->numeric()
                            ->default(0)
                            ->required()
                            ->disabled(),
                    ])
                    ->label('Kegiatan Inti (Hasil AI)')
                    ->columns(3)
                    ->columnSpanFull(),
            ]);
    }
}