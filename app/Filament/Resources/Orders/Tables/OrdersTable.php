<?php

namespace App\Filament\Resources\Orders\Tables;

use App\Models\Order;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class OrdersTable
{
    public static function statusColor(?string $status): string
    {
        return match ($status) {
            'pending' => 'warning',
            'processing' => 'info',
            'shipped' => 'primary',
            'delivered' => 'success',
            'cancelled' => 'danger',
            default => 'gray',
        };
    }

    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('order_number')
                    ->label('Order')
                    ->searchable()
                    ->weight('bold')
                    ->copyable(),
                TextColumn::make('customer_name')
                    ->label('Customer')
                    ->searchable()
                    ->description(fn (Order $record) => $record->customer_phone),
                TextColumn::make('city')
                    ->toggleable(),
                TextColumn::make('items_count')
                    ->counts('items')
                    ->label('Items'),
                TextColumn::make('total')
                    ->formatStateUsing(fn ($state) => money($state))
                    ->sortable()
                    ->weight('medium'),
                TextColumn::make('payment_method')
                    ->label('Payment')
                    ->formatStateUsing(fn ($state) => Order::PAYMENT_METHODS[$state] ?? $state)
                    ->description(fn (Order $record) => Order::PAYMENT_STATUSES[$record->payment_status] ?? null)
                    ->toggleable(),
                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn ($state) => Order::STATUSES[$state] ?? $state)
                    ->color(fn ($state) => self::statusColor($state)),
                TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime('d M Y, h:i A')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')->options(Order::STATUSES)->multiple(),
                SelectFilter::make('payment_status')->options(Order::PAYMENT_STATUSES),
                Filter::make('created_at')
                    ->schema([
                        DatePicker::make('from'),
                        DatePicker::make('until'),
                    ])
                    ->query(fn (Builder $query, array $data) => $query
                        ->when($data['from'] ?? null, fn ($q, $date) => $q->whereDate('created_at', '>=', $date))
                        ->when($data['until'] ?? null, fn ($q, $date) => $q->whereDate('created_at', '<=', $date))),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('change_status')
                        ->label('Change status')
                        ->icon('heroicon-o-arrow-path')
                        ->schema([
                            Select::make('status')->options(Order::STATUSES)->required(),
                        ])
                        ->action(fn (Collection $records, array $data) => $records->each->update(['status' => $data['status']]))
                        ->deselectRecordsAfterCompletion(),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
