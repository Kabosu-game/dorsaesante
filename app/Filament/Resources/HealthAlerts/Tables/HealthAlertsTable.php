<?php

namespace App\Filament\Resources\HealthAlerts\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class HealthAlertsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('Titre')
                    ->searchable()
                    ->sortable()
                    ->limit(50),

                TextColumn::make('level')
                    ->label('Niveau')
                    ->badge()
                    ->color(fn($state) => match($state) {
                        'info'     => 'info',
                        'warning'  => 'warning',
                        'danger'   => 'danger',
                        'critical' => 'danger',
                        default    => 'gray',
                    })
                    ->formatStateUsing(fn($state) => match($state) {
                        'info'     => 'Information',
                        'warning'  => 'Avertissement',
                        'danger'   => 'Danger',
                        'critical' => 'Critique',
                        default    => $state,
                    }),

                TextColumn::make('type')
                    ->label('Type')
                    ->searchable(),

                TextColumn::make('author.name')
                    ->label('Auteur')
                    ->sortable(),

                TextColumn::make('zone.name')
                    ->label('Zone')
                    ->sortable()
                    ->toggleable(),

                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),

                TextColumn::make('expires_at')
                    ->label('Expire le')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Créé le')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('level')
                    ->label('Niveau')
                    ->options([
                        'info'     => 'Information',
                        'warning'  => 'Avertissement',
                        'danger'   => 'Danger',
                        'critical' => 'Critique',
                    ]),

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
            ->defaultSort('created_at', 'desc');
    }
}
