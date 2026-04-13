<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Models\Zone;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informations personnelles')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->label('Nom complet')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('email')
                            ->label('Adresse e-mail')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),

                        TextInput::make('phone')
                            ->label('Téléphone')
                            ->tel()
                            ->maxLength(20),

                        Select::make('role')
                            ->label('Rôle')
                            ->options([
                                'admin'   => 'Administrateur',
                                'doctor'  => 'Médecin',
                                'patient' => 'Patient',
                            ])
                            ->default('patient')
                            ->required(),

                        DatePicker::make('birth_date')
                            ->label('Date de naissance')
                            ->maxDate(now()),

                        Select::make('gender')
                            ->label('Genre')
                            ->options([
                                'male'   => 'Homme',
                                'female' => 'Femme',
                                'other'  => 'Autre',
                            ]),

                        TextInput::make('address')
                            ->label('Adresse')
                            ->maxLength(255)
                            ->columnSpanFull(),

                        Select::make('zone_id')
                            ->label('Zone')
                            ->relationship('zone', 'name')
                            ->searchable()
                            ->preload(),
                    ]),

                Section::make('Compte & Sécurité')
                    ->columns(2)
                    ->schema([
                        TextInput::make('password')
                            ->label('Mot de passe')
                            ->password()
                            ->required(fn($context) => $context === 'create')
                            ->dehydrateStateUsing(fn($state) => filled($state) ? bcrypt($state) : null)
                            ->dehydrated(fn($state) => filled($state))
                            ->maxLength(255),

                        Toggle::make('is_active')
                            ->label('Compte actif')
                            ->default(true),

                        FileUpload::make('avatar')
                            ->label('Avatar')
                            ->image()
                            ->directory('avatars')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
