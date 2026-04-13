<?php

namespace App\Filament\Resources\HealthAlerts;

use App\Filament\Resources\HealthAlerts\Pages\CreateHealthAlert;
use App\Filament\Resources\HealthAlerts\Pages\EditHealthAlert;
use App\Filament\Resources\HealthAlerts\Pages\ListHealthAlerts;
use App\Filament\Resources\HealthAlerts\Schemas\HealthAlertForm;
use App\Filament\Resources\HealthAlerts\Tables\HealthAlertsTable;
use App\Models\HealthAlert;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class HealthAlertResource extends Resource
{
    protected static ?string $model = HealthAlert::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBellAlert;

    protected static string|UnitEnum|null $navigationGroup = 'Alertes & Urgences';

    protected static ?string $navigationLabel = 'Alertes sanitaires';

    protected static ?string $modelLabel = 'Alerte sanitaire';

    protected static ?string $pluralModelLabel = 'Alertes sanitaires';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Schema $schema): Schema
    {
        return HealthAlertForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return HealthAlertsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListHealthAlerts::route('/'),
            'create' => CreateHealthAlert::route('/create'),
            'edit'   => EditHealthAlert::route('/{record}/edit'),
        ];
    }
}
