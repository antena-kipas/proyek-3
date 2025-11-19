<?php

namespace App\Filament\Resources\SilabusResource\Pages;

use App\Filament\Resources\SilabusResource;
use App\Models\Silabus;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateSilabus extends CreateRecord
{
    protected static string $resource = SilabusResource::class;
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

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

    protected function handleRecordCreation(array $data): Model
    {
        // Extract relationship data
        $relationshipData = [
            'kompetensiIntis' => $data['kompetensiIntis'] ?? [],
            'kompetensiDasars' => $data['kompetensiDasars'] ?? [],
            'indikators' => $data['indikators'] ?? [],
            'materiPelajaran' => $data['materiPelajaran'] ?? [],
            'kegiatanPembelajaran' => $data['kegiatanPembelajaran'] ?? [],
            'penilaianDiri' => $data['penilaianDiri'] ?? [],
        ];

        // Remove relationship data from the main data array
        unset(
            $data['kompetensiIntis'],
            $data['kompetensiDasars'],
            $data['indikators'],
            $data['materiPelajaran'],
            $data['kegiatanPembelajaran'],
            $data['penilaianDiri']
        );

        // Create the main Silabus record
        $silabus = static::getModel()::create($data);

        // Create and associate related records
        foreach ($relationshipData as $relationship => $items) {
            if (!empty($items)) {
                if ($relationship === 'kompetensiIntis') {
                    $processedItems = [];
                    foreach (array_values($items) as $key => $item) {
                        $item['urutan'] = $key + 1;
                        $processedItems[] = $item;
                    }
                    $items = $processedItems;
                }
                $silabus->{$relationship}()->createMany($items);
            }
        }

        return $silabus;
    }
}
