<?php

namespace App\Filament\Resources\SilabusResource\Schemas;

use App\Models\MataPelajaran;
use App\Services\GeminiAIService;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;

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
                            ->label('Judul Buku tematik!')
                            ->required(),

                        TextInput::make('subtema')
                            ->label('judul bab / nama bab')
                            ->required(),

                        \Filament\Forms\Components\Repeater::make('kompetensiIntis')
                            ->relationship()
                            ->schema([
                                \Filament\Forms\Components\Textarea::make('kompetensi_inti')->required(),
                            ])
                            ->label('Kompetensi Inti')
                            ->addActionLabel('Tambah Kompetensi Inti')
                            ->columns(2)
                            ->columnSpanFull(),

                        \Filament\Forms\Components\Select::make('mata_pelajaran_id')
                            ->label('Mata Pelajaran')
                            ->options(function () {
                                $user = auth()->user();

                                if ($user->role === 'super-user') {
                                    // Super-user sees all subjects
                                    return MataPelajaran::all()->pluck('nama_pelajaran', 'id');
                                }

                                if ($user->role === 'guru') {
                                    if ($user->mapel) {
                                        // Subject teacher sees only their subject
                                        return MataPelajaran::where('nama_pelajaran', $user->mapel)->pluck('nama_pelajaran', 'id');
                                    } else {
                                        // Homeroom teacher sees all subjects except PAI and PJOK
                                        return MataPelajaran::whereNotIn('nama_pelajaran', ['Pendidikan Agama Islam', 'PJOK'])
                                                            ->pluck('nama_pelajaran', 'id');
                                    }
                                }

                                // Default empty for any other case
                                return [];
                            })
                            ->live()
                            ->required()
                            ->columnSpanFull(),

                        \Filament\Forms\Components\Repeater::make('kompetensiDasars')
                            ->relationship()
                            ->schema([
                                \Filament\Forms\Components\Textarea::make('deskripsi_kd')
                                    ->label('Deskripsi Kompetensi Dasar')
                                    ->required(),
                                \Filament\Forms\Components\Hidden::make('mata_pelajaran_id')
                                    ->default(fn (Get $get) => $get('mata_pelajaran_id')),
                                
                            ])
                            ->label('Kompetensi Dasar')
                            ->addActionLabel('Tambah Kompetensi Dasar')
                            ->columns(1)
                            ->columnSpanFull()
                            ->mutateRelationshipDataBeforeCreateUsing(function (array $data, Get $get) {
                                $data['mata_pelajaran_id'] = $get('mata_pelajaran_id');
                                return $data;
                            })
                            ->mutateRelationshipDataBeforeSaveUsing(function (array $data, Get $get) { // Untuk update
                                $data['mata_pelajaran_id'] = $get('mata_pelajaran_id');
                                return $data;
                            }),






                        \Filament\Forms\Components\Repeater::make('indikators')
                            ->relationship()
                            ->schema([
                                \Filament\Forms\Components\Textarea::make('deskripsi_indikator')
                                    ->label('Deskripsi Indikator')
                                    ->required(),
                                \Filament\Forms\Components\Hidden::make('mata_pelajaran_id')
                                    ->default(fn (Get $get) => $get('mata_pelajaran_id')),
                                
                            ])
                            ->label('Indikator')
                            ->addActionLabel('Tambah Indikator')
                            ->columns(1)
                            ->columnSpanFull()
                            ->mutateRelationshipDataBeforeCreateUsing(function (array $data, Get $get) {
                                $data['mata_pelajaran_id'] = $get('mata_pelajaran_id');
                                return $data;
                            })
                            ->mutateRelationshipDataBeforeSaveUsing(function (array $data, Get $get) {
                                $data['mata_pelajaran_id'] = $get('mata_pelajaran_id');
                                return $data;
                            }),

                        Actions::make([
                            Action::make('generate_details_ai')
                                ->label('Generate Detail Silabus dengan AI')
                                ->button()
                                ->icon('heroicon-o-sparkles')
                                ->action(function (Get $get, Set $set, GeminiAIService $gemini) {
                                    // Set loading state for the new single-field structure
                                    $set('materiPelajaran', [['materi_pelajaran' => 'Memuat...']]);
                                    $set('kegiatanPembelajaran', [['kegiatan_pembelajaran' => 'Memuat...']]);
                                    $set('penilaianDiri', [['penilaian_diri' => 'Memuat...']]);

                                    $konteks = [
                                        'kelas' => $get('kelas'),
                                        'semester' => $get('semester'),
                                        'tema' => $get('tema'),
                                        'subtema' => $get('subtema'),
                                        'mata_pelajaran_id' => $get('mata_pelajaran_id'),
                                        'kompetensi_intis' => $get('kompetensiIntis'),
                                        'kompetensi_dasars' => $get('kompetensiDasars'),
                                        'indikators' => $get('indikators'),
                                    ];

                                    $result = $gemini->generateSilabusDetails($konteks);

                                    if ($result) {
                                        $set('materiPelajaran', $result['materi_pelajaran']);
                                        $set('kegiatanPembelajaran', $result['kegiatan_pembelajaran']);
                                        $set('penilaianDiri', $result['penilaian_diri']);
                                    } else {
                                        // Handle error, maybe show a notification
                                        $set('materiPelajaran', [['materi_pelajaran' => 'Gagal memuat']]);
                                        $set('kegiatanPembelajaran', [['kegiatan_pembelajaran' => 'Gagal memuat']]);
                                        $set('penilaianDiri', [['penilaian_diri' => 'Gagal memuat']]);
                                    }
                                }),
                        ])->columnSpanFull(),

                        \Filament\Forms\Components\Repeater::make('materiPelajaran')
                            ->relationship()
                            ->schema([
                                \Filament\Forms\Components\Textarea::make('materi_pelajaran')->required(),
                                \Filament\Forms\Components\Hidden::make('mata_pelajaran_id')
                                    ->default(fn (Get $get) => $get('mata_pelajaran_id')),

                            ])
                            ->label('Materi Pelajaran')
                            ->addActionLabel('Tambah Materi Pelajaran')
                            ->columns(1)
                            ->columnSpanFull()
                            ->mutateRelationshipDataBeforeCreateUsing(function (array $data, Get $get) {
                                $data['mata_pelajaran_id'] = $get('mata_pelajaran_id');
                                return $data;
                            })
                            ->mutateRelationshipDataBeforeSaveUsing(function (array $data, Get $get) {
                                $data['mata_pelajaran_id'] = $get('mata_pelajaran_id');
                                return $data;
                            }),

                        \Filament\Forms\Components\Repeater::make('kegiatanPembelajaran')
                            ->relationship()
                            ->schema([
                                \Filament\Forms\Components\Textarea::make('kegiatan_pembelajaran')->required(),
                                \Filament\Forms\Components\Hidden::make('mata_pelajaran_id')
                                    ->default(fn (Get $get) => $get('mata_pelajaran_id')),
                            ])
                            ->label('Kegiatan Pembelajaran')
                            ->addActionLabel('Tambah Kegiatan Pembelajaran')
                            ->columns(1)
                            ->columnSpanFull()
                            ->mutateRelationshipDataBeforeCreateUsing(function (array $data, Get $get) {
                                $data['mata_pelajaran_id'] = $get('mata_pelajaran_id');
                                return $data;
                            })
                            ->mutateRelationshipDataBeforeSaveUsing(function (array $data, Get $get) {
                                $data['mata_pelajaran_id'] = $get('mata_pelajaran_id');
                                return $data;
                            }),

                        \Filament\Forms\Components\Repeater::make('penilaianDiri')
                            ->relationship()
                            ->schema([
                                \Filament\Forms\Components\Textarea::make('penilaian_diri')->required(),
                                \Filament\Forms\Components\Hidden::make('mata_pelajaran_id')
                                    ->default(fn (Get $get) => $get('mata_pelajaran_id')),
                            ])
                            ->label('Penilaian Diri')
                            ->addActionLabel('Tambah Penilaian Diri')
                            ->columns(1)
                            ->columnSpanFull()
                            ->mutateRelationshipDataBeforeCreateUsing(function (array $data, Get $get) {
                                $data['mata_pelajaran_id'] = $get('mata_pelajaran_id');
                                return $data;
                            })
                            ->mutateRelationshipDataBeforeSaveUsing(function (array $data, Get $get) {
                                $data['mata_pelajaran_id'] = $get('mata_pelajaran_id');
                                return $data;
                            }),
                    ])->columns(2);
            }
        }
