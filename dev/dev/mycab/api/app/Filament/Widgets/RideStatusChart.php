<?php

namespace App\Filament\Widgets;

use App\Models\Ride;
use Filament\Widgets\ChartWidget;

class RideStatusChart extends ChartWidget
{
    protected static ?int $sort = 3;

    protected ?string $heading = 'Rides by status';

    protected ?string $description = 'Pending, accepted, in-progress, completed and cancelled ride split.';

    protected function getData(): array
    {
        $statuses = [
            'pending' => 'Pending',
            'requested' => 'Requested',
            'accepted' => 'Accepted',
            'in_progress' => 'In progress',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
            'rejected' => 'Rejected',
        ];

        $ridesByStatus = Ride::query()
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return [
            'datasets' => [
                [
                    'label' => 'Rides',
                    'data' => array_map(
                        fn (string $status): int => (int) ($ridesByStatus[$status] ?? 0),
                        array_keys($statuses),
                    ),
                    'backgroundColor' => [
                        '#f59e0b',
                        '#6366f1',
                        '#3b82f6',
                        '#8b5cf6',
                        '#10b981',
                        '#ef4444',
                        '#6b7280',
                    ],
                ],
            ],
            'labels' => array_values($statuses),
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
