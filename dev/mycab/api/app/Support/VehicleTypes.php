<?php

namespace App\Support;

use App\Models\VehicleType;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class VehicleTypes
{
    /**
     * @return array<string, array{label: string, base_fare: float, hint?: string|null}>
     */
    public static function all(bool $activeOnly = true): array
    {
        try {
            if (! Schema::hasTable('vehicle_types')) {
                return self::fromConfig();
            }
        } catch (\Throwable) {
            return self::fromConfig();
        }

        try {
            $types = Cache::remember('himcab.vehicle_types', 3600, function (): array {
                return VehicleType::query()
                    ->orderBy('sort_order')
                    ->orderBy('label')
                    ->get()
                    ->map(fn (VehicleType $type): array => [
                        'slug' => $type->slug,
                        'label' => $type->label,
                        'hint' => $type->hint,
                        'base_fare' => (float) $type->base_fare,
                        'is_active' => (bool) $type->is_active,
                    ])
                    ->all();
            });
        } catch (\Throwable) {
            return self::fromConfig();
        }

        if (! is_array($types) || $types === []) {
            Cache::forget('himcab.vehicle_types');

            return self::fromConfig();
        }

        $result = [];

        foreach ($types as $type) {
            if ($activeOnly && ! ($type['is_active'] ?? true)) {
                continue;
            }

            $result[$type['slug']] = [
                'label' => $type['label'],
                'hint' => $type['hint'],
                'base_fare' => (float) $type['base_fare'],
            ];
        }

        return $result === [] ? self::fromConfig() : $result;
    }

    /**
     * @return array<string, array{label: string, base_fare: float, hint?: string|null}>
     */
    private static function fromConfig(): array
    {
        return config('himcab.vehicle_types', []);
    }

    /**
     * @return list<string>
     */
    public static function ids(): array
    {
        return array_keys(self::all());
    }

    /**
     * @return array<string, string>
     */
    public static function labelOptions(): array
    {
        $options = [];

        foreach (self::all() as $slug => $meta) {
            $options[$slug] = $meta['label'];
        }

        return $options;
    }

    public static function validationRule(): string
    {
        $ids = self::ids();

        return $ids === [] ? 'string' : 'in:'.implode(',', $ids);
    }

    public static function baseFare(string $vehicleType): float
    {
        return (float) (self::all()[$vehicleType]['base_fare'] ?? 59);
    }

    /**
     * @return list<array{id: string, label: string, hint: string|null, base_fare: float}>
     */
    public static function forApi(): array
    {
        $data = [];

        foreach (self::all() as $slug => $meta) {
            $data[] = [
                'id' => $slug,
                'label' => $meta['label'],
                'hint' => $meta['hint'] ?? null,
                'base_fare' => (float) $meta['base_fare'],
            ];
        }

        return $data;
    }
}
