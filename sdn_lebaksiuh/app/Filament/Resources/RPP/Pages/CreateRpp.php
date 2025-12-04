<?php

namespace App\Filament\Resources\RPP\Pages;

use App\Filament\Resources\RPP\RppResource;
use Filament\Actions\Action; // Import kelas Action
use Filament\Resources\Pages\CreateRecord;

class CreateRpp extends CreateRecord
{
    protected static string $resource = RppResource::class;

    protected function getFormActions(): array
    {
        return [
            $this->getCreateFormAction()->label('Simpan'),
            $this->getCancelFormAction(),
        ];
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id();

        return $data;
    }
}
