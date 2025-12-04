<?php

namespace App\Filament\Resources\RPP\Pages;

use App\Filament\Resources\RPP\RppResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListRPP extends ListRecords
{
    protected static string $resource = RppResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

