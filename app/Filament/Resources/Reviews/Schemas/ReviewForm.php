<?php

namespace App\Filament\Resources\Reviews\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ReviewForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->columns(2)
                    ->columnSpanFull()
                    ->schema([
                        Select::make('product_id')
                            ->relationship('product', 'name')
                            ->searchable()
                            ->required(),
                        Select::make('user_id')
                            ->label('Customer')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->required(),
                        Select::make('rating')
                            ->options([5 => '★★★★★', 4 => '★★★★', 3 => '★★★', 2 => '★★', 1 => '★'])
                            ->required(),
                        Toggle::make('is_approved')
                            ->label('Approved (shown on product page)'),
                        Textarea::make('comment')
                            ->rows(4)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
