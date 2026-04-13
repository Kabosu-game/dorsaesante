<?php

namespace App\Filament\Resources\CommunityReports\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class CommunityReportsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('Signalé par')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('type')
                    ->label('Type')
                    ->badge()
                    ->formatStateUsing(fn($state) => match($state) {
                        'disease_outbreak' => 'Épidémie',
                        'water_quality'    => 'Qualité de l\'eau',
                        'sanitation'       => 'Assainissement',
                        'food_safety'      => 'Sécurité alimentaire',
                        'other'            => 'Autre',
                        default            => $state,
                    }),

                TextColumn::make('title')
                    ->label('Titre')
                    ->searchable()
                    ->limit(40),

                TextColumn::make('zone.name')
                    ->label('Zone')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('status')
                    ->label('Statut')
                    ->badge()
                    ->color(fn($state) => match($state) {
                        'pending'      => 'warning',
                        'under_review' => 'info',
                        'resolved'     => 'success',
                        'dismissed'    => 'gray',
                        default        => 'gray',
                    })
                    ->formatStateUsing(fn($state) => match($state) {
                        'pending'      => 'En attente',
                        'under_review' => 'En cours d\'examen',
                        'resolved'     => 'Résolu',
                        'dismissed'    => 'Rejeté',
                        default        => $state,
                    }),

                IconColumn::make('is_anonymous')
                    ->label('Anonyme')
                    ->boolean(),

                TextColumn::make('created_at')
                    ->label('Signalé le')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label('Type')
                    ->options([
                        'disease_outbreak' => 'Épidémie',
                        'water_quality'    => 'Qualité de l\'eau',
                        'sanitation'       => 'Assainissement',
                        'food_safety'      => 'Sécurité alimentaire',
                        'other'            => 'Autre',
                    ]),

                SelectFilter::make('status')
                    ->label('Statut')
                    ->options([
                        'pending'      => 'En attente',
                        'under_review' => 'En cours d\'examen',
                        'resolved'     => 'Résolu',
                        'dismissed'    => 'Rejeté',
                    ]),

                TernaryFilter::make('is_anonymous')
                    ->label('Anonyme'),
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
