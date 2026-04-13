<?php

namespace App\Filament\Resources\EmergencyAlerts\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class EmergencyAlertForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informations de l\'urgence')
                    ->columns(2)
                    ->schema([
                        Select::make('user_id')
                            ->label('Utilisateur')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Select::make('type')
                            ->label('Type')
                            ->options([
                                'medical'  => 'Médical',
                                'accident' => 'Accident',
                                'fire'     => 'Incendie',
                                'other'    => 'Autre',
                            ])
                            ->required(),

                        Select::make('status')
                            ->label('Statut')
                            ->options([
                                'sent'       => 'Envoyé',
                                'received'   => 'Reçu',
                                'dispatched' => 'Dispatché',
                                'resolved'   => 'Résolu',
                            ])
                            ->default('sent')
                            ->required(),

                        Select::make('nearest_structure_id')
                            ->label('Structure la plus proche')
                            ->relationship('nearestStructure', 'name')
                            ->searchable()
                            ->preload(),

                        Textarea::make('description')
                            ->label('Description')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),

                Section::make('Localisation')
                    ->columns(2)
                    ->schema([
                        TextInput::make('latitude')
                            ->label('Latitude')
                            ->numeric()
                            ->required(),

                        TextInput::make('longitude')
                            ->label('Longitude')
                            ->numeric()
                            ->required(),

                        TextInput::make('address')
                            ->label('Adresse')
                            ->maxLength(255)
                            ->columnSpanFull(),
                    ]),

                Section::make('Résolution')
                    ->columns(1)
                    ->schema([
                        DateTimePicker::make('resolved_at')
                            ->label('Résolu le'),

                        Textarea::make('responder_notes')
                            ->label('Notes du répondant')
                            ->rows(3),
                    ]),
            ]);
    }
}
