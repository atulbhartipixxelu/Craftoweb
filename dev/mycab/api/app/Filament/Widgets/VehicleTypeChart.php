<?php

namespace App\Filament\Widgets;

use App\Models\Driver;
use App\Models\VehicleType;
use App\Support\VehicleTypes;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Schema;
use Throwable;

class VehicleTypeChart extends ChartWidget
{
    protected static ?int $sort = 4;

    protected ?string $heading = 'Vehicles by type';

    protected ?string $description = 'Registered driver vehicles by type.';

    protected function getData(): array
    {
        $vehicleTypes = $this->resolveVehicleTypeLabels();

        $vehiclesByType = Driver::query()
            ->selectRaw('vehicle_type, COUNT(*) as total')
            ->groupBy('vehicle_type')
            ->pluck('total', 'vehicle_type');

        $slugs = array_keys($vehicleTypes);
        $palette = ['#06b6d4', '#6366f1', '#f97316', '#22c55e', '#eab308', '#a855f7', '#ef4444'];

        return [
            'datasets' => [
                [
                    'label' => 'Vehicles',
                    'data' => array_map(
                        fn (string $slug): int => (int) ($vehiclesByType[$slug] ?? 0),
                        $slugs,
                    ),
                    'backgroundColor' => array_map(
                        fn (int $index): string => $palette[$index % count($palette)],
                        array_keys($slugs),
                    ),
                ],
            ],
            'labels' => array_values($vehicleTypes),
        ];
    }

    /**
     * @return array<string, string>
     */
    private function resolveVehicleTypeLabels(): array
    {
        try {
            if (Schema::hasTable('vehicle_types')) {
                $fromDb = VehicleType::query()
                    ->orderBy('sort_order')
                    ->pluck('label', 'slug')
                    ->all();

                if ($fromDb !== []) {
                    return $fromDb;
                }
            }
        } catch (Throwable) {
            // Fall through to config / helper.
        }

        return VehicleTypes::labelOptions();
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
