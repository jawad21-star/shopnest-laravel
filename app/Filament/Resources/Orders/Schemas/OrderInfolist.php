<?php

namespace App\Filament\Resources\Orders\Schemas;

use App\Filament\Resources\Orders\Tables\OrdersTable;
use App\Models\Order;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class OrderInfolist
{
    public static function configure(Schema $schema): Schema
    {
        $money = fn ($state) => money($state);

        return $schema
            ->columns(3)
            ->components([
                Group::make()
                    ->columnSpan(2)
                    ->schema([
                        Section::make('Items')
                            ->schema([
                                RepeatableEntry::make('items')
                                    ->hiddenLabel()
                                    ->table([
                                        RepeatableEntry\TableColumn::make('Product'),
                                        RepeatableEntry\TableColumn::make('Price'),
                                        RepeatableEntry\TableColumn::make('Qty'),
                                        RepeatableEntry\TableColumn::make('Total'),
                                    ])
                                    ->schema([
                                        TextEntry::make('product_name'),
                                        TextEntry::make('price')->formatStateUsing($money),
                                        TextEntry::make('quantity'),
                                        TextEntry::make('total')->formatStateUsing($money)->weight('bold'),
                                    ]),
                            ]),
                        Section::make('Customer & shipping')
                            ->columns(2)
                            ->schema([
                                TextEntry::make('customer_name')->label('Name'),
                                TextEntry::make('customer_email')->label('Email')->copyable(),
                                TextEntry::make('customer_phone')->label('Phone')->copyable(),
                                TextEntry::make('user.name')->label('Account')->placeholder('Guest checkout'),
                                TextEntry::make('address')->columnSpanFull(),
                                TextEntry::make('city'),
                                TextEntry::make('postal_code')->placeholder('—'),
                                TextEntry::make('notes')->placeholder('No notes')->columnSpanFull(),
                            ]),
                    ]),
                Group::make()
                    ->columnSpan(1)
                    ->schema([
                        Section::make('Order')
                            ->schema([
                                TextEntry::make('order_number')->weight('bold')->copyable(),
                                TextEntry::make('created_at')->label('Placed on')->dateTime('d M Y, h:i A'),
                                TextEntry::make('status')
                                    ->badge()
                                    ->formatStateUsing(fn ($state) => Order::STATUSES[$state] ?? $state)
                                    ->color(fn ($state) => OrdersTable::statusColor($state)),
                                TextEntry::make('payment_method')
                                    ->formatStateUsing(fn ($state) => Order::PAYMENT_METHODS[$state] ?? $state),
                                TextEntry::make('payment_status')
                                    ->badge()
                                    ->formatStateUsing(fn ($state) => Order::PAYMENT_STATUSES[$state] ?? $state)
                                    ->color(fn ($state) => $state === 'paid' ? 'success' : 'gray'),
                            ]),
                        Section::make('Summary')
                            ->schema([
                                TextEntry::make('subtotal')->formatStateUsing($money)->inlineLabel(),
                                TextEntry::make('discount')->formatStateUsing(fn ($state) => '- '.money($state))->inlineLabel(),
                                TextEntry::make('coupon_code')->inlineLabel()->placeholder('—'),
                                TextEntry::make('shipping')->formatStateUsing($money)->inlineLabel(),
                                TextEntry::make('total')->formatStateUsing($money)->inlineLabel()->weight('bold')->size('lg'),
                            ]),
                    ]),
            ]);
    }
}
