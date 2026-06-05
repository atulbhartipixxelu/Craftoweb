<?php

namespace App\Filament\Widgets;

use App\Models\Driver;
use Filament\Widgets\ChartWidget;

class VehicleTypeChart extends ChartWidget
{
    protected static ?int $sort = 4;

    protected ?string $heading = 'Vehicles by type';

    protected ?string $description = 'Registered driver vehicles grouped by Mini, Sedan and SUV.';

    protected function getData(): array
    {
        $vehicleTypes = [
            'mini' => 'Mini',
            'sedan' => 'Sedan',
            'suv' => 'SUV',
        ];

        $vehiclesByType = Driver::query()
            ->selectRaw('vehicle_type, COUNT(*) as total')
            ->groupBy('vehicle_type')
            ->pluck('total', 'vehicle_type');

        return [
            'datasets' => [
                [
                    'label' => 'Vehicles',
                    'data' => array_map(
                        fn (string $type): int => (int) ($vehiclesByType[$type] ?? 0),
                        array_keys($vehicleTypes),
                    ),
                    'backgroundColor' => [
                        '#06b6d4',
                        '#6366f1',
                        '#f97316',
                    ],
                ],
            ],
            'labels' => array_values($vehicleTypes),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
