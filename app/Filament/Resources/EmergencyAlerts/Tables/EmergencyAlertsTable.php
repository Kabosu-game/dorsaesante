<?php

namespace App\Filament\Resources\EmergencyAlerts\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class EmergencyAlertsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('Utilisateur')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('type')
                    ->label('Type')
                    ->badge()
                    ->color(fn($state) => match($state) {
                        'medical'  => 'danger',
                        'accident' => 'warning',
                        'fire'     => 'danger',
                        default    => 'gray',
                    })
                    ->formatStateUsing(fn($state) => match($state) {
                        'medical'  => 'Médical',
                        'accident' => 'Accident',
                        'fire'     => 'Incendie',
                        'other'    => 'Autre',
                        default    => $state,
                    }),

                TextColumn::make('status')
                    ->label('Statut')
                    ->badge()
                    ->color(fn($state) => match($state) {
                        'sent'       => 'danger',
                        'received'   => 'warning',
                        'dispatched' => 'info',
                        'resolved'   => 'success',
                        default      => 'gray',
                    })
                    ->formatStateUsing(fn($state) => match($state) {
                        'sent'       => 'Envoyé',
                        'received'   => 'Reçu',
                        'dispatched' => 'Dispatché',
                        'resolved'   => 'Résolu',
                        default      => $state,
                    }),

                TextColumn::make('address')
                    ->label('Adresse')
                    ->limit(35)
                    ->searchable(),

                TextColumn::make('nearestStructure.name')
                    ->label('Structure proche')
                    ->toggleable(),

                TextColumn::make('resolved_at')
                    ->label('Résolu le')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label('Type')
                    ->options([
                        'medical'  => 'Médical',
                        'accident' => 'Accident',
                        'fire'     => 'Incendie',
                        'other'    => 'Autre',
                    ]),

                SelectFilter::make('status')
                    ->label('Statut')
                    ->options([
                        'sent'       => 'Envoyé',
                        'received'   => 'Reçu',
                        'dispatched' => 'Dispatché',
                        'resolved'   => 'Résolu',
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
            ->defaultSort('created_at', 'desc');
    }
}
