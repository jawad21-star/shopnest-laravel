<?php

namespace App\Filament\Resources\Orders\Pages;

use App\Filament\Resources\Orders\OrderResource;
use App\Models\Order;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewOrder extends ViewRecord
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        $statusActions = collect(Order::STATUSES)
            ->map(fn (string $label, string $status) => Action::make('mark_'.$status)
                ->label('Mark as '.$label)
                ->hidden(fn (Order $record) => $record->status === $status)
                ->requiresConfirmation($status === 'cancelled')
                ->color($status === 'cancelled' ? 'danger' : null)
                ->action(function (Order $record) use ($status, $label) {
                    $record->update(['status' => $status]);
                    Notification::make()->title("Order marked as {$label}")->success()->send();
                }))
            ->values()
            ->all();

        return [
            Action::make('mark_paid')
                ->label('Mark paid')
                ->icon('heroicon-o-banknotes')
                ->color('success')
                ->hidden(fn (Order $record) => $record->payment_status === 'paid')
                ->action(function (Order $record) {
                    $record->update(['payment_status' => 'paid']);
                    Notification::make()->title('Payment marked as received')->success()->send();
                }),
            ActionGroup::make($statusActions)
                ->label('Update status')
                ->icon('heroicon-o-truck')
                ->button(),
            EditAction::make(),
        ];
    }
}
