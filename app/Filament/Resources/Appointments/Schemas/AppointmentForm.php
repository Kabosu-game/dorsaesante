<?php

namespace App\Filament\Resources\Appointments\Schemas;

use App\Models\HealthStructure;
use App\Models\User;
use Filament\Forms\Components\DateTimePicker;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class AppointmentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Participants')
                    ->columns(2)
                    ->schema([
                        Select::make('patient_id')
                            ->label('Patient')
                            ->relationship('patient', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Select::make('doctor_id')
                            ->label('Médecin')
                            ->relationship('doctor', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Select::make('health_structure_id')
                            ->label('Structure de santé')
                            ->relationship('healthStructure', 'name')
                            ->searchable()
                            ->preload()
                            ->columnSpanFull(),
                    ]),

                Section::make('Détails du rendez-vous')
                    ->columns(2)
                    ->schema([
                        DateTimePicker::make('scheduled_at')
                            ->label('Date et heure')
                            ->required(),

                        TextInput::make('duration_minutes')
                            ->label('Durée (minutes)')
                            ->numeric()
                            ->default(30)
                            ->required(),

                        Select::make('type')
                            ->label('Type')
                            ->options([
                                'in_person'       => 'En personne',
                                'teleconsultation' => 'Téléconsultation',
                            ])
                            ->default('in_person')
                            ->required(),

                        Select::make('status')
                            ->label('Statut')
                            ->options([
                                'pending'    => 'En attente',
                                'confirmed'  => 'Confirmé',
                                'cancelled'  => 'Annulé',
                                'completed'  => 'Terminé',
                                'no_show'    => 'Absent',
                            ])
                            ->default('pending')
                            ->required(),

                        Textarea::make('reason')
                            ->label('Motif')
                            ->rows(3)
                            ->columnSpanFull(),

                        Textarea::make('notes')
                            ->label('Notes')
                            ->rows(3)
                            ->columnSpanFull(),

                        TextInput::make('cancellation_reason')
                            ->label('Motif d\'annulation')
                            ->columnSpanFull(),

                        Toggle::make('reminder_sent')
                            ->label('Rappel envoyé'),
                    ]),
            ]);
    }
}
