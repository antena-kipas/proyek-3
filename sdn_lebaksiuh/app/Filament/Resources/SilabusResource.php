<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SilabusResource\Pages;
use App\Filament\Resources\SilabusResource\Schemas\SilabusForm;
use App\Models\Silabus;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SilabusResource extends Resource
{
    protected static ?string $model = Silabus::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return SilabusForm::form($form);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->label('ID Silabus')->sortable(),
                Tables\Columns\TextColumn::make('tema')->label('Tema'),
                Tables\Columns\TextColumn::make('sub_tema')->label('Sub Tema'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListSilabuses::route('/'),
            'create' => Pages\CreateSilabus::route('/create'),
            'edit' => Pages\EditSilabus::route('/{record}/edit'),
        ];
    }
}