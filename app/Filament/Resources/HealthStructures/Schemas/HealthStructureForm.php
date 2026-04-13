<?php

namespace App\Filament\Resources\HealthStructures\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class HealthStructureForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informations générales')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->label('Nom')
                            ->required()
                            ->maxLength(255),

                        Select::make('type')
                            ->label('Type')
                            ->options([
                                'hospital'     => 'Hôpital',
                                'clinic'       => 'Clinique',
                                'health_center' => 'Centre de santé',
                                'pharmacy'     => 'Pharmacie',
                                'laboratory'   => 'Laboratoire',
                                'dispensary'   => 'Dispensaire',
                            ])
                            ->required(),

                        Textarea::make('description')
                            ->label('Description')
                            ->rows(3)
                            ->columnSpanFull(),

                        TextInput::make('address')
                            ->label('Adresse')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),

                        Select::make('zone_id')
                            ->label('Zone')
                            ->relationship('zone', 'name')
                            ->searchable()
                            ->preload(),

                        TextInput::make('opening_hours')
                            ->label('Horaires d\'ouverture')
                            ->maxLength(255),
                    ]),

                Section::make('Contact')
                    ->columns(2)
                    ->schema([
                        TextInput::make('phone')
                            ->label('Téléphone')
                            ->tel()
                            ->maxLength(20),

                        TextInput::make('email')
                            ->label('E-mail')
                            ->email()
                            ->maxLength(255),

                        TextInput::make('website')
                            ->label('Site web')
                            ->url()
                            ->maxLength(255)
                            ->columnSpanFull(),
                    ]),

                Section::make('Localisation')
                    ->columns(2)
                    ->schema([
                        TextInput::make('latitude')
                            ->label('Latitude')
                            ->numeric(),

                        TextInput::make('longitude')
                            ->label('Longitude')
                            ->numeric(),
                    ]),

                Section::make('Services & Statut')
                    ->columns(3)
                    ->schema([
                        Toggle::make('has_emergency')
                            ->label('Service urgences'),

                        Toggle::make('has_teleconsult')
                            ->label('Téléconsultation'),

                        Toggle::make('is_active')
                            ->label('Active'),

                        FileUpload::make('image')
                            ->label('Image')
                            ->image()
                            ->directory('structures')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
