<?php

namespace App\Filament\Resources\Coupons\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class CouponForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->columns(2)
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('code')
                            ->required()
                            ->maxLength(50)
                            ->unique(ignoreRecord: true)
                            ->dehydrateStateUsing(fn (?string $state) => strtoupper(trim((string) $state))),
                        Select::make('type')
                            ->options(['percent' => 'Percentage (%)', 'fixed' => 'Fixed amount'])
                            ->default('percent')
                            ->required()
                            ->live()
                            ->native(false),
                        TextInput::make('value')
                            ->required()
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(fn (Get $get) => $get('type') === 'percent' ? 100 : null)
                            ->suffix(fn (Get $get) => $get('type') === 'percent' ? '%' : config('shop.currency')),
                        TextInput::make('min_amount')
                            ->label('Minimum order amount')
                            ->numeric()
                            ->prefix(config('shop.currency')),
                        TextInput::make('max_uses')
                            ->label('Usage limit')
                            ->numeric()
                            ->helperText('Leave empty for unlimited.'),
                        DateTimePicker::make('expires_at')
                            ->label('Expires at'),
                        TextInput::make('used_count')
                            ->label('Times used')
                            ->numeric()
                            ->default(0)
                            ->disabled()
                            ->dehydrated(false)
                            ->visibleOn('edit'),
                        Toggle::make('is_active')
                            ->label('Active')
                            ->default(true),
                    ]),
            ]);
    }
}
