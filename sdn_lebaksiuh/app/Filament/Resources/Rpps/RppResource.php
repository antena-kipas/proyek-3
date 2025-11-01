<?php

namespace App\Filament\Resources\Rpps;

use App\Filament\Resources\Rpps\Pages\CreateRpp;
use App\Filament\Resources\Rpps\Pages\EditRpp;
use App\Filament\Resources\Rpps\Pages\ListRpps;
use App\Filament\Resources\Rpps\Schemas\RppForm;
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
                    ->url(fn (Rpp $record): string => route('rpps.download', $record))
                    ->openUrlInNewTab(),
                Action::make('backup')
                    ->label('Backup')
                    ->icon('heroicon-o-archive-box')
                    ->color('info')
                    ->url(fn (Rpp $record): string => '#') // Placeholder URL
                    ->openUrlInNewTab(),
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
            'index' => ListRpps::route('/'),
            'create' => CreateRpp::route('/create'),
            'edit' => EditRpp::route('/{record}/edit'),
        ];
    }
}