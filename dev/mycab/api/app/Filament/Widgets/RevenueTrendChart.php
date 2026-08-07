<?php

namespace App\Filament\Widgets;

use App\Models\Ride;
use Carbon\CarbonImmutable;
use Filament\Widgets\ChartWidget;

class RevenueTrendChart extends ChartWidget
{
    protected static ?int $sort = 2;

    protected ?string $heading = 'Confirmed revenue trend';

    protected ?string $description = 'Daily fare from accepted, in-progress and completed rides (cash recorded on completion).';

    protected int | string | array $columnSpan = 'full';

    protected function getData(): array
    {
        $dates = $this->lastSevenDateLabels();

        $revenueByDate = Ride::query()
            ->whereIn('status', ['accepted', 'in_progress', 'completed'])
            ->where('created_at', '>=', now()->subDays(6)->startOfDay())
            ->selectRaw('DATE(created_at) as ride_date, COALESCE(SUM(CASE WHEN status = ? THEN COALESCE(fare_paid, fare_estimate) ELSE fare_estimate END), 0) as total', ['completed'])
            ->groupBy('ride_date')
            ->pluck('total', 'ride_date');

        return [
            'datasets' => [
                [
                    'label' => 'Confirmed revenue',
                    'data' => array_map(
                        fn (string $date): float => (float) ($revenueByDate[$date] ?? 0),
                        $dates,
                    ),
                    'borderColor' => '#10b981',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.18)',
                    'fill' => true,
                    'tension' => 0.35,
                ],
            ],
            'labels' => array_map(
                fn (string $date): string => CarbonImmutable::parse($date)->format('M d'),
                $dates,
            ),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    /**
     * @return array<int, string>
     */
    private function lastSevenDateLabels(): array
    {
        return array_map(
            fn (int $daysAgo): string => now()->subDays($daysAgo)->format('Y-m-d'),
            range(6, 0),
        );
    }
}
