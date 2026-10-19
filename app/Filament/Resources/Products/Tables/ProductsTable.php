<?php

namespace App\Filament\Resources\Products\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class ProductsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                ImageColumn::make('image')
                    ->disk('public')
                    ->square()
                    ->size(48),
                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->description(fn ($record) => $record->sku),
                TextColumn::make('category.name')
                    ->badge()
                    ->color('gray')
                    ->sortable(),
                TextColumn::make('brand.name')
                    ->toggleable(),
                TextColumn::make('price')
                    ->formatStateUsing(fn ($state) => money($state))
                    ->sortable(),
                TextColumn::make('stock')
                    ->sortable()
                    ->badge()
                    ->color(fn (int $state) => match (true) {
                        $state === 0 => 'danger',
                        $state < 5 => 'warning',
                        default => 'success',
                    }),
                IconColumn::make('is_featured')
                    ->label('Featured')
                    ->boolean()
                    ->trueIcon('heroicon-s-star')
                    ->falseIcon('heroicon-o-star')
                    ->trueColor('warning'),
                ToggleColumn::make('is_active')
                    ->label('Active'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('category')
                    ->relationship('category', 'name')
                    ->preload(),
                SelectFilter::make('brand')
                    ->relationship('brand', 'name')
                    ->preload(),
                TernaryFilter::make('is_active')->label('Active'),
                TernaryFilter::make('is_featured')->label('Featured'),
                Filter::make('low_stock')
                    ->label('Low stock (< 5)')
                    ->query(fn (Builder $query) => $query->where('stock', '<', 5)),
            ])
            ->recordActions([
                ActionGroup::make([
                    Action::make('view_in_store')
                        ->label('View in store')
                        ->icon('heroicon-o-arrow-top-right-on-square')
                        ->url(fn ($record) => route('products.show', $record))
                        ->openUrlInNewTab(),
                    EditAction::make(),
                    DeleteAction::make(),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('activate')
                        ->label('Mark active')
                        ->icon('heroicon-o-eye')
                        ->action(fn (Collection $records) => $records->each->update(['is_active' => true]))
                        ->deselectRecordsAfterCompletion(),
                    BulkAction::make('deactivate')
                        ->label('Mark hidden')
                        ->icon('heroicon-o-eye-slash')
                        ->action(fn (Collection $records) => $records->each->update(['is_active' => false]))
                        ->deselectRecordsAfterCompletion(),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
