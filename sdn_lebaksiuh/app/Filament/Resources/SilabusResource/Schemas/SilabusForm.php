<?php

namespace App\Filament\Resources\SilabusResource\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;

class SilabusForm
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

                TextInput::make('id_tema')
                    ->label('Tema ke berapa ?')
                    ->numeric()
                    ->required(),

                TextInput::make('id_subtema')
                    ->label('Subtema ke berapa ?')
                    ->numeric()
                    ->required(),

                TextInput::make('tema')
                    ->label('Tema')
                    ->required(),

                TextInput::make('sub_tema')
                    ->label('Sub Tema')
                    ->required(),

                \Filament\Forms\Components\Repeater::make('kompetensiIntis')
                    ->relationship()
                    ->schema([
                        \Filament\Forms\Components\TextInput::make('urutan')->numeric()->required(),
                        \Filament\Forms\Components\Textarea::make('kompetensi_inti')->required(),
                    ])
                    ->label('Kompetensi Inti')
                    ->addActionLabel('Tambah Kompetensi Inti')
                    ->columns(2)
                    ->columnSpanFull(),

                \Filament\Forms\Components\Select::make('mata_pelajaran_id')
                    ->relationship('mataPelajaran', 'nama_pelajaran')
                    ->label('Mata Pelajaran')
                    ->required()
                    ->columnSpanFull(),

                \Filament\Forms\Components\Repeater::make('kompetensiDasars')
                    ->relationship()
                    ->schema([
                        \Filament\Forms\Components\Textarea::make('deskripsi_kd')
                            ->label('Deskripsi Kompetensi Dasar')
                            ->required(),
                    ])
                    ->label('Kompetensi Dasar')
                    ->addActionLabel('Tambah Kompetensi Dasar')
                    ->columns(1)
                    ->columnSpanFull(),

                \Filament\Forms\Components\Repeater::make('indikators')
                    ->relationship()
                    ->schema([
                        \Filament\Forms\Components\Textarea::make('deskripsi_indikator')
                            ->label('Deskripsi Indikator')
                            ->required(),
                    ])
                    ->label('Indikator')
                    ->addActionLabel('Tambah Indikator')
                    ->columns(1)
                    ->columnSpanFull(),
            ])->columns(2);
    }
}
