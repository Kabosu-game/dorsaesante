<?php

namespace App\Filament\Resources\HealthAlerts\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class HealthAlertForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Contenu de l\'alerte')
                    ->columns(2)
                    ->schema([
                        TextInput::make('title')
                            ->label('Titre')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),

                        Textarea::make('message')
                            ->label('Message')
                            ->required()
                            ->rows(4)
                            ->columnSpanFull(),

                        Select::make('level')
                            ->label('Niveau')
                            ->options([
                                'info'     => 'Information',
                                'warning'  => 'Avertissement',
                                'danger'   => 'Danger',
                                'critical' => 'Critique',
                            ])
                            ->default('info')
                            ->required(),

                        TextInput::make('type')
                            ->label('Type')
                            ->required()
                            ->maxLength(100),

                        Select::make('author_id')
                            ->label('Auteur')
                            ->relationship('author', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Select::make('zone_id')
                            ->label('Zone ciblée')
                            ->relationship('zone', 'name')
                            ->searchable()
                            ->preload(),

                        TextInput::make('target_roles')
                            ->label('Rôles ciblés')
                            ->placeholder('patient,doctor'),

                        DateTimePicker::make('expires_at')
                            ->label('Expire le'),

                        Toggle::make('is_active')
                            ->label('Active')
                            ->default(true),

                        FileUpload::make('image')
                            ->label('Image')
                            ->image()
                            ->directory('health-alerts')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
