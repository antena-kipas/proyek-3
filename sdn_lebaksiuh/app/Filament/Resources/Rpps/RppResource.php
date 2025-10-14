<?php

namespace App\Filament\Resources\Rpps;

use App\Filament\Resources\Rpps\Pages\CreateRpp;
use App\Filament\Resources\Rpps\Pages\EditRpp;
use App\Filament\Resources\Rpps\Pages\ListRpps;
use App\Models\Rpp;
use BackedEnum;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class RppResource extends Resource
{
    protected static ?string $model = Rpp::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
                TextInput::make('mata_pelajaran')
                    ->required()
                    ->maxLength(255),
                TextInput::make('topik_materi')
                    ->required()
                    ->maxLength(255),
                TextInput::make('alokasi_waktu')
                    ->required()
                    ->maxLength(255),
                Textarea::make('tujuan_1')
                    ->required()
                    ->columnSpanFull(),
                Textarea::make('tujuan_2')
                    ->nullable()
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('mata_pelajaran'),
                TextColumn::make('topik_materi'),
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
