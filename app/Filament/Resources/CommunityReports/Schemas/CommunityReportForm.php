<?php

namespace App\Filament\Resources\CommunityReports\Schemas;

use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class CommunityReportForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Signalement')
                    ->columns(2)
                    ->schema([
                        Select::make('user_id')
                            ->label('Signalé par')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Select::make('type')
                            ->label('Type')
                            ->options([
                                'disease_outbreak' => 'Épidémie',
                                'water_quality'    => 'Qualité de l\'eau',
                                'sanitation'       => 'Assainissement',
                                'food_safety'      => 'Sécurité alimentaire',
                                'other'            => 'Autre',
                            ])
                            ->required(),

                        TextInput::make('title')
                            ->label('Titre')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),

                        Textarea::make('description')
                            ->label('Description')
                            ->required()
                            ->rows(4)
                            ->columnSpanFull(),

                        Select::make('status')
                            ->label('Statut')
                            ->options([
                                'pending'      => 'En attente',
                                'under_review' => 'En cours d\'examen',
                                'resolved'     => 'Résolu',
                                'dismissed'    => 'Rejeté',
                            ])
                            ->default('pending')
                            ->required(),

                        Select::make('zone_id')
                            ->label('Zone')
                            ->relationship('zone', 'name')
                            ->searchable()
                            ->preload(),

                        Toggle::make('is_anonymous')
                            ->label('Signalement anonyme'),
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

                        TextInput::make('address')
                            ->label('Adresse')
                            ->maxLength(255)
                            ->columnSpanFull(),
                    ]),

                Section::make('Réponse administrative')
                    ->schema([
                        Textarea::make('admin_response')
                            ->label('Réponse de l\'administration')
                            ->rows(3),
                    ]),
            ]);
    }
}
