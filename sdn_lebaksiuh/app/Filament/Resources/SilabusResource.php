<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SilabusResource\Pages;
use App\Filament\Resources\SilabusResource\Schemas\SilabusForm;
use App\Models\Silabus;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use Filament\Notifications\Notification;
use App\Jobs\BackupSilabusToGoogleDrive;

class SilabusResource extends Resource
{
    protected static ?string $model = Silabus::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getNavigationLabel(): string
    {
        return 'Silabus';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Silabus';
    }

    public static function getModelLabel(): string
    {
        return 'Silabus';
    }
    
        public static function form(Form $form): Form
        {
            return SilabusForm::form($form);
        }

        public static function table(Table $table): Table
        {
            return $table
                ->columns([
                    Tables\Columns\TextColumn::make('id')->label('ID Silabus')->sortable(),
                    Tables\Columns\TextColumn::make('user.name')->label('Guru')->sortable(),
                    Tables\Columns\TextColumn::make('mataPelajaran.nama_pelajaran')->label('Mata Pelajaran')->sortable(),
                    Tables\Columns\TextColumn::make('id_tema')->label('Tema ke-')->sortable(),
                    Tables\Columns\TextColumn::make('id_subtema')->label('Subtema ke-')->sortable(),
                    Tables\Columns\TextColumn::make('tema')->label('Tema'),
                    Tables\Columns\TextColumn::make('sub_tema')->label('Sub Tema'),
                ])
                ->filters([
                    //
                ])
                ->actions([
                    Action::make('unduh')
                        ->label('Unduh')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->url(fn (Silabus $record) => route('silabus.download', $record))
                        ->openUrlInNewTab(),
                    Action::make('backup')
                        ->label('Backup')
                        ->icon('heroicon-o-cloud-arrow-up')
                        ->requiresConfirmation()
                        ->action(function (Silabus $record) {
                            BackupSilabusToGoogleDrive::dispatch($record, auth()->user());
                            Notification::make()
                                ->title('Backup Dimulai')
                                ->body('Proses backup silabus sedang berjalan di latar belakang.')
                                ->info()
                                ->send();
                        })
                        ->color('info'),
                    Tables\Actions\DeleteAction::make(),
                ])
                ->bulkActions([
                    Tables\Actions\BulkActionGroup::make([
                        Tables\Actions\DeleteBulkAction::make(),
                    ]),
                ]);
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
                'index' => Pages\ListSilabus::route('/'),
                'create' => Pages\CreateSilabus::route('/create'),
                'edit' => Pages\EditSilabus::route('/{record}/edit'),
            ];
        }
    }