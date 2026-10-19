<?php

namespace App\Filament\Resources\Orders\Schemas;

use App\Models\Order;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class OrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(3)
            ->components([
                Section::make('Order status')
                    ->columnSpan(1)
                    ->schema([
                        Select::make('status')
                            ->options(Order::STATUSES)
                            ->required()
                            ->native(false),
                        Select::make('payment_status')
                            ->options(Order::PAYMENT_STATUSES)
                            ->required()
                            ->native(false),
                        Select::make('payment_method')
                            ->options(Order::PAYMENT_METHODS)
                            ->required()
                            ->native(false),
                    ]),
                Section::make('Customer & shipping')
                    ->columnSpan(2)
                    ->columns(2)
                    ->schema([
                        TextInput::make('customer_name')->required(),
                        TextInput::make('customer_email')->email()->required(),
                        TextInput::make('customer_phone')->tel()->required(),
                        TextInput::make('city')->required(),
                        TextInput::make('address')->required()->columnSpanFull(),
                        TextInput::make('postal_code'),
                        Textarea::make('notes')->columnSpanFull(),
                    ]),
            ]);
    }
}
