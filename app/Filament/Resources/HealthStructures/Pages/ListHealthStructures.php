<?php

namespace App\Filament\Resources\HealthStructures\Pages;

use App\Filament\Resources\HealthStructures\HealthStructureResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListHealthStructures extends ListRecords
{
    protected static string $resource = HealthStructureResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
