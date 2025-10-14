<?php

namespace App\Filament\Resources\Rpps\Pages;

use App\Filament\Resources\Rpps\RppResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListRpps extends ListRecords
{
    protected static string $resource = RppResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
