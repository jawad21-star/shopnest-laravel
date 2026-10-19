<?php

namespace App\Filament\Widgets;

use App\Models\Product;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class TopProducts extends TableWidget
{
    protected static ?int $sort = 4;

    protected int|string|array $columnSpan = 'full';

    protected static ?string $heading = 'Best sellers';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Product::query()
                    ->withSum('orderItems as units_sold', 'quantity')
                    ->withSum('orderItems as revenue', 'total')
                    ->orderByDesc('units_sold')
                    ->limit(5)
            )
            ->paginated(false)
            ->columns([
                ImageColumn::make('image')->disk('public')->square()->size(40),
                TextColumn::make('name')->weight('medium'),
                TextColumn::make('units_sold')->label('Units sold')->default(0)->badge(),
                TextColumn::make('revenue')->formatStateUsing(fn ($state) => money($state))->default(0),
                TextColumn::make('stock')->badge()->color(fn (int $state) => $state < 5 ? 'danger' : 'success'),
            ]);
    }
}
