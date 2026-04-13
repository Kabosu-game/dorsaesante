<?php

namespace App\Filament\Resources\HealthStructures\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class HealthStructuresTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nom')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('type')
                    ->label('Type')
                    ->badge()
                    ->formatStateUsing(fn($state) => match($state) {
                        'hospital'      => 'Hôpital',
                        'clinic'        => 'Clinique',
                        'health_center' => 'Centre de santé',
                        'pharmacy'      => 'Pharmacie',
                        'laboratory'    => 'Laboratoire',
                        'dispensary'    => 'Dispensaire',
                        default         => $state,
                    }),

                TextColumn::make('zone.name')
                    ->label('Zone')
                    ->sortable(),

                TextColumn::make('phone')
                    ->label('Téléphone')
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('address')
                    ->label('Adresse')
                    ->limit(40)
                    ->toggleable(),

                IconColumn::make('has_emergency')
                    ->label('Urgences')
                    ->boolean(),

                IconColumn::make('has_teleconsult')
                    ->label('Téléconsult')
                    ->boolean(),

                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),

                TextColumn::make('created_at')
                    ->label('Créé le')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label('Type')
                    ->options([
                        'hospital'      => 'Hôpital',
                        'clinic'        => 'Clinique',
                        'health_center' => 'Centre de santé',
                        'pharmacy'      => 'Pharmacie',
                        'laboratory'    => 'Laboratoire',
                        'dispensary'    => 'Dispensaire',
                    ]),

                TernaryFilter::make('has_emergency')
                    ->label('Service urgences'),

                TernaryFilter::make('is_active')
                    ->label('Active'),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('name');
    }
}
