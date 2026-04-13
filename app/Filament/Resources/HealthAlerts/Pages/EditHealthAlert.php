<?php

namespace App\Filament\Resources\HealthAlerts\Pages;

use App\Filament\Resources\HealthAlerts\HealthAlertResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditHealthAlert extends EditRecord
{
    protected static string $resource = HealthAlertResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
