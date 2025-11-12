<?php

namespace App\Filament\Resources\SilabusResource\Pages;

use App\Filament\Resources\SilabusResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSilabus extends EditRecord
{
    protected static string $resource = SilabusResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
