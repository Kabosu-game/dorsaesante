<?php

namespace App\Filament\Resources\CommunityReports;

use App\Filament\Resources\CommunityReports\Pages\CreateCommunityReport;
use App\Filament\Resources\CommunityReports\Pages\EditCommunityReport;
use App\Filament\Resources\CommunityReports\Pages\ListCommunityReports;
use App\Filament\Resources\CommunityReports\Schemas\CommunityReportForm;
use App\Filament\Resources\CommunityReports\Tables\CommunityReportsTable;
use App\Models\CommunityReport;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class CommunityReportResource extends Resource
{
    protected static ?string $model = CommunityReport::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedFlag;

    protected static string|UnitEnum|null $navigationGroup = 'Alertes & Urgences';

    protected static ?string $navigationLabel = 'Signalements communautaires';

    protected static ?string $modelLabel = 'Signalement';

    protected static ?string $pluralModelLabel = 'Signalements communautaires';

    protected static ?int $navigationSort = 3;

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Schema $schema): Schema
    {
        return CommunityReportForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CommunityReportsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListCommunityReports::route('/'),
            'create' => CreateCommunityReport::route('/create'),
            'edit'   => EditCommunityReport::route('/{record}/edit'),
        ];
    }
}
