<?php

namespace App\Filament\Resources\Users\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->description(fn ($record) => $record->email),
                TextColumn::make('phone')
                    ->searchable()
                    ->placeholder('—'),
                TextColumn::make('city')
                    ->placeholder('—')
                    ->toggleable(),
                TextColumn::make('orders_count')
                    ->counts('orders')
                    ->label('Orders')
                    ->badge()
                    ->sortable(),
                TextColumn::make('orders_sum_total')
                    ->sum('orders', 'total')
                    ->label('Total spent')
                    ->formatStateUsing(fn ($state) => money($state))
                    ->sortable(),
                IconColumn::make('is_admin')
                    ->label('Admin')
                    ->boolean(),
                TextColumn::make('created_at')
                    ->label('Joined')
                    ->date('d M Y')
                    ->sortable(),
            ])
            ->filters([
                TernaryFilter::make('is_admin')->label('Admin'),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
