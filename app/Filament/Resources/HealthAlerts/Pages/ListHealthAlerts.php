<?php

namespace App\Filament\Resources\HealthAlerts\Pages;

use App\Filament\Resources\HealthAlerts\HealthAlertResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListHealthAlerts extends ListRecords
{
    protected static string $resource = HealthAlertResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
