<?php

namespace App\Filament\Resources\Products\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        $currency = config('shop.currency');

        return $schema
            ->columns(3)
            ->components([
                Group::make()
                    ->columnSpan(2)
                    ->schema([
                        Section::make('Product information')
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
                                Textarea::make('short_description')
                                    ->rows(2)
                                    ->maxLength(500)
                                    ->columnSpanFull(),
                                RichEditor::make('description')
                                    ->columnSpanFull(),
                            ]),
                        Section::make('Images')
                            ->schema([
                                FileUpload::make('image')
                                    ->label('Main image')
                                    ->image()
                                    ->imageEditor()
                                    ->disk('public')
                                    ->directory('products'),
                                FileUpload::make('gallery')
                                    ->label('Gallery')
                                    ->image()
                                    ->multiple()
                                    ->reorderable()
                                    ->maxFiles(8)
                                    ->disk('public')
                                    ->directory('products/gallery'),
                            ]),
                    ]),
                Group::make()
                    ->columnSpan(1)
                    ->schema([
                        Section::make('Status')
                            ->schema([
                                Toggle::make('is_active')
                                    ->label('Visible in store')
                                    ->default(true),
                                Toggle::make('is_featured')
                                    ->label('Featured on homepage'),
                            ]),
                        Section::make('Pricing')
                            ->schema([
                                TextInput::make('price')
                                    ->required()
                                    ->numeric()
                                    ->minValue(0)
                                    ->prefix($currency),
                                TextInput::make('compare_price')
                                    ->label('Compare-at price')
                                    ->helperText('Original price, shown crossed out.')
                                    ->numeric()
                                    ->minValue(0)
                                    ->prefix($currency)
                                    ->gt('price'),
                            ]),
                        Section::make('Inventory')
                            ->schema([
                                TextInput::make('sku')
                                    ->label('SKU')
                                    ->maxLength(100)
                                    ->unique(ignoreRecord: true),
                                TextInput::make('stock')
                                    ->required()
                                    ->numeric()
                                    ->minValue(0)
                                    ->default(0),
                            ]),
                        Section::make('Organisation')
                            ->schema([
                                Select::make('category_id')
                                    ->relationship('category', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required(),
                                Select::make('brand_id')
                                    ->relationship('brand', 'name')
                                    ->searchable()
                                    ->preload(),
                            ]),
                    ]),
            ]);
    }
}
