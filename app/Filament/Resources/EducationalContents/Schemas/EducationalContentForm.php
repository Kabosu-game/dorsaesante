<?php

namespace App\Filament\Resources\EducationalContents\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class EducationalContentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Contenu')
                    ->columns(2)
                    ->schema([
                        TextInput::make('title')
                            ->label('Titre')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),

                        Textarea::make('content')
                            ->label('Contenu')
                            ->required()
                            ->rows(6)
                            ->columnSpanFull(),

                        Select::make('type')
                            ->label('Type')
                            ->options([
                                'article' => 'Article',
                                'video'   => 'Vidéo',
                                'podcast' => 'Podcast',
                                'infographic' => 'Infographie',
                            ])
                            ->required(),

                        TextInput::make('category')
                            ->label('Catégorie')
                            ->required()
                            ->maxLength(100),

                        Select::make('author_id')
                            ->label('Auteur')
                            ->relationship('author', 'name')
                            ->searchable()
                            ->preload(),

                        TextInput::make('tags')
                            ->label('Tags (séparés par des virgules)')
                            ->maxLength(255),

                        TextInput::make('media_url')
                            ->label('URL média')
                            ->url()
                            ->maxLength(500)
                            ->columnSpanFull(),

                        Toggle::make('is_published')
                            ->label('Publié')
                            ->default(false),

                        FileUpload::make('thumbnail')
                            ->label('Miniature')
                            ->image()
                            ->directory('educational-content')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
