<?php

namespace App\Filament\Resources\Categories\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class CategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(3)
            ->components([
                Section::make('Category details')
                    ->columnSpan(2)
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (Set $set, ?string $state, string $operation) => $operation === 'create' ? $set('slug', Str::slug($state)) : null),
                        TextInput::make('slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        Select::make('parent_id')
                            ->label('Parent category')
                            ->relationship('parent', 'name')
                            ->searchable()
                            ->preload()
                            ->placeholder('None (top level)'),
                        TextInput::make('sort_order')
                            ->numeric()
                            ->default(0),
                        Textarea::make('description')
                            ->rows(4)
                            ->columnSpanFull(),
                    ]),
                Section::make('Display')
                    ->columnSpan(1)
                    ->schema([
                        FileUpload::make('image')
                            ->image()
                            ->imageEditor()
                            ->disk('public')
                            ->directory('categories'),
                        Toggle::make('is_active')
                            ->label('Visible in store')
                            ->default(true),
                    ]),
            ]);
    }
}
