<?php

namespace App\Filament\Resources\LiveStreams\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class LiveStreamsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('doctor.name')
                    ->label('Médecin')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('title')
                    ->label('Titre')
                    ->searchable()
                    ->limit(40),

                TextColumn::make('topic')
                    ->label('Sujet')
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('status')
                    ->label('Statut')
                    ->badge()
                    ->color(fn($state) => match($state) {
                        'scheduled' => 'info',
                        'live'      => 'danger',
                        'ended'     => 'success',
                        'cancelled' => 'gray',
                        default     => 'gray',
                    })
                    ->formatStateUsing(fn($state) => match($state) {
                        'scheduled' => 'Planifié',
                        'live'      => 'En direct',
                        'ended'     => 'Terminé',
                        'cancelled' => 'Annulé',
                        default     => $state,
                    }),

                TextColumn::make('scheduled_at')
                    ->label('Planifié le')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                TextColumn::make('viewers_count')
                    ->label('Spectateurs')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('max_viewers')
                    ->label('Max spectateurs')
                    ->numeric()
                    ->sortable()
                    ->toggleable(),

                IconColumn::make('is_recorded')
                    ->label('Enregistré')
                    ->boolean()
                    ->toggleable(),

                TextColumn::make('created_at')
                    ->label('Créé le')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Statut')
                    ->options([
                        'scheduled' => 'Planifié',
                        'live'      => 'En direct',
                        'ended'     => 'Terminé',
                        'cancelled' => 'Annulé',
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
