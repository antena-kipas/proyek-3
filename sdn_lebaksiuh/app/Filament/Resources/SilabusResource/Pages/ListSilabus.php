<?php

namespace App\Filament\Resources\SilabusResource\Pages;

use App\Filament\Resources\SilabusResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSilabus extends ListRecords
{
    protected static string $resource = SilabusResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
