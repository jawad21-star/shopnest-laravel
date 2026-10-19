<?php

namespace App\Filament\Resources\Coupons\Tables;

use App\Models\Coupon;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class CouponsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('code')
                    ->searchable()
                    ->weight('bold')
                    ->copyable()
                    ->badge(),
                TextColumn::make('value')
                    ->label('Discount')
                    ->formatStateUsing(fn ($state, Coupon $record) => $record->type === 'percent' ? rtrim(rtrim($state, '0'), '.').'%' : money($state)),
                TextColumn::make('min_amount')
                    ->label('Min. order')
                    ->formatStateUsing(fn ($state) => money($state))
                    ->placeholder('—'),
                TextColumn::make('used_count')
                    ->label('Used')
                    ->formatStateUsing(fn ($state, Coupon $record) => $state.' / '.($record->max_uses ?? '∞')),
                TextColumn::make('expires_at')
                    ->dateTime('d M Y')
                    ->placeholder('Never')
                    ->color(fn (Coupon $record) => $record->expires_at?->isPast() ? 'danger' : null)
                    ->sortable(),
                ToggleColumn::make('is_active')
                    ->label('Active'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
