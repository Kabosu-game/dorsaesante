<?php

namespace App\Filament\Resources\HealthStructures\Pages;

use App\Filament\Resources\HealthStructures\HealthStructureResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditHealthStructure extends EditRecord
{
    protected static string $resource = HealthStructureResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
