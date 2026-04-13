<?php

namespace App\Filament\Resources\Users\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nom')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('email')
                    ->label('E-mail')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('phone')
                    ->label('Téléphone')
                    ->searchable(),

                TextColumn::make('role')
                    ->label('Rôle')
                    ->badge()
                    ->color(fn($state) => match($state) {
                        'admin'   => 'danger',
                        'doctor'  => 'primary',
                        'patient' => 'success',
                        default   => 'gray',
                    }),

                TextColumn::make('zone.name')
                    ->label('Zone')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('birth_date')
                    ->label('Naissance')
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('gender')
                    ->label('Genre')
                    ->badge()
                    ->formatStateUsing(fn($state) => match($state) {
                        'male'   => 'Homme',
                        'female' => 'Femme',
                        'other'  => 'Autre',
                        default  => $state,
                    })
                    ->toggleable(isToggledHiddenByDefault: true),

                IconColumn::make('is_active')
                    ->label('Actif')
                    ->boolean(),

                TextColumn::make('created_at')
                    ->label('Inscrit le')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('role')
                    ->label('Rôle')
                    ->options([
                        'admin'   => 'Administrateur',
                        'doctor'  => 'Médecin',
                        'patient' => 'Patient',
                    ]),

                TernaryFilter::make('is_active')
                    ->label('Actif'),

                SelectFilter::make('gender')
                    ->label('Genre')
                    ->options([
                        'male'   => 'Homme',
                        'female' => 'Femme',
                        'other'  => 'Autre',
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
