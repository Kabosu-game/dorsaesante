<?php

namespace App\Filament\Resources\LiveStreams\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class LiveStreamForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informations du stream')
                    ->columns(2)
                    ->schema([
                        Select::make('doctor_id')
                            ->label('Médecin')
                            ->relationship('doctor', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        TextInput::make('title')
                            ->label('Titre')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),

                        Textarea::make('description')
                            ->label('Description')
                            ->rows(3)
                            ->columnSpanFull(),

                        TextInput::make('topic')
                            ->label('Sujet')
                            ->maxLength(255),

                        Select::make('status')
                            ->label('Statut')
                            ->options([
                                'scheduled' => 'Planifié',
                                'live'      => 'En direct',
                                'ended'     => 'Terminé',
                                'cancelled' => 'Annulé',
                            ])
                            ->default('scheduled')
                            ->required(),

                        DateTimePicker::make('scheduled_at')
                            ->label('Planifié le'),

                        DateTimePicker::make('started_at')
                            ->label('Démarré le'),

                        DateTimePicker::make('ended_at')
                            ->label('Terminé le'),
                    ]),

                Section::make('Technique')
                    ->columns(2)
                    ->schema([
                        TextInput::make('stream_key')
                            ->label('Clé de stream')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('stream_url')
                            ->label('URL de stream')
                            ->url()
                            ->maxLength(500),

                        TextInput::make('replay_url')
                            ->label('URL du replay')
                            ->url()
                            ->maxLength(500),

                        Toggle::make('is_recorded')
                            ->label('Enregistré'),

                        FileUpload::make('thumbnail')
                            ->label('Miniature')
                            ->image()
                            ->directory('livestreams')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
