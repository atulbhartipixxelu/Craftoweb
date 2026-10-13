<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class VehicleType extends Model
{
    protected $fillable = [
        'slug',
        'label',
        'hint',
        'base_fare',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'base_fare' => 'float',
            'sort_order' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        $clear = fn () => Cache::forget('himcab.vehicle_types');

        static::saved($clear);
        static::deleted($clear);
    }

    public function isInUse(): bool
    {
        return Driver::query()->where('vehicle_type', $this->slug)->exists()
            || Ride::query()->where('vehicle_type', $this->slug)->exists();
    }
}
