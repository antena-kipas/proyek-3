<?php

namespace App\Filament\Resources\Rpps;

use App\Filament\Resources\Rpps\Pages\CreateRpp;
use App\Filament\Resources\Rpps\Pages\EditRpp;
use App\Filament\Resources\Rpps\Pages\ListRpps;
use App\Filament\Resources\Rpps\Schemas\RppForm;
use App\Models\Rpp;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class RppResource extends Resource
{
    protected static ?string $model = Rpp::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    public static function form(Schema $schema): Schema
    {
        return RppForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tema_name')->label('Tema'),
                TextColumn::make('sub_tema_name')->label('Sub Tema'),
                TextColumn::make('user.name')->label('Dibuat oleh'),
            ])
            ->filters([
                //
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