<?php

namespace Database\Seeders;

use App\Models\VehicleType;
use Illuminate\Database\Seeder;

class VehicleTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = config('himcab.vehicle_types', []);
        $order = 0;

        foreach ($types as $slug => $meta) {
            VehicleType::query()->updateOrCreate(
                ['slug' => $slug],
                [
                    'label' => $meta['label'],
                    'hint' => $meta['hint'] ?? null,
                    'base_fare' => $meta['base_fare'],
                    'sort_order' => $order,
                    'is_active' => true,
                ],
            );

            $order++;
        }
    }
}
