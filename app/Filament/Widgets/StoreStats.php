<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StoreStats extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected ?string $pollingInterval = null;

    protected function getStats(): array
    {
        $paidOrders = Order::where('status', '!=', 'cancelled');

        $revenueThisMonth = (clone $paidOrders)->where('created_at', '>=', now()->startOfMonth())->sum('total');
        $revenueLastMonth = (clone $paidOrders)
            ->whereBetween('created_at', [now()->subMonthNoOverflow()->startOfMonth(), now()->subMonthNoOverflow()->endOfMonth()])
            ->sum('total');
        $change = $revenueLastMonth > 0 ? round(($revenueThisMonth - $revenueLastMonth) / $revenueLastMonth * 100) : null;

        $dailyRevenue = collect(range(6, 0))
            ->map(fn ($daysAgo) => (float) (clone $paidOrders)->whereDate('created_at', now()->subDays($daysAgo))->sum('total'))
            ->all();

        return [
            Stat::make('Revenue (this month)', money($revenueThisMonth))
                ->description($change === null ? 'No sales last month' : abs($change).'% '.($change >= 0 ? 'increase' : 'decrease').' vs last month')
                ->descriptionIcon($change !== null && $change < 0 ? 'heroicon-m-arrow-trending-down' : 'heroicon-m-arrow-trending-up')
                ->color($change !== null && $change < 0 ? 'danger' : 'success')
                ->chart($dailyRevenue),
            Stat::make('Orders', Order::count())
                ->description(Order::where('status', 'pending')->count().' pending')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),
            Stat::make('Customers', User::where('is_admin', false)->count())
                ->description(User::where('is_admin', false)->where('created_at', '>=', now()->subDays(30))->count().' new in 30 days')
                ->descriptionIcon('heroicon-m-user-plus')
                ->color('info'),
            Stat::make('Products', Product::active()->count())
                ->description(Product::where('stock', '<', 5)->count().' low on stock')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color('danger'),
        ];
    }
}
