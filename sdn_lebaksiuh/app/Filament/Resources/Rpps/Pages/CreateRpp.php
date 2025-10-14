<?php

namespace App\Filament\Resources\Rpps\Pages;

use App\Filament\Resources\Rpps\RppResource;
use Filament\Actions\Action; // Import kelas Action
use Filament\Resources\Pages\CreateRecord;

class CreateRpp extends CreateRecord
{
    protected static string $resource = RppResource::class;

    // Method ini akan menimpa action default
    protected function getFormActions(): array
    {
        return [
            // Kita hanya akan menampilkan action "Create" utama
            $this->getCreateFormAction(),
        ];
    }
}
