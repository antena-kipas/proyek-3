<?php

namespace App\Filament\Resources\RPP\Pages;

use App\Filament\Resources\RPP\RppResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditRpp extends EditRecord
{
    protected static string $resource = RppResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
