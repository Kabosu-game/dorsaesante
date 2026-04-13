<?php

namespace App\Filament\Resources\Appointments\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class AppointmentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('patient.name')
                    ->label('Patient')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('doctor.name')
                    ->label('Médecin')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('healthStructure.name')
                    ->label('Structure')
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('scheduled_at')
                    ->label('Date & Heure')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                TextColumn::make('duration_minutes')
                    ->label('Durée (min)')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('type')
                    ->label('Type')
                    ->badge()
                    ->color(fn($state) => match($state) {
                        'in_person'        => 'primary',
                        'teleconsultation' => 'info',
                        default            => 'gray',
                    })
                    ->formatStateUsing(fn($state) => match($state) {
                        'in_person'        => 'En personne',
                        'teleconsultation' => 'Téléconsultation',
                        default            => $state,
                    }),

                TextColumn::make('status')
                    ->label('Statut')
                    ->badge()
                    ->color(fn($state) => match($state) {
                        'pending'   => 'warning',
                        'confirmed' => 'info',
                        'cancelled' => 'danger',
                        'completed' => 'success',
                        'no_show'   => 'gray',
                        default     => 'gray',
                    })
                    ->formatStateUsing(fn($state) => match($state) {
                        'pending'   => 'En attente',
                        'confirmed' => 'Confirmé',
                        'cancelled' => 'Annulé',
                        'completed' => 'Terminé',
                        'no_show'   => 'Absent',
                        default     => $state,
                    }),

                IconColumn::make('reminder_sent')
                    ->label('Rappel')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('Créé le')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Statut')
                    ->options([
                        'pending'   => 'En attente',
                        'confirmed' => 'Confirmé',
                        'cancelled' => 'Annulé',
                        'completed' => 'Terminé',
                        'no_show'   => 'Absent',
                    ]),

                SelectFilter::make('type')
                    ->label('Type')
                    ->options([
                        'in_person'        => 'En personne',
                        'teleconsultation' => 'Téléconsultation',
                    ]),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('scheduled_at', 'desc');
    }
}
