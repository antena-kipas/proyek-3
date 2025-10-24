<?php

namespace App\Filament\Resources\Rpps\Pages;

use App\Filament\Resources\Rpps\RppResource;
use Filament\Actions\Action; // Import kelas Action
use Filament\Resources\Pages\CreateRecord;

class CreateRpp extends CreateRecord
{
    protected static string $resource = RppResource::class;

    protected function getFormActions(): array
    {
        return [
            $this->getCreateFormAction(),
            $this->getCancelFormAction(),
        ];
    }
}
