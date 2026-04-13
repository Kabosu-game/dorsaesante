<?php

namespace App\Filament\Resources\CommunityReports\Pages;

use App\Filament\Resources\CommunityReports\CommunityReportResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCommunityReport extends EditRecord
{
    protected static string $resource = CommunityReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
