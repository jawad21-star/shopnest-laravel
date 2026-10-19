<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\ChartWidget;

class SalesChart extends ChartWidget
{
    protected static ?int $sort = 2;

    protected ?string $heading = 'Sales — last 30 days';

    protected ?string $maxHeight = '280px';

    protected int|string|array $columnSpan = 'full';

    protected function getData(): array
    {
        $start = now()->subDays(29)->startOfDay();

        $orders = Order::where('status', '!=', 'cancelled')
            ->where('created_at', '>=', $start)
            ->get(['total', 'created_at'])
            ->groupBy(fn (Order $order) => $order->created_at->format('Y-m-d'));

        $days = collect(range(0, 29))->map(fn ($i) => $start->copy()->addDays($i));

        return [
            'datasets' => [
                [
                    'label' => 'Revenue',
                    'data' => $days->map(fn ($day) => round((float) ($orders->get($day->format('Y-m-d'))?->sum('total') ?? 0)))->all(),
                    'fill' => 'start',
                    'tension' => 0.35,
                ],
                [
                    'label' => 'Orders',
                    'data' => $days->map(fn ($day) => $orders->get($day->format('Y-m-d'))?->count() ?? 0)->all(),
                    'yAxisID' => 'y1',
                    'type' => 'bar',
                    'backgroundColor' => 'rgba(99, 102, 241, 0.35)',
                    'borderColor' => 'rgba(99, 102, 241, 0.35)',
                ],
            ],
            'labels' => $days->map(fn ($day) => $day->format('d M'))->all(),
        ];
    }

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'y' => ['beginAtZero' => true, 'position' => 'left'],
                'y1' => ['beginAtZero' => true, 'position' => 'right', 'grid' => ['drawOnChartArea' => false]],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
