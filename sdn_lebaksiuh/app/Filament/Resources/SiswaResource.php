<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SiswaResource\Pages;
use App\Models\Siswa;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class SiswaResource extends Resource
{
    protected static ?string $model = Siswa::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $navigationLabel = 'Siswa';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('nama_lengkap')
                    ->required()
                    ->maxLength(100),
                Select::make('kelas_sekarang')
                    ->options([
                        '1' => 'Kelas 1',
                        '2' => 'Kelas 2',
                        '3' => 'Kelas 3',
                        '4' => 'Kelas 4',
                        '5' => 'Kelas 5',
                        '6' => 'Kelas 6',
                    ])
                    ->required(),
                Toggle::make('status_aktif')
                    ->label('Status Aktif')
                    ->onColor('success')
                    ->offColor('danger')
                    ->default(true)
                    ->required()
                    ->afterStateHydrated(function (Toggle $component, ?string $state) {
                        $component->state($state === 'Y');
                    })
                    ->dehydrateStateUsing(fn ($state): string => $state ? 'Y' : 'N'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nama_lengkap')->searchable()->sortable(),
                TextColumn::make('kelas_sekarang')
                    ->formatStateUsing(fn (string $state): string => "Kelas {$state}")
                    ->sortable(),
                IconColumn::make('status_aktif')
                    ->label('Status Aktif')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-badge')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),
            ])
            ->filters([
                SelectFilter::make('kelas_sekarang')
                    ->options([
                        '1' => 'Kelas 1',
                        '2' => 'Kelas 2',
                        '3' => 'Kelas 3',
                        '4' => 'Kelas 4',
                        '5' => 'Kelas 5',
                        '6' => 'Kelas 6',
                    ]),
                SelectFilter::make('status_aktif')
                    ->options([
                        'Y' => 'Aktif',
                        'N' => 'Tidak Aktif',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                ActionGroup::make([
                    Action::make('setAktif')
                        ->label('Jadikan Aktif')
                        ->icon('heroicon-s-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(fn (Siswa $record) => $record->update(['status_aktif' => 'Y']))
                        ->hidden(fn (Siswa $record) => $record->status_aktif === 'Y')
                        ->after(fn () => Notification::make()->title('Status siswa berhasil diubah')->success()->send()),

                    Action::make('setTidakAktif')
                        ->label('Jadikan Tidak Aktif')
                        ->icon('heroicon-s-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->action(fn (Siswa $record) => $record->update(['status_aktif' => 'N']))
                        ->hidden(fn (Siswa $record) => $record->status_aktif === 'N')
                        ->after(fn () => Notification::make()->title('Status siswa berhasil diubah')->success()->send()),
                ])->label('Ubah Status'),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    BulkAction::make('naikKelas')
                        ->label('Naik Kelas')
                        ->requiresConfirmation()
                        ->icon('heroicon-o-arrow-up')
                        ->action(function (Collection $records) {
                            $records->each(function (Siswa $siswa) {
                                if ($siswa->kelas_sekarang < 6) {
                                    $siswa->increment('kelas_sekarang');
                                } else {
                                    $siswa->update(['status_aktif' => 'N']);
                                }
                            });
                            Notification::make()
                                ->title('Siswa Berhasil Dinaikkan Kelas')
                                ->success()
                                ->send();
                        }),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()
            ->orderBy('kelas_sekarang', 'asc')
            ->orderBy(DB::raw('LOWER(nama_lengkap)'), 'asc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSiswas::route('/'),
            'create' => Pages\CreateSiswa::route('/create'),
            'edit' => Pages\EditSiswa::route('/{record}/edit'),
        ];
    }
}