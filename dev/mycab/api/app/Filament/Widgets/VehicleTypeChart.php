<?php

namespace App\Filament\Widgets;

use App\Models\Driver;
use App\Support\VehicleTypes;
use Filament\Widgets\ChartWidget;

class VehicleTypeChart extends ChartWidget
{
    protected static ?int $sort = 4;

    protected ?string $heading = 'Vehicles by type';

    protected ?string $description = 'Registered driver vehicles by type.';

    protected function getData(): array
    {
        $vehicleTypes = \App\Models\VehicleType::query()
            ->orderBy('sort_order')
            ->pluck('label', 'slug')
            ->all();

        if ($vehicleTypes === []) {
            $vehicleTypes = VehicleTypes::labelOptions();
        }

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
                        '#22c55e',
                        '#eab308',
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
