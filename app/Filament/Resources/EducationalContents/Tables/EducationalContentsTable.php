<?php

namespace App\Filament\Resources\EducationalContents\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class EducationalContentsTable
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

                TextColumn::make('type')
                    ->label('Type')
                    ->badge()
                    ->formatStateUsing(fn($state) => match($state) {
                        'article'     => 'Article',
                        'video'       => 'Vidéo',
                        'podcast'     => 'Podcast',
                        'infographic' => 'Infographie',
                        default       => $state,
                    }),

                TextColumn::make('category')
                    ->label('Catégorie')
                    ->searchable(),

                TextColumn::make('author.name')
                    ->label('Auteur')
                    ->sortable(),

                TextColumn::make('views_count')
                    ->label('Vues')
                    ->numeric()
                    ->sortable(),

                IconColumn::make('is_published')
                    ->label('Publié')
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
                        'article'     => 'Article',
                        'video'       => 'Vidéo',
                        'podcast'     => 'Podcast',
                        'infographic' => 'Infographie',
                    ]),

                TernaryFilter::make('is_published')
                    ->label('Publié'),
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
