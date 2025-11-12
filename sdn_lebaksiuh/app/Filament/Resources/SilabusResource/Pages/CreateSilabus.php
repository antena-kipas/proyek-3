<?php

namespace App\Filament\Resources\SilabusResource\Pages;

use App\Filament\Resources\SilabusResource;
use Filament\Resources\Pages\CreateRecord;

class CreateSilabus extends CreateRecord
{
    protected static string $resource = SilabusResource::class;

    protected function getFormActions(): array
    {
        return [
            $this->getCreateFormAction()->label('Simpan'),
            $this->getCancelFormAction(),
        ];
    }
}
