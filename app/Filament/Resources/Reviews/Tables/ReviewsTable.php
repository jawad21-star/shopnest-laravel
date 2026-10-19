<?php

namespace App\Filament\Resources\Reviews\Tables;

use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

class ReviewsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('product.name')
                    ->searchable()
                    ->weight('medium')
                    ->limit(30),
                TextColumn::make('user.name')
                    ->label('Customer')
                    ->searchable(),
                TextColumn::make('rating')
                    ->formatStateUsing(fn (int $state) => str_repeat('★', $state).str_repeat('☆', 5 - $state))
                    ->color('warning')
                    ->sortable(),
                TextColumn::make('comment')
                    ->limit(50)
                    ->wrap(),
                ToggleColumn::make('is_approved')
                    ->label('Approved'),
                TextColumn::make('created_at')
                    ->since()
                    ->sortable(),
            ])
            ->filters([
                TernaryFilter::make('is_approved')->label('Approved'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('approve')
                        ->label('Approve selected')
                        ->icon('heroicon-o-check')
                        ->action(fn (Collection $records) => $records->each->update(['is_approved' => true]))
                        ->deselectRecordsAfterCompletion(),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
