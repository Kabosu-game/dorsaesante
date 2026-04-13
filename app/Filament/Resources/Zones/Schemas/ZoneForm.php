<?php

namespace App\Filament\Resources\Zones\Schemas;

use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ZoneForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Zone géographique')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->label('Nom')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('code')
                            ->label('Code')
                            ->maxLength(20),

                        Select::make('type')
                            ->label('Type')
                            ->options([
                                'region'   => 'Région',
                                'district' => 'District',
                                'commune'  => 'Commune',
                                'village'  => 'Village',
                            ])
                            ->default('district')
                            ->required(),

                        Select::make('parent_id')
                            ->label('Zone parente')
                            ->relationship('parent', 'name')
                            ->searchable()
                            ->preload(),

                        TextInput::make('latitude')
                            ->label('Latitude')
                            ->numeric(),

                        TextInput::make('longitude')
                            ->label('Longitude')
                            ->numeric(),
                    ]),
            ]);
    }
}
