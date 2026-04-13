<?php

namespace App\Filament\Resources\HealthStructures;

use App\Filament\Resources\HealthStructures\Pages\CreateHealthStructure;
use App\Filament\Resources\HealthStructures\Pages\EditHealthStructure;
use App\Filament\Resources\HealthStructures\Pages\ListHealthStructures;
use App\Filament\Resources\HealthStructures\Schemas\HealthStructureForm;
use App\Filament\Resources\HealthStructures\Tables\HealthStructuresTable;
use App\Models\HealthStructure;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class HealthStructureResource extends Resource
{
    protected static ?string $model = HealthStructure::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingOffice2;

    protected static string|UnitEnum|null $navigationGroup = 'Géographie';

    protected static ?string $navigationLabel = 'Structures de santé';

    protected static ?string $modelLabel = 'Structure de santé';

    protected static ?string $pluralModelLabel = 'Structures de santé';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return HealthStructureForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return HealthStructuresTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListHealthStructures::route('/'),
            'create' => CreateHealthStructure::route('/create'),
            'edit'   => EditHealthStructure::route('/{record}/edit'),
        ];
    }
}
