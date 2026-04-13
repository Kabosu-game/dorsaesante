<?php

namespace App\Filament\Resources\CommunityReports\Pages;

use App\Filament\Resources\CommunityReports\CommunityReportResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCommunityReports extends ListRecords
{
    protected static string $resource = CommunityReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
