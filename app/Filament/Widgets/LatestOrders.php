<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Orders\OrderResource;
use App\Filament\Resources\Orders\Tables\OrdersTable;
use App\Models\Order;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class LatestOrders extends TableWidget
{
    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 'full';

    protected static ?string $heading = 'Latest orders';

    public function table(Table $table): Table
    {
        return $table
            ->query(Order::query()->latest()->limit(8))
            ->paginated(false)
            ->columns([
                TextColumn::make('order_number')->label('Order')->weight('bold'),
                TextColumn::make('customer_name')->label('Customer'),
                TextColumn::make('total')->formatStateUsing(fn ($state) => money($state)),
                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn ($state) => Order::STATUSES[$state] ?? $state)
                    ->color(fn ($state) => OrdersTable::statusColor($state)),
                TextColumn::make('created_at')->label('Date')->since(),
            ])
            ->recordActions([
                Action::make('open')
                    ->label('View')
                    ->icon('heroicon-o-eye')
                    ->url(fn (Order $record) => OrderResource::getUrl('view', ['record' => $record])),
            ]);
    }
}
