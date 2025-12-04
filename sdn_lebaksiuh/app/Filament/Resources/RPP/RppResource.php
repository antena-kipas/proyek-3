<?php

namespace App\Filament\Resources\RPP;

use App\Filament\Resources\RPP\Pages\CreateRpp;
use App\Filament\Resources\RPP\Pages\EditRpp;
use App\Filament\Resources\RPP\Pages\ListRPP;
use App\Filament\Resources\RPP\Schemas\RppForm;
use App\Models\Rpp;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Table;

class RppResource extends Resource
{
    protected static ?string $model = Rpp::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    public static function getNavigationLabel(): string
    {
        return 'RPP';
    }

    public static function getPluralModelLabel(): string
    {
        return 'RPP';
    }

    public static function getModelLabel(): string
    {
        return 'RPP';
    }

    public static function form(Form $form): Form
    {
        return RppForm::form($form);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->label('ID RPP')->sortable(),
                TextColumn::make('tema_name')->label('Tema'),
                TextColumn::make('sub_tema_name')->label('Sub Tema'),
                TextColumn::make('user.kelas')->label('Kelas'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Action::make('unduh')
                    ->label('Unduh')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->url(fn (Rpp $record): string => route('rpp.download', $record))
                    ->openUrlInNewTab(),
                Action::make('backup')
                    ->label('Backup')
                    ->icon('heroicon-o-archive-box')
                    ->color('info')
                    // ->requiresConfirmation()
                    ->action(function (Rpp $record) {
                        try {
                            \App\Jobs\BackupRppToGoogleDrive::dispatch($record, auth()->user());
                            \Filament\Notifications\Notification::make()
                                ->title('Backup Dimulai')
                                ->body('Proses backup untuk RPP ID ' . $record->id . ' telah dimulai di latar belakang.')
                                ->success()
                                ->send();
                        } catch (\Exception $e) {
                            \Filament\Notifications\Notification::make()
                                ->title('Gagal Memulai Backup')
                                ->body('Terjadi kesalahan: ' . $e->getMessage())
                                ->danger()
                                ->persistent()
                                ->send();
                        }
                    })
                    ->disabled(fn (Rpp $record): bool => !empty($record->google_drive_file_id))
                    ->tooltip(fn (Rpp $record): string => !empty($record->google_drive_file_id) ? 'RPP ini sudah di-backup' : 'Backup RPP ke Google Drive'),
                DeleteAction::make(),
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
            'index' => Pages\ListRPP::route('/'),
            'create' => CreateRpp::route('/create'),
            'edit' => EditRpp::route('/{record}/edit'),
        ];
    }
}